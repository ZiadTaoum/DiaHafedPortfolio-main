<?php

namespace App\Support;

use App\Models\AnalyticsVisit;
use App\Models\ContactMessage;
use App\Models\PortfolioItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PortfolioAnalytics
{
    private const RANGE_OPTIONS = [7, 30, 90];

    public function record(Request $request): void
    {
        if ($request->user()?->is_admin) {
            return;
        }

        $userAgent = (string) $request->userAgent();
        if ($userAgent === '' || preg_match('/bot|crawler|spider|slurp|preview|headless|uptime|monitoring/i', $userAgent)) {
            return;
        }

        $sessionId = $request->session()->getId();
        if ($sessionId === '') {
            return;
        }

        $path = '/'.ltrim($request->path(), '/');
        $referrer = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);
        $source = $this->sourceName(is_string($referrer) ? $referrer : '', $request->getHost());

        AnalyticsVisit::create([
            'session_hash' => hash_hmac('sha256', $sessionId, (string) config('app.key')),
            'path' => mb_substr($path, 0, 255),
            'source' => $source,
            'device' => $this->device($userAgent),
            'browser' => $this->browser($userAgent),
            'visited_at' => now(),
        ]);
    }

    public function dashboard(int $days): array
    {
        abort_unless(in_array($days, self::RANGE_OPTIONS, true), 404);

        $end = now()->endOfDay();
        $start = now()->startOfDay()->subDays($days - 1);
        $visits = AnalyticsVisit::query()->whereBetween('visited_at', [$start, $end]);
        $pageViews = (clone $visits)->count();
        $uniqueVisitors = (clone $visits)->distinct()->count('session_hash');

        $sessions = (clone $visits)->select('session_hash')->selectRaw('COUNT(*) as page_views')->groupBy('session_hash')->get();
        $sessionCount = $sessions->count();
        $singlePageSessions = $sessions->where('page_views', 1)->count();
        $bounceRate = $sessionCount ? $singlePageSessions / $sessionCount * 100 : 0;

        $daily = (clone $visits)
            ->selectRaw('DATE(visited_at) as day, COUNT(*) as page_views, COUNT(DISTINCT session_hash) as visitors')
            ->groupBy('day')
            ->get()
            ->keyBy('day');
        $chart = collect(range(0, $days - 1))->map(function (int $offset) use ($start, $daily) {
            $date = $start->copy()->addDays($offset);
            $stats = $daily->get($date->toDateString());
            return [
                'label' => $date->format('M j'),
                'visitors' => (int) ($stats->visitors ?? 0),
                'pageViews' => (int) ($stats->page_views ?? 0),
            ];
        });

        $topPages = $this->topPages($visits);
        $sources = $this->breakdown($visits, 'source', 'source');
        $devices = $this->breakdown($visits, 'device', 'device');
        $browsers = $this->breakdown($visits, 'browser', 'browser');
        $contactCount = ContactMessage::query()->whereBetween('created_at', [$start, $end])->count();

        return [
            'rangeOptions' => self::RANGE_OPTIONS,
            'days' => $days,
            'uniqueVisitors' => $uniqueVisitors,
            'pageViews' => $pageViews,
            'singlePageSessions' => $singlePageSessions,
            'bounceRate' => $bounceRate,
            'liveVisitors' => AnalyticsVisit::query()->where('visited_at', '>=', now()->subMinutes(5))->distinct()->count('session_hash'),
            'contactCount' => $contactCount,
            'conversionRate' => $uniqueVisitors ? $contactCount / $uniqueVisitors * 100 : 0,
            'chart' => $chart,
            'maxChartViews' => max(1, (int) $chart->max('pageViews')),
            'topPages' => $topPages,
            'maxPageViews' => max(1, (int) ($topPages->max('views') ?? 0)),
            'sources' => $sources,
            'maxSourceViews' => max(1, (int) ($sources->max('views') ?? 0)),
            'devices' => $devices,
            'browsers' => $browsers,
        ];
    }

    private function sourceName(string $referrerHost, string $requestHost): string
    {
        $normalize = static fn (string $host): string => strtolower(preg_replace('/^www\./i', '', rtrim($host, '.')));
        $referrerHost = $normalize($referrerHost);
        if ($referrerHost === '' || $referrerHost === $normalize($requestHost)) {
            return 'Direct / internal';
        }

        return mb_substr($referrerHost, 0, 190);
    }

    private function device(string $userAgent): string
    {
        return match (true) {
            preg_match('/ipad|tablet|kindle|silk/i', $userAgent) === 1 => 'Tablet',
            preg_match('/mobile|iphone|ipod|android/i', $userAgent) === 1 => 'Mobile',
            default => 'Desktop',
        };
    }

    private function browser(string $userAgent): string
    {
        return match (true) {
            preg_match('/edg\//i', $userAgent) === 1 => 'Edge',
            preg_match('/opr\//i', $userAgent) === 1 => 'Opera',
            preg_match('/firefox\//i', $userAgent) === 1 => 'Firefox',
            preg_match('/chrome\//i', $userAgent) === 1 => 'Chrome',
            preg_match('/safari\//i', $userAgent) === 1 => 'Safari',
            default => 'Other',
        };
    }

    private function topPages($visits): Collection
    {
        $pages = (clone $visits)
            ->select('path')
            ->selectRaw('COUNT(*) as views')
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        $projectIds = $pages->map(fn ($page) => preg_match('~^/projects/(\d+)$~', $page->path, $match) ? (int) $match[1] : null)->filter()->unique();
        $projectTitles = $projectIds->isNotEmpty()
            ? PortfolioItem::query()->whereIn('id', $projectIds)->pluck('title', 'id')
            : collect();

        return $pages->map(function ($page) use ($projectTitles) {
            if ($page->path === '/') {
                $page->label = 'Homepage';
            } elseif (preg_match('~^/projects/(\d+)$~', $page->path, $match)) {
                $page->label = $projectTitles->get((int) $match[1], 'Project #'.$match[1]);
            } else {
                $page->label = $page->path;
            }
            return $page;
        });
    }

    private function breakdown($visits, string $column, string $label): Collection
    {
        return (clone $visits)
            ->select($column)
            ->selectRaw('COUNT(*) as views')
            ->groupBy($column)
            ->orderByDesc('views')
            ->limit(8)
            ->get()
            ->map(function ($row) use ($column, $label) {
                $row->{$label} = $row->{$column};
                return $row;
            });
    }
}
