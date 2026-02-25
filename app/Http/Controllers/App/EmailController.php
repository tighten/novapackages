<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmailController extends Controller
{
    public function create(): View
    {
        return view('app.email.create');
    }

    public function store(): RedirectResponse
    {
        request()->validate([
            'email' => 'required|email|unique:users,email',
        ]);

        auth()->user()->update(['email' => request('email')]);

        return redirect()->route('home');
    }
}
