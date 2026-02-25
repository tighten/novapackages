<div>
    @if (count($tokens) > 0)
        <div class="relative flex flex-col min-w-0 rounded-sm wrap-break-word border bg-white border border-gray-400 card-default">
            <div class="py-3 px-6 mb-0 bg-gray-100 border-b border-gray-400 text-gray-800">Authorized Applications</div>

            <div class="flex-auto p-6">
                <table class="w-full max-w-full mb-4 bg-transparent table-borderless mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Scopes</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($tokens as $token)
                            <tr>
                                <td style="vertical-align: middle;">{{ $token['client_name'] }}</td>
                                <td style="vertical-align: middle;">
                                    @if (count($token['scopes']) > 0)
                                        {{ implode(', ', $token['scopes']) }}
                                    @endif
                                </td>
                                <td style="vertical-align: middle;">
                                    <a class="cursor-pointer text-red-600" wire:click="revoke('{{ $token['id'] }}')">
                                        Revoke
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
