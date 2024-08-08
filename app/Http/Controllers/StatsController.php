<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Stats;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function __invoke(Stats $stats): View
    {
        return view('stats', ['stats' => $stats]);
    }
}
