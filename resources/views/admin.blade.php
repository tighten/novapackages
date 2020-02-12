@extends('layouts.app')

@section('content')
<div class="flex items-center">
    <div class="md:w-1/2 md:mx-auto">
        <div class="rounded shadow">
            <div class="font-medium text-lg text-brand-darker bg-brand-lighter p-3 rounded-t">
                Admin Dashboard
            </div>
            <div class="bg-white p-3 rounded-b">
                @if (session('status'))
                    <div class="bg-green-100 border border-green-300 text-green-600 text-sm px-4 py-3 rounded mb-4">
                        {{ session('status') }}
                    </div>
                @endif

                <p class="text-gray-600 text-sm">
                    Admin stuff here
                </p>

                @if ($enabled_packages->isNotEmpty())
                    <h3 class="mt-6">Enabled Packages</h3>
                    <ul>
                        @foreach ($enabled_packages as $enabled_package)
                            <li>
                                <a href="{{ route('packages.show', [
                                            'namespace' => $enabled_package->composer_vendor,
                                            'name' => $enabled_package->composer_package,
                                        ]) }}"
                                >{{ $enabled_package->name }}</a> - (<a href="{{ route('app.packages.edit', [$enabled_package]) }}">edit</a>)
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($disabled_packages->isNotEmpty())
                    <h3 class="mt-6">Disabled Packages</h3>
                    <ul>
                        @foreach ($disabled_packages as $disabled_package)
                            <li>
                                <a href="{{ route('packages.show', [
                                            'namespace' => $disabled_package->composer_vendor,
                                            'name' => $disabled_package->composer_package,
                                        ]) }}"
                                >{{ $disabled_package->name }}</a> - (<a href="{{ route('app.packages.edit', [$disabled_package]) }}">edit</a>)
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
