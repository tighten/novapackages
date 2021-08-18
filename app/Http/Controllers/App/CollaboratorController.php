<?php

namespace App\Http\Controllers\App;

use App\Collaborator;
use App\Events\CollaboratorCreated;
use App\Http\Controllers\Controller;
use App\Http\Remotes\GitHub;
use Github\Exception\RuntimeException as GitHubException;
use Illuminate\Validation\Rule;

class CollaboratorController extends Controller
{
    public function index()
    {
        return view('app.collaborators.index', [
            'unclaimed_collaborators' => Collaborator::doesntHave('user')->get(),
        ]);
    }

    public function create()
    {
        return view('app.collaborators.create');
    }

    public function store()
    {
        $input = request()->validate([
            'name' => 'required',
            'github_username' => 'required|unique:collaborators,github_username',
            'url' => 'nullable|url',
            'description' => 'nullable',
        ]);

        try {
            $githubData = $this->getCollaboratorGitHubData($input['github_username']);
        } catch (GitHubException $e) {
            return redirect()->back()->withInput()->withErrors([
                'github_username' => 'Sorry, but that is not a valid GitHub username.',
            ]);
        }

        $collaborator = Collaborator::create(array_merge($input, $githubData));

        event(new CollaboratorCreated($collaborator));

        return redirect()->route('app.collaborators.index');
    }

    public function edit(Collaborator $collaborator)
    {
        return view('app.collaborators.edit', compact('collaborator'));
    }

    public function update(Collaborator $collaborator)
    {
        $input = request()->validate([
            'name' => 'required',
            'github_username' => [
                'required',
                Rule::unique('collaborators')->ignore($collaborator),
            ],
            'url' => 'nullable|url',
            'description' => 'nullable',
        ]);

        try {
            $githubData = ($collaborator->github_username != request('github_username'))
                ? $this->getCollaboratorGitHubData($input['github_username'])
                : [];
        } catch (GitHubException $e) {
            return redirect()->back()->withInput()->withErrors([
                'github_username' => 'Sorry, but that is not a valid GitHub username.',
            ]);
        }

        $collaborator = $collaborator->update(array_merge($input, $githubData));

        return redirect()->route('app.collaborators.index');
    }

    private function getCollaboratorGitHubData($username)
    {
        return [
            'avatar' => app(GitHub::class)->user($username)['avatar_url'] ?? null,
        ];
    }
}
