<?php

namespace App\Http\Controllers\App;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function create(): View
    {
        return view('app.email.create');
    }

    public function store(): RedirectResponse
    {
        $this->validate(request(), [
            'email' => 'required|email|unique:users,email',
        ]);

        auth()->user()->update(['email' => request('email')]);

        return redirect()->route('home');
    }
}
