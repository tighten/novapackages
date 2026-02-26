<?php

namespace App\Http\Controllers\App;

use App\Models\Collaborator;
use App\Events\CollaboratorClaimed;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CollaboratorClaimController extends Controller
{
    public function create(Collaborator $collaborator): View
    {
        return view('app.collaborators.claim', [
            'collaborator' => $collaborator,
        ]);
    }

    public function store(Collaborator $collaborator): RedirectResponse
    {
        $collaborator->user()->associate(auth()->user())->save();

        event(new CollaboratorClaimed($collaborator, auth()->user()));

        return redirect()->route('app.collaborators.index');
    }
}
