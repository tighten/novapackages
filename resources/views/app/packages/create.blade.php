@extends('layouts.app')

@section('title', 'Create package')

@section('top-id', 'vue-app')

@section('content')
<div class="flex items-center">
    <div class="md:w-1/2 mx-4 md:mx-auto">
        <div class="rounded shadow">
            <div class="font-medium text-lg text-brand-darker bg-brand-lighter p-3 rounded-t">
                Submit Package
            </div>
            <div class="bg-white p-3 rounded-b">
                @include('partials.errors')

                <form method="post" action="{{ route('app.packages.store') }}">
                    @csrf

                    <label class="block font-bold">Package name*</label>
                    <p class="max-w-sm text-gray-800 text-sm mb-2">Important note: When this package is displayed on a listing page, we will trim <code>"Laravel Nova "</code> and <code>"Nova "</code> from the beginning and we'll trim <code>" for Nova"</code> off the end.</p>
                    <input name="name" placeholder="Nova Stock Ticker" class="border border-gray-600 p-2 mb-6 w-64" value="{{ old('name') }}">

                    <label class="block font-bold">Packagist namespace*</label>
                    <input name="packagist_namespace" placeholder="tightenco" class="border border-gray-600 p-2 mb-6 w-64" value="{{ old('packagist_namespace') }}">

                    <label class="block font-bold">Packagist project name*</label>
                    <input name="packagist_name" placeholder="nova-stock-ticker" class="border border-gray-600 p-2 mb-6 w-64" value="{{ old('packagist_name') }}">

                    <label class="block font-bold">Package author*</label>
                    <collaborator-select :collaborators="{{ $collaborators }}" :initial-selected="{{ json_encode(old('selectedAuthor')) }}" name="author_id"></collaborator-select>

                    <label class="block font-bold">Contributors</label>
                    <collaborator-select multiple :collaborators="{{ $collaborators }}" :initial-selected="{{ json_encode(old('selectedCollaborators')) }}" name="contributors"></collaborator-select>

                    <label class="block font-bold">Tags</label>
                    <tag-select :tags="{{ $tags }}" :initial-selected="{{ json_encode(old('selectedTags')) }}" name="tags"></tag-select>

                    <label class="block font-bold">URL (e.g. GitHub)*</label>
                    <input name="url" placeholder="https://github.com/tightenco/nova-stock-ticker" class="border border-gray-600 p-2 mb-6 w-128 max-w-full" value="{{ old('url') }}">

                    <label class="block font-bold">Abstract*</label>
                    <p class="max-w-sm text-gray-800 text-sm mb-2">The short description that shows on a list page; not required; will be parsed from the description if not provided</p>
                    <textarea name="abstract" class="border border-gray-600 p-2" maxlength="190" cols="40">{{ old('abstract') }}</textarea>
                    <p class="mb-6 text-gray-600 text-sm italic">Max length 190 characters</p>
                    {{-- @todo show a character counter --}}

                    <label class="block font-bold">Installation Instructions</label>
                    <p class="max-w-sm text-gray-800 text-sm mb-2">Optional, but will be shown <i>before</i> your readme. If you'd like to give quick installation steps up-front, write them here.</p>
                    <textarea name="instructions" class="border border-gray-600 p-2 w-full" rows="7" cols="40">{{ old('instructions') }}</textarea>
                    <p class="mb-6 text-gray-600 text-sm italic">(Write in Markdown)</p>

                    <label class="block font-bold">Screenshots</label>
                    <p class="max-w-sm text-gray-800 text-sm mb-2">Most of the packages right now don't have screenshots and it makes it very difficult for people to understand what they do.</p>
                    <package-screenshots class="mb-6" :initial-screenshots="{{ json_encode(old('screenshots', [])) }}"></package-screenshots>

                    <label class="block font-bold">Readme</label>
                    <p class="max-w-sm text-gray-800 text-sm mb-6">The readme for this project is consumed from the URL you specify. If your package is on Packagist, we will pull the VCS source Packagist points to and consume its readme. If you provide a GitHub URL in the "URL" field, we will bypass Packagist and pull the readme directly from that repo.</p>

                    <input type="submit" value="Submit package" class="block border border-gray-600 p-2 px-6">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
