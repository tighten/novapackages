<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;

class EmailController extends Controller
{
    public function create()
    {
        return view('app.email.create');
    }

    public function store()
    {
        $this->validate(request(), [
            'email' => 'required|email|unique:users,email',
        ]);

        auth()->user()->update(['email' => request('email')]);

        return to_route('home');
    }
}
