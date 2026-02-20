@extends('layouts.app')

@section('title', 'Edit package')

@section('content')
<div class="max-w-4xl mx-auto pt-16 px-4 md:px-0">
    <div class="flex flex-col mb-12 items-center justify-between sm:flex-row">
        <h1 class="text-gray-800 text-3xl font-bold mb-4 sm:mb-0">Edit Package</h1>

        <a href="{{ route('app.packages.index') }}" class="text-indigo-600 font-bold text-sm uppercase no-underline hover:text-indigo-900">
            &larr; Back to Packages
        </a>
    </div>

    <div class="bg-white p-8 rounded-lg text-gray-800 shadow-sm sm:p-12">
        @include('partials.errors')

        <form id="edit_package" method="post" action="{{ route('app.packages.update', [$package]) }}">
            @method('PUT')
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Package name <span class="text-red-500">*</span></label>
                <p class="max-w-lg text-gray-500 text-sm mb-2">Important note: When this package is displayed on a listing page, we will trim <code class="bg-gray-100 px-1 rounded">"Laravel Nova "</code> and <code class="bg-gray-100 px-1 rounded">"Nova "</code> from the beginning and we'll trim <code class="bg-gray-100 px-1 rounded">" for Nova"</code> off the end.</p>
                <input name="name" placeholder="Nova Stock Ticker" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" value="{{ old('name', $package->name) }}">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Packagist namespace <span class="text-red-500">*</span></label>
                <input name="packagist_namespace" placeholder="tightenco" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" value="{{ old('packagist_namespace', $package->composer_vendor) }}">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Packagist project name <span class="text-red-500">*</span></label>
                <input name="packagist_name" placeholder="nova-stock-ticker" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" value="{{ old('packagist_name', $package->composer_package) }}">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Package author <span class="text-red-500">*</span></label>
                <x-collaborator-select :collaborators="$collaborators" :selected="old('author_id', $package->author_id)" name="author_id" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Contributors</label>
                <x-collaborator-select :collaborators="$collaborators" :selected="old('contributors', $package->contributors->pluck('id')->toArray())" name="contributors" :multiple="true" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Tags</label>
                <x-tag-select :tags="$tags" :selected="old('tags', $package->tags->pluck('id')->toArray())" name="tags" />
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">URL (e.g. GitHub) <span class="text-red-500">*</span></label>
                <input name="url" placeholder="https://github.com/tightenco/nova-stock-ticker" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" value="{{ old('url', $package->url) }}">
                @if ($package->marked_as_unavailable_at)
                <p class="text-red-500 text-sm mt-1">
                    This URL was recently marked as inaccessible. Please review and update as necessary!
                </p>
                @endif
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Abstract <span class="text-red-500">*</span></label>
                <p class="max-w-lg text-gray-500 text-sm mb-2">The short description that shows on a list page; not required; will be parsed from the description if not provided</p>
                <textarea name="abstract" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" maxlength="190" rows="3">{{ old('abstract', $package->getAttributes()['abstract']) }}</textarea>
                <p class="text-gray-500 text-sm italic mt-1">Max length 190 characters</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Installation Instructions</label>
                <p class="max-w-lg text-gray-500 text-sm mb-2">Optional, but will be shown <em>before</em> your readme. If you'd like to give quick installation steps up-front, write them here.</p>
                <textarea name="instructions" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" rows="7">{{ old('instructions', $package->instructions) }}</textarea>
                <p class="text-gray-500 text-sm italic mt-1">Write in Markdown</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Screenshots</label>
                @livewire('package-screenshots', ['screenshots' => old('screenshots', $screenshots->map(fn($s) => ['id' => $s->id, 'public_url' => $s->public_url])->toArray())])
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-1">Readme</label>
                <p class="max-w-lg text-gray-500 text-sm">The readme for this project is consumed from the URL you specify. If your package is on Packagist, we will pull the VCS source Packagist points to and consume its readme. If you provide a GitHub URL in the "URL" field, we will bypass Packagist and pull the readme directly from that repo.</p>
            </div>
        </form>

        <div class="flex items-center justify-between border-t border-gray-200 pt-6">
            <button form="edit_package" type="submit" class="button--indigo">Save Package</button>

            <form
                method="post"
                action="{{ route('app.packages.delete', [$package]) }}"
                onsubmit="return confirm('Do you really want to delete this package? Associated data like ratings and reviews will be deleted as well.')"
            >
                @method('DELETE')
                @csrf
                <button type="submit" class="button--red">Delete Package</button>
            </form>
        </div>
    </div>
</div>
@endsection
