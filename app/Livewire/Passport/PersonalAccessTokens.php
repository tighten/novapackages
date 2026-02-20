<?php

namespace App\Livewire\Passport;

use Livewire\Component;

class PersonalAccessTokens extends Component
{
    public array $tokens = [];
    public array $scopes = [];

    public bool $showCreateModal = false;
    public bool $showAccessTokenModal = false;

    public string $createName = '';
    public array $createScopes = [];
    public array $createErrors = [];

    public ?string $accessToken = null;

    public function mount()
    {
        $this->loadTokens();
        $this->loadScopes();
    }

    public function loadTokens()
    {
        $this->tokens = auth()->user()->tokens()
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->whereHas('client', function ($query) {
                $query->where('personal_access_client', true);
            })
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                ];
            })->toArray();
    }

    public function loadScopes()
    {
        // Passport scopes if registered
        $this->scopes = collect(\Laravel\Passport\Passport::scopes())->map(function ($scope) {
            return [
                'id' => $scope->id,
                'description' => $scope->description,
            ];
        })->toArray();
    }

    public function openCreateModal()
    {
        $this->reset(['createName', 'createScopes', 'createErrors']);
        $this->showCreateModal = true;
    }

    public function toggleScope(string $scope)
    {
        if (in_array($scope, $this->createScopes)) {
            $this->createScopes = array_values(array_filter($this->createScopes, fn ($s) => $s !== $scope));
        } else {
            $this->createScopes[] = $scope;
        }
    }

    public function store()
    {
        $this->createErrors = [];

        try {
            $result = auth()->user()->createToken($this->createName, $this->createScopes);

            $this->accessToken = $result->accessToken;
            $this->showCreateModal = false;
            $this->showAccessTokenModal = true;

            $this->reset(['createName', 'createScopes']);
            $this->loadTokens();
            $this->dispatch('toast', message: 'Token created.');
        } catch (\Exception $e) {
            $this->createErrors = ['Something went wrong. Please try again.'];
        }
    }

    public function revoke(string $tokenId)
    {
        $token = auth()->user()->tokens()->where('id', $tokenId)->first();

        if ($token) {
            $token->revoke();
        }

        $this->loadTokens();
        $this->dispatch('toast', message: 'Token revoked.');
    }

    public function closeAccessTokenModal()
    {
        $this->showAccessTokenModal = false;
        $this->accessToken = null;
    }

    public function render()
    {
        return view('livewire.passport.personal-access-tokens');
    }
}
