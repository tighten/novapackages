<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ApiDetailsController extends Controller
{
    public function __invoke(): View
    {
        return view('app.api_details');
    }
}
