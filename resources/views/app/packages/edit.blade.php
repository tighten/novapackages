@extends('layouts.app')

@section('title', 'Edit package')

@section('content')
<div class="flex items-center">
    <div class="md:w-1/2 mx-4 md:mx-auto">
        <div class="rounded-sm shadow-sm">
            <div class="font-medium text-lg text-brand-darker bg-brand-lighter p-3 rounded-t">
                Edit Package
            </div>
            <div class="bg-white p-3 rounded-b">

                @include('partials.errors')

                <form id="edit_package" method="post" action="{{ route('app.packages.update', [$package]) }}">
                    @method('PUT')
                    @csrf

                    <label class="block font-bold">Package name*</label>
                    <p class="max-w-sm text-gray-800 text-sm mb-2">Important note: When this package is displayed on a listing page, we will trim <code>"Laravel Nova "</code> and <code>"Nova "</code> from the beginning and we'll trim <code>" for Nova"</code> off the end.</p>
                    <input name="name" placeholder="Nova Stock Ticker" class="border border-gray-600 p-2 mb-6 w-64" value="{{ old('name', $package->name) }}">

                    <label class="block font-bold">Packagist namespace*</label>
                    <input name="packagist_namespace" placeholder="tightenco" class="border border-gray-600 p-2 mb-6 w-64" value="{{ old('packagist_namespace', $package->composer_vendor) }}">

                    <label class="block font-bold">Packagist project name*</label>
                    <input name="packagist_name" placeholder="nova-stock-ticker" class="border border-gray-600 p-2 mb-6 w-64" value="{{ old('packagist_name', $package->composer_package) }}">

                    <label class="block font-bold">Package author*</label>
                    <x-collaborator-select :collaborators="$collaborators" :selected="old('author_id', $package->author_id)" name="author_id" />

                    <label class="block font-bold">Contributors</label>
                    <x-collaborator-select :collaborators="$collaborators" :selected="old('contributors', $package->contributors->pluck('id')->toArray())" name="contributors" :multiple="true" />

                    <label class="block font-bold">Tags</label>
                    <x-tag-select :tags="$tags" :selected="old('tags', $package->tags->pluck('id')->toArray())" name="tags" />

                    <label class="block font-bold">URL (e.g. GitHub)*</label>
                    <input name="url" placeholder="https://github.com/tightenco/nova-stock-ticker" class="border border-gray-600 p-2 mb-6 w-lg w-full max-w-full" value="{{ old('url', $package->url) }}">
                    @if ($package->marked_as_unavailable_at)
                    <span class="block text-red -mt-4 mb-4 text-sm text-red-400">
                        This URL was recently marked as inaccessible. Please review and update as necessary!
                    </span>
                    @endif


                    <label class="block font-bold">Abstract*</label>
                    <p class="max-w-sm text-gray-800 text-sm mb-2">The short description that shows on a list page; not required; will be parsed from the description if not provided</p>
                    <textarea name="abstract" class="border border-gray-600 p-2" maxlength="190" cols="40">{{ old('abstract', $package->getAttributes()['abstract']) }}</textarea>
                    <p class="mb-6 text-gray-600 text-sm italic">Max length 190 characters</p>
                    {{-- @todo show a character counter --}}

                    <label class="block font-bold">Installation Instructions</label>
                    <p class="max-w-sm text-gray-800 text-sm mb-2">Optional, but will be shown <i>before</i> your readme. If you'd like to give quick installation steps up-front, write them here.</p> <textarea name="instructions" class="border border-gray-600 p-2 w-full" rows="7" cols="40">{{ old('instructions', $package->instructions) }}</textarea>
                    <p class="mb-6 text-gray-600 text-sm italic">(Write in Markdown)</p>

                    <label class="block font-bold">Screenshots</label>
                    <div class="mb-6">
                        @livewire('package-screenshots', ['screenshots' => old('screenshots', $screenshots->map(fn($s) => ['id' => $s->id, 'public_url' => $s->public_url])->toArray())])
                    </div>

                    <label class="block font-bold">Readme</label>
                    <p class="max-w-sm text-gray-800 text-sm mb-6">The readme for this project is consumed from the URL you specify. If your package is on Packagist, we will pull the VCS source Packagist points to and consume its readme. If you provide a GitHub URL in the "URL" field, we will bypass Packagist and pull the readme directly from that repo.</p>
                </form>
                <div class="flex justify-between">
                    <input form="edit_package" type="submit" value="Save package" class="block border border-gray-600 py-2 px-6">
                    <form
                        method="post"
                        action="{{ route('app.packages.delete', [$package]) }}"
                        onsubmit="return confirm('Do you really want to delete this package? Associated data like ratings and reviews will be deleted as well.')"
                    >
                        @method('DELETE')
                        @csrf
                        <input type="submit" value="Delete Package" class="block border bg-red-300 border-red-700 py-2 px-6">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
