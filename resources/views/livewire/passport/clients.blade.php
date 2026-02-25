<div class="flex flex-col my-8 rounded-lg text-gray-600">
    <div class="flex flex-col items-center justify-between mb-6 sm:flex-row">
        <h3 class="text-gray-800 text-xl font-semibold mb-4 sm:mb-0">OAuth Clients</h3>

        <a class="button--indigo cursor-pointer" wire:click="openCreateModal">
            <img src="/images/icon-plus.svg" alt="Plus icon" class="mr-2 inline"> Create new client
        </a>
    </div>

    <div class="bg-white flex flex-col min-w-0 rounded-sm wrap-break-word shadow-sm">
        @if (count($clients) === 0)
            <p class="mb-0 p-8">
                You have not created any OAuth clients.
            </p>
        @else
            <table class="flex flex-col w-full">
                <thead class="border-b border-gray-300 p-6 sm:p-8 pb-4">
                    <tr class="flex pb-2 justify-between text-gray-800">
                        <th class="text-left w-1/5 font-semibold hidden md:block">Client ID</th>
                        <th class="text-left font-semibold w-1/5">Name</th>
                        <th class="text-left font-semibold w-1/2">Secret</th>
                        <th class="text-left font-semibold w-1/5"></th>
                    </tr>
                </thead>

                <tbody class="flex flex-col p-6 sm:p-8 sm:pb-4">
                    @foreach ($clients as $client)
                        <tr class="flex justify-between mb-4">
                            <td class="w-1/5 align-middle hidden md:block">{{ $client['id'] }}</td>
                            <td class="w-1/5 align-middle text-left">{{ $client['name'] }}</td>
                            <td class="w-1/2 align-middle"><code>{{ $client['secret'] }}</code></td>
                            <td class="flex flex-col w-1/4 align-middle justify-around text-center text-xs sm:text-sm sm:flex-row sm:justify-end md:w-1/5">
                                <a class="cursor-pointer border border-gray-200 inline-block mb-2 p-2 hover:bg-gray-100 sm:mr-2 hover:border-gray-600" wire:click="edit('{{ $client['id'] }}')">
                                    Edit
                                </a>
                                <a class="cursor-pointer border border-gray-200 inline-block mb-2 p-2 text-red-600 hover:bg-gray-100 hover:border-gray-600" wire:click="destroy('{{ $client['id'] }}')" wire:confirm="Are you sure you want to delete this client?">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Create Client Modal --}}
    @if ($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data @keydown.escape.window="$wire.set('showCreateModal', false)">
            <div class="fixed inset-0 bg-black/50" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative bg-white rounded-sm shadow-lg w-full max-w-lg mx-4">
                <div class="flex justify-between items-center border-b p-4">
                    <h4 class="text-lg font-semibold">Create Client</h4>
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
                        <label class="md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Name</label>
                        <div class="md:w-3/4 pr-4 pl-4">
                            <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-gray-300 rounded-sm" wire:model="createName" wire:keydown.enter="store">
                            <span class="block mt-1 text-gray-500">Something your users will recognize and trust.</span>
                        </div>
                    </div>

                    <div class="mb-4 flex flex-wrap">
                        <label class="md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Redirect URL</label>
                        <div class="md:w-3/4 pr-4 pl-4">
                            <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-gray-300 rounded-sm" wire:model="createRedirect" wire:keydown.enter="store">
                            <span class="block mt-1 text-gray-500">Your application's authorization callback URL.</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t p-4">
                    <button class="inline-block align-middle text-center select-none border border-gray-200 font-normal py-2 px-4 rounded-sm text-base leading-normal text-gray-100 bg-gray-500 hover:bg-gray-400" wire:click="$set('showCreateModal', false)">Close</button>
                    <button class="inline-block align-middle text-center select-none border border-gray-200 font-normal py-2 px-4 rounded-sm text-base leading-normal text-blue-100 bg-blue-500 hover:bg-blue-400" wire:click="store">Create</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit Client Modal --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center" x-data @keydown.escape.window="$wire.set('showEditModal', false)">
            <div class="fixed inset-0 bg-black/50" wire:click="$set('showEditModal', false)"></div>
            <div class="relative bg-white rounded-sm shadow-lg w-full max-w-lg mx-4">
                <div class="flex justify-between items-center border-b p-4">
                    <h4 class="text-lg font-semibold">Edit Client</h4>
                    <button wire:click="$set('showEditModal', false)" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>

                <div class="p-4">
                    @if (count($editErrors))
                        <div class="relative px-3 py-3 mb-4 border rounded-sm text-red-900 border-red-700 bg-red-300">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <ul class="mt-2">
                                @foreach ($editErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-4 flex flex-wrap">
                        <label class="md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Name</label>
                        <div class="md:w-3/4 pr-4 pl-4">
                            <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-gray-300 rounded-sm" wire:model="editName" wire:keydown.enter="update">
                            <span class="block mt-1 text-gray-500">Something your users will recognize and trust.</span>
                        </div>
                    </div>

                    <div class="mb-4 flex flex-wrap">
                        <label class="md:w-1/4 pr-4 pl-4 pt-2 pb-2 mb-0 leading-normal">Redirect URL</label>
                        <div class="md:w-3/4 pr-4 pl-4">
                            <input type="text" class="block appearance-none w-full py-1 px-2 mb-1 text-base leading-normal bg-white text-gray-600 border border-gray-300 rounded-sm" wire:model="editRedirect" wire:keydown.enter="update">
                            <span class="block mt-1 text-gray-500">Your application's authorization callback URL.</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t p-4">
                    <button class="inline-block align-middle text-center select-none border border-gray-200 font-normal py-2 px-4 rounded-sm text-base leading-normal text-gray-100 bg-gray-500 hover:bg-gray-400" wire:click="$set('showEditModal', false)">Close</button>
                    <button class="inline-block align-middle text-center select-none border border-gray-200 font-normal py-2 px-4 rounded-sm text-base leading-normal text-blue-100 bg-blue-500 hover:bg-blue-400" wire:click="update">Save Changes</button>
                </div>
            </div>
        </div>
    @endif
</div>
