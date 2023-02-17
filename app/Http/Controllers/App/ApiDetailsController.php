<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;

class ApiDetailsController extends Controller
{
    public function __invoke()
    {
        return view('app.api_details');
    }
}
