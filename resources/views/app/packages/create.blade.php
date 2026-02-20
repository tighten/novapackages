@extends('layouts.app')

@section('title', 'Create package')

@section('content')
<div class="max-w-4xl mx-auto pt-16 px-4 md:px-0">
    <div class="flex flex-col mb-12 items-center justify-between sm:flex-row">
        <h1 class="text-gray-800 text-3xl font-bold mb-4 sm:mb-0">Submit Package</h1>

        <a href="{{ route('app.packages.index') }}" class="text-indigo-600 font-bold text-sm uppercase no-underline hover:text-indigo-900">
            &larr; Back to Packages
        </a>
    </div>

    <div class="bg-white p-8 rounded-lg text-gray-800 shadow-sm sm:p-12">
        @include('partials.errors')

        <form method="post" action="{{ route('app.packages.store') }}">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Package name <span class="text-red-500">*</span></label>
                <p class="max-w-lg text-gray-500 text-sm mb-2">Important note: When this package is displayed on a
                    listing page, we will trim references to "Nova", "Laravel" and mentions of version numbers such as "Nova
                    {{ config('novapackages.nova.latest_major_version') }}",
                    "V{{ config('novapackages.nova.latest_major_version') }}" and
                    "N{{ config('novapackages.nova.latest_major_version') }}". Instead, we encourage you to require Laravel Nova's version in your "composer.json" which will show in the detail page and on the listing page if its the latest major version of Nova.</p>
                <input name="name" placeholder="Nova Stock Ticker" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" value="{{ old('name') }}">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Packagist namespace <span class="text-red-500">*</span></label>
                <input name="packagist_namespace" placeholder="tightenco" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" value="{{ old('packagist_namespace') }}">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Packagist project name <span class="text-red-500">*</span></label>
                <input name="packagist_name" placeholder="nova-stock-ticker" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" value="{{ old('packagist_name') }}">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Package author <span class="text-red-500">*</span></label>
                <x-collaborator-select :collaborators="$collaborators" :selected="old('author_id')" name="author_id" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Contributors</label>
                <x-collaborator-select :collaborators="$collaborators" :selected="old('contributors', [])" name="contributors" :multiple="true" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Tags</label>
                <x-tag-select :tags="$tags" :selected="old('tags', [])" name="tags" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">URL (e.g. GitHub) <span class="text-red-500">*</span></label>
                <input name="url" placeholder="https://github.com/tightenco/nova-stock-ticker" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" value="{{ old('url') }}">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Abstract <span class="text-red-500">*</span></label>
                <p class="max-w-lg text-gray-500 text-sm mb-2">The short description that shows on a list page</p>
                <textarea name="abstract" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" maxlength="190" rows="3">{{ old('abstract') }}</textarea>
                <p class="text-gray-500 text-sm italic mt-1">Max length 190 characters</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Installation Instructions</label>
                <p class="max-w-lg text-gray-500 text-sm mb-2">Optional, but will be shown <em>before</em> your readme. If you'd like to give quick installation steps up-front, write them here.</p>
                <textarea name="instructions" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" rows="7">{{ old('instructions') }}</textarea>
                <p class="text-gray-500 text-sm italic mt-1">Write in Markdown</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Screenshots</label>
                <p class="max-w-lg text-gray-500 text-sm mb-2">Most of the packages right now don't have screenshots and it makes it very difficult for people to understand what they do.</p>
                @livewire('package-screenshots', ['screenshots' => old('screenshots', [])])
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-1">Readme</label>
                <p class="max-w-lg text-gray-500 text-sm">The readme for this project is consumed from the URL you specify. If your package is on Packagist, we will pull the VCS source Packagist points to and consume its readme. If you provide a GitHub URL in the "URL" field, we will bypass Packagist and pull the readme directly from that repo.</p>
            </div>

            <button type="submit" class="button--indigo">Submit Package</button>
        </form>
    </div>
</div>
@endsection
