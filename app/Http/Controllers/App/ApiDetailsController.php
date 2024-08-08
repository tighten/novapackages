<?php

namespace App\Http\Controllers\App;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiDetailsController extends Controller
{
    public function __invoke(): View
    {
        return view('app.api_details');
    }
}
