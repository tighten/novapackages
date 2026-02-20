<?php

namespace App\Livewire\Passport;

use Laravel\Passport\Token;
use Livewire\Component;

class AuthorizedClients extends Component
{
    public array $tokens = [];

    public function mount()
    {
        $this->loadTokens();
    }

    public function loadTokens()
    {
        $this->tokens = Token::where('user_id', auth()->id())
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->whereNotNull('client_id')
            ->with('client')
            ->get()
            ->filter(fn ($token) => ! $token->client->personal_access_client && ! $token->client->password_client)
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'client_name' => $token->client->name,
                    'scopes' => $token->scopes,
                ];
            })->values()->toArray();
    }

    public function revoke(string $tokenId)
    {
        $token = Token::where('id', $tokenId)
            ->where('user_id', auth()->id())
            ->first();

        if ($token) {
            $token->revoke();
        }

        $this->loadTokens();
        $this->dispatch('toast', message: 'Token revoked.');
    }

    public function render()
    {
        return view('livewire.passport.authorized-clients');
    }
}
