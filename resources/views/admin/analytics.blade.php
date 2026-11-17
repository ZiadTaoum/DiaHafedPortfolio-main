@extends('admin.layout')
@section('title', 'Analytics')
@section('content')
<a class="back" href="{{ route('admin.index') }}">← Dashboard</a>
<div class="page-title analytics-title">
    <div><p class="eyebrow">YOUR PORTFOLIO, IN NUMBERS</p><h1>Analytics.</h1><p>Understand visits, popular work, and where people find you.</p></div>
    <nav class="analytics-range" aria-label="Analytics date range">
        @foreach($rangeOptions as $option)
            <a href="{{ route('admin.analytics', ['range' => $option]) }}" @class(['active' => $days === $option]) @if($days === $option) aria-current="page" @endif>{{ $option }} days</a>
        @endforeach
    </nav>
</div>
<div class="analytics-stats">
    <article class="analytics-stat"><span>Unique visitors</span><strong>{{ number_format($uniqueVisitors) }}</strong><small>Estimated unique browsing sessions</small></article>
    <article class="analytics-stat"><span>Page views</span><strong>{{ number_format($pageViews) }}</strong><small>Homepage and project pages</small></article>
    <article class="analytics-stat"><span>Pages per visitor</span><strong>{{ number_format($uniqueVisitors ? $pageViews / $uniqueVisitors : 0, 1) }}</strong><small>Average pages in selected period</small></article>
    <article class="analytics-stat"><span>Bounce rate</span><strong>{{ number_format($bounceRate, 1) }}%</strong><small>{{ number_format($singlePageSessions) }} single-page sessions</small></article>
    <article class="analytics-stat analytics-live"><span><i aria-hidden="true"></i> Visitors now</span><strong>{{ number_format($liveVisitors) }}</strong><small>Active in the last five minutes</small></article>
    <article class="analytics-stat"><span>Inquiries received</span><strong>{{ number_format($contactCount) }}</strong><small>Contact form submissions</small></article>
    <article class="analytics-stat"><span>Inquiry rate</span><strong>{{ number_format($conversionRate, 1) }}%</strong><small>Inquiries per unique visitor</small></article>
</div>
<section class="panel analytics-panel">
    <div class="panel-heading"><div><h2>Traffic over time</h2><span>Daily visitors and page views</span></div><div class="analytics-legend"><span><i class="legend-views"></i>Page views</span><span><i class="legend-visitors"></i>Visitors</span></div></div>
    @if($pageViews)
        <div class="analytics-chart" style="grid-template-columns:repeat({{ $days }},minmax(0,1fr))" role="img" aria-label="Daily visitors and page views for the last {{ $days }} days">
            @foreach($chart as $day)
                <div class="analytics-day" title="{{ $day['label'] }}: {{ $day['visitors'] }} visitors, {{ $day['pageViews'] }} page views">
                    <div class="analytics-bars"><span class="bar-views" style="height:{{ max($day['pageViews'] ? 3 : 0, ($day['pageViews'] / $maxChartViews) * 100) }}%"></span><span class="bar-visitors" style="height:{{ max($day['visitors'] ? 3 : 0, ($day['visitors'] / $maxChartViews) * 100) }}%"></span></div>
                    @if($days === 7 || $loop->first || $loop->last || $loop->iteration % 7 === 0)<small>{{ $day['label'] }}</small>@endif
                </div>
            @endforeach
        </div>
    @else
        <div class="analytics-empty">No visits yet in this period. Once visitors browse your portfolio, trends will appear here.</div>
    @endif
</section>
<div class="analytics-grid">
    <section class="panel analytics-panel">
        <div class="panel-heading"><div><h2>Most viewed pages</h2><span>Your most popular destinations</span></div></div>
        @forelse($topPages as $page)
            <div class="analytics-row"><div class="analytics-row-label"><strong>{{ $page->label }}</strong><span>{{ number_format($page->views) }} views</span></div><div class="analytics-track"><i style="width:{{ round(($page->views / $maxPageViews) * 100) }}%"></i></div></div>
        @empty <div class="analytics-empty">No page views in this period.</div> @endforelse
    </section>
    <section class="panel analytics-panel">
        <div class="panel-heading"><div><h2>Traffic sources</h2><span>External referrers; otherwise unreported or internal</span></div></div>
        @forelse($sources as $source)
            <div class="analytics-row"><div class="analytics-row-label"><strong>{{ $source->source }}</strong><span>{{ number_format($source->views) }} visits</span></div><div class="analytics-track"><i style="width:{{ round(($source->views / $maxSourceViews) * 100) }}%"></i></div></div>
        @empty <div class="analytics-empty">No referral data in this period.</div> @endforelse
    </section>
    <section class="panel analytics-panel">
        <div class="panel-heading"><div><h2>Devices</h2><span>How visitors view your portfolio</span></div></div>
        @forelse($devices as $device)
            <div class="analytics-breakdown"><strong>{{ $device->device }}</strong><span>{{ number_format($device->views) }} page views</span></div>
        @empty <div class="analytics-empty">Device data will appear when visits are recorded.</div> @endforelse
    </section>
    <section class="panel analytics-panel">
        <div class="panel-heading"><div><h2>Browsers</h2><span>Browser family by page views</span></div></div>
        @forelse($browsers as $browser)
            <div class="analytics-breakdown"><strong>{{ $browser->browser }}</strong><span>{{ number_format($browser->views) }} page views</span></div>
        @empty <div class="analytics-empty">Browser data will appear when visits are recorded.</div> @endforelse
    </section>
</div>
<p class="analytics-privacy">Privacy-friendly measurement: analytics records page paths, referring site names, device and browser categories, and a one-way session identifier. It does not store IP addresses, full browser fingerprints, or URL query parameters. Administrator visits and obvious bots are excluded.</p>
@endsection
