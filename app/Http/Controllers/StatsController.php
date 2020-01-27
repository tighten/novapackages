<?php

namespace App\Http\Controllers;

use App\Stats;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function __invoke(Stats $stats)
    {
        return view('stats', ['stats' => $stats]);
    }
}
