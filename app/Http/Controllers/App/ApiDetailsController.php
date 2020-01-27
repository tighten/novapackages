<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiDetailsController extends Controller
{
    public function __invoke()
    {
        return view('app.api_details');
    }
}
