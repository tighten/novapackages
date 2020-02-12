@extends('layouts.app')

@section('title', 'Create collaborator')

@section('content')
<div class="flex items-center">
    <div class="md:w-1/2 md:mx-auto">
        <div class="rounded shadow">
            <div class="font-medium text-lg text-brand-darker bg-brand-lighter p-3 rounded-t">
                Create Collaborator
            </div>
            <div class="bg-white p-3 rounded-b">
                @include('partials.errors')

                <form method="post" action="{{ route('app.collaborators.store') }}">
                    @csrf

                    <label class="block">*Name:</label>
                    <input name="name" placeholder="Matt Stauffer" class="border border-gray-600 p-2 mb-6" value="{{ old('name') }}" required>

                    <label class="block">*GitHub username:</label>
                    <input name="github_username" placeholder="mattstauffer" class="border border-gray-600 p-2 mb-6" value="{{ old('github_username') }}" required>

                    <label class="block">URL (e.g. personal web site):</label>
                    <input name="url" placeholder="https://mattstauffer.com/" class="border border-gray-600 p-2 mb-6" value="{{ old('url') }}">

                    <label class="block">Description:</label>
                    <textarea name="description" class="border border-gray-600 p-2 mb-6">{{ old('description') }}</textarea>

                    <input type="submit" value="Create collaborator" class="block border border-gray-600 p-2 px-6">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
