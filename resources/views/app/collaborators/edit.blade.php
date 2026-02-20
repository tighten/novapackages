@extends('layouts.app')

@section('title', 'Edit collaborator')

@section('content')
<div class="max-w-4xl mx-auto pt-16 px-4 md:px-0">
    <div class="flex flex-col mb-12 items-center justify-between sm:flex-row">
        <h1 class="text-gray-800 text-3xl font-bold mb-4 sm:mb-0">Edit Collaborator</h1>

        <a href="{{ route('app.collaborators.index') }}" class="text-indigo-600 font-bold text-sm uppercase no-underline hover:text-indigo-900">
            &larr; Back to Collaborators
        </a>
    </div>

    <div class="bg-white p-8 rounded-lg text-gray-800 shadow-sm sm:p-12">
        @include('partials.errors')

        <form method="post" action="{{ route('app.collaborators.update', $collaborator) }}">
            @csrf
            @method('patch')

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input name="name"
                       placeholder="Matt Stauffer"
                       class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       value="{{ old('name', $collaborator->name) }}"
                       required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">GitHub username <span class="text-red-500">*</span></label>
                <input name="github_username"
                       placeholder="mattstauffer"
                       class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       value="{{ old('github_username', $collaborator->github_username) }}"
                       required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-1">URL (e.g. personal web site)</label>
                <input name="url"
                       placeholder="https://mattstauffer.com/"
                       class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       value="{{ old('url', $collaborator->url) }}">
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-1">Description</label>
                <textarea name="description" class="block w-full max-w-lg rounded-md border border-gray-300 px-3 py-2 text-gray-800 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" rows="4">{{ old('description', $collaborator->description) }}</textarea>
            </div>

            <button type="submit" class="button--indigo">Update Collaborator</button>
        </form>
    </div>
</div>
@endsection
