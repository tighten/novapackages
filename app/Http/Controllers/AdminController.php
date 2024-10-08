<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Package;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin', [
            'enabled_packages' => Package::all(),
            'disabled_packages' => Package::withoutGlobalScopes()->where('is_disabled', true)->get(),
        ]);
    }
}
