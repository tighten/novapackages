<?php

namespace App\Http\Controllers;

use App\Stats;

class StatsController extends Controller
{
    public function __invoke(Stats $stats)
    {
        return view('stats', ['stats' => $stats]);
    }
}
