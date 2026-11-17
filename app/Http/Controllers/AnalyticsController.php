<?php

namespace App\Http\Controllers;

use App\Support\PortfolioAnalytics;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __invoke(Request $request, PortfolioAnalytics $analytics)
    {
        $rangeOptions = [7, 30, 90];
        $requested = filter_var($request->query('range', 30), FILTER_VALIDATE_INT);
        $days = in_array($requested, $rangeOptions, true) ? $requested : 30;

        return view('admin.analytics', $analytics->dashboard($days));
    }
}
