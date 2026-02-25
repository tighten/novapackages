<div class="flex flex-col my-8 rounded-lg text-gray-600">
    <div class="flex flex-col items-center justify-between mb-6 sm:flex-row">
        <h3 class="text-gray-800 text-xl font-semibold mb-4 sm:mb-0">Personal Access Tokens</h3>

        <a class="button--indigo cursor-pointer" wire:click="openCreateModal">
            <img src="/images/icon-plus.svg" alt="Plus icon" class="mr-2 inline"> Create new token
        </a>
    </div>

    <div class="bg-white flex flex-col min-w-0 rounded-sm wrap-break-word shadow-sm">
        @if (count($tokens) === 0)
            <p class="p-8">
                You have not created any personal access tokens.
            </p>
        @else
            <table class="flex flex-col w-full">
                <thead class="border-b border-gray-300 p-8 pb-4">
                    <tr class="flex pb-2 justify-between text-gray-800">
                        <th class="w-2/3 text-left text-semibold text-gray-800">Name</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody class="flex flex-col p-8 pb-4">
                    @foreach ($tokens as $token)
                        <tr class="flex justify-between mb-8">
                            <td class="align-middle w-2/3">{{ $token['name'] }}</td>
                            <td class="w-1/3 align-middle text-right">
                                <a class="cursor-pointer border border-gray-200 p-2 text-red-600 text-xs sm:text-sm hover:bg-gray-100 hover:border-gray-600" wire:click="revoke('{{ $token['id'] }}')" wire:confirm="Are you sure you want to delete this token?">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Create Token Modal --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data @keydown.escape.window="$wire.set('showCreateModal', false)">
            <div class="fixed inset-0 bg-black/50" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative bg-white rounded-sm shadow-lg w-full max-w-lg mx-4">
                <div class="flex justify-between items-center border-b p-4">
                    <h4 class="text-lg font-semibold">Create Token</h4>
                    <button wire:click="$set('showCreateModal', false)" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>

                <div class="p-4">
                    @if (count($createErrors))
                        <div class="relative px-3 py-3 mb-4 border rounded-sm text-red-900 border-red-700 bg-red-300">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <ul class="mt-2">
                                @foreach ($createErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-4 flex flex-wrap">
                        <label class="md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Name</label>
                        <div class="md:w-1/2 pr-4 pl-4">
                            <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-gray-300 rounded-sm" wire:model="createName" wire:keydown.enter="store">
                        </div>
                    </div>

                    @if (count($scopes) > 0)
                        <div class="mb-4 flex flex-wrap">
                            <label class="md:w-1/3 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Scopes</label>
                            <div class="md:w-1/2 pr-4 pl-4">
                                @foreach ($scopes as $scope)
                                    <div class="checkbox">
                                        <label>
                                            <input
                                                type="checkbox"
                                                wire:click="toggleScope('{{ $scope['id'] }}')"
                                                @checked(in_array($scope['id'], $createScopes))
                                            >
                                            {{ $scope['id'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 border-t p-4">
                    <button class="inline-block align-middle text-center select-none border border-gray-200 font-normal py-2 px-4 rounded-sm text-base leading-normal text-gray-100 bg-gray-500 hover:bg-gray-400" wire:click="$set('showCreateModal', false)">Close</button>
                    <button class="inline-block align-middle text-center select-none border border-gray-200 font-normal py-2 px-4 rounded-sm text-base leading-normal text-blue-100 bg-blue-500 hover:bg-blue-400" wire:click="store">Create</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Access Token Modal --}}
    @if ($showAccessTokenModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data @keydown.escape.window="$wire.closeAccessTokenModal()">
            <div class="fixed inset-0 bg-black/50" wire:click="closeAccessTokenModal"></div>
            <div class="relative bg-white rounded-sm shadow-lg w-full max-w-lg mx-4">
                <div class="flex justify-between items-center border-b p-4">
                    <h4 class="text-lg font-semibold">Personal Access Token</h4>
                    <button wire:click="closeAccessTokenModal" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>

                <div class="p-4">
                    <p>
                        Here is your new personal access token. This is the only time it will be shown so don't lose it!
                        You may now use this token to make API requests.
                    </p>

                    <textarea class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-gray-300 rounded-sm" rows="10" readonly>{{ $accessToken }}</textarea>
                </div>

                <div class="flex justify-end border-t p-4">
                    <button class="inline-block align-middle text-center select-none border border-gray-200 font-normal py-2 px-4 rounded-sm text-base leading-normal text-gray-100 bg-gray-500 hover:bg-gray-400" wire:click="closeAccessTokenModal">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
