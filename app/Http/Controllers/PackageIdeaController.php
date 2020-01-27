<?php

namespace App\Http\Controllers;

use App\Http\Remotes\GitHub;
use Illuminate\Http\Request;

class PackageIdeaController extends Controller
{
    public function __invoke(GitHub $github)
    {
        $ideas = collect($github->packageIdeaIssues());

        return view('package-ideas', [
            'claimed_ideas' => $this->claimedIdeas($ideas),
            'unclaimed_ideas' => $this->unclaimedIdeas($ideas),
        ]);
    }

    private function claimedIdeas($ideas)
    {
        return $ideas->filter(function ($idea) {
            return $this->hasChallengeAcceptedLabel($idea);
        });
    }

    private function unclaimedIdeas($ideas)
    {
        return $ideas->reject(function ($idea) {
            return $this->hasChallengeAcceptedLabel($idea);
        });
    }

    private function hasChallengeAcceptedLabel($idea)
    {
        return collect($idea['labels'])->filter(function ($label) {
            return $label['name'] == 'challenge-accepted';
        })->count() > 0;
    }
}
