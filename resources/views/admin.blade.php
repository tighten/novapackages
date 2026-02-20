@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-4xl mx-auto pt-16 px-4 md:px-0">
    <div class="flex flex-col mb-12 items-center justify-between sm:flex-row">
        <h1 class="text-gray-800 text-3xl font-bold mb-4 sm:mb-0">Admin Dashboard</h1>
    </div>

    <div class="bg-white p-8 rounded-lg leading-loose text-gray-800 shadow-sm sm:p-12">
        <x-status class="mb-6"/>

        @if ($enabled_packages->isNotEmpty())
            <h3 class="text-gray-800 text-lg font-bold mb-4">Enabled Packages</h3>
            <ul class="list-disc pl-10 mb-8">
                @foreach ($enabled_packages as $enabled_package)
                    <li>
                        <a href="{{ route('packages.show', [
                                    'namespace' => $enabled_package->composer_vendor,
                                    'name' => $enabled_package->composer_package,
                                ]) }}"
                            class="text-indigo-600 underline"
                        >{{ $enabled_package->name }}</a>
                        <a href="{{ route('app.packages.edit', [$enabled_package]) }}" class="text-gray-500 hover:text-gray-700 text-sm no-underline ml-1">(edit)</a>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($disabled_packages->isNotEmpty())
            <h3 class="text-gray-800 text-lg font-bold mb-4">Disabled Packages</h3>
            <ul class="list-disc pl-10">
                @foreach ($disabled_packages as $disabled_package)
                    <li>
                        <a href="{{ route('packages.show', [
                                    'namespace' => $disabled_package->composer_vendor,
                                    'name' => $disabled_package->composer_package,
                                ]) }}"
                            class="text-indigo-600 underline"
                        >{{ $disabled_package->name }}</a>
                        <a href="{{ route('app.packages.edit', [$disabled_package]) }}" class="text-gray-500 hover:text-gray-700 text-sm no-underline ml-1">(edit)</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
