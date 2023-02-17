<?php

namespace App\Http\Controllers\App;

use App\Events\CollaboratorClaimed;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;

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

        return to_route('app.collaborators.index');
    }
}
