<?php

namespace App\Http\Controllers\App;

use App\Collaborator;
use App\Events\CollaboratorClaimed;
use App\Http\Controllers\Controller;

class CollaboratorClaimController extends Controller
{
    public function create(Collaborator $collaborator)
    {
        return view('app.collaborators.claim', [
            'collaborator' => $collaborator,
        ]);
    }

    public function store(Collaborator $collaborator)
    {
        $collaborator->user()->associate(auth()->user())->save();

        event(new CollaboratorClaimed($collaborator, auth()->user()));

        return redirect()->route('app.collaborators.index');
    }
}
