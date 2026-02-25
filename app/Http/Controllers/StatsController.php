<?php

namespace App\Http\Controllers;

use App\Stats;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function __invoke(Stats $stats): View
    {
        return view('stats', ['stats' => $stats]);
    }
}
