<?php

namespace App\Livewire\Passport;

use Illuminate\Support\Facades\Http;
use Laravel\Passport\ClientRepository;
use Livewire\Component;

class Clients extends Component
{
    public array $clients = [];

    public bool $showCreateModal = false;
    public bool $showEditModal = false;

    public string $createName = '';
    public string $createRedirect = '';
    public array $createErrors = [];

    public ?string $editId = null;
    public string $editName = '';
    public string $editRedirect = '';
    public array $editErrors = [];

    public function mount()
    {
        $this->loadClients();
    }

    public function loadClients()
    {
        $clientRepository = app(ClientRepository::class);
        $this->clients = $clientRepository->forUser(auth()->user())->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name,
                'secret' => $client->secret,
                'redirect' => $client->redirect,
            ];
        })->toArray();
    }

    public function openCreateModal()
    {
        $this->reset(['createName', 'createRedirect', 'createErrors']);
        $this->showCreateModal = true;
    }

    public function store()
    {
        $this->createErrors = [];

        try {
            $clientRepository = app(ClientRepository::class);
            $clientRepository->createAuthorizationCodeGrantClient(
                $this->createName,
                [$this->createRedirect],
                true,
                auth()->user(),
            );

            $this->showCreateModal = false;
            $this->reset(['createName', 'createRedirect']);
            $this->loadClients();
            $this->dispatch('toast', message: 'Client created.');
        } catch (\Exception $e) {
            $this->createErrors = ['Something went wrong. Please try again.'];
        }
    }

    public function edit(string $clientId)
    {
        $this->editErrors = [];
        $client = collect($this->clients)->firstWhere('id', $clientId);

        if ($client) {
            $this->editId = $client['id'];
            $this->editName = $client['name'];
            $this->editRedirect = $client['redirect'];
            $this->showEditModal = true;
        }
    }

    public function update()
    {
        $this->editErrors = [];

        try {
            $clientRepository = app(ClientRepository::class);
            $client = $clientRepository->findForUser($this->editId, auth()->user());

            if ($client) {
                $clientRepository->update($client, $this->editName, [$this->editRedirect]);
            }

            $this->showEditModal = false;
            $this->loadClients();
            $this->dispatch('toast', message: 'Client updated.');
        } catch (\Exception $e) {
            $this->editErrors = ['Something went wrong. Please try again.'];
        }
    }

    public function destroy(string $clientId)
    {
        $clientRepository = app(ClientRepository::class);
        $client = $clientRepository->findForUser($clientId, auth()->user());

        if ($client) {
            $clientRepository->delete($client);
        }

        $this->loadClients();
        $this->dispatch('toast', message: 'Client deleted.');
    }

    public function render()
    {
        return view('livewire.passport.clients');
    }
}
