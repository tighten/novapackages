@extends('layouts.app')

@section('content')
<div class="flex items-center">
    <div class="md:w-1/2 md:mx-auto">
        <div class="flex mb-8">
            <div class="h-48 w-48 border-4 border-white rounded-full overflow-hidden">
                <img src="{{ $collaborator->avatar }}" alt="{{ $collaborator->name }}">
            </div>

            <div class="pl-4 flex-1">
                <div class="font-medium text-2xl py-3">
                    <strong>{{ $collaborator->name }}</strong>
                </div>
                <p class="py-1"><span class="font-bold">GitHub Username:</span> <a href="https://github.com/{{ $collaborator->github_username }}" class="text-indigo-600 underline">{{ $collaborator->github_username }}</a></p>
                @if ($collaborator->url)
                    <p class="py-1"><span class="font-bold">URL:</span> <a href="{{ $collaborator->url }}" rel="nofollow" class="text-indigo-600 underline">{{ $collaborator->url }}</a></p>
                @endif
                @if ($collaborator->description)
                    <div class="mt-4 text-">
                        {!! nl2br($collaborator->description) !!}
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded shadow">
            <div class="bg-white p-3 rounded-b">
                <h3 class="mt-2 mb-4 p-4 bg-gray-100 mx-2 text-lg font-bold">Authored packages</h3>
                <div class="px-2 mb-4">
                    @each('collaborators.package-card', $collaborator->authoredPackages, 'package')
                </div>

                <h3 class="mt-2 mb-4 p-4 bg-gray-100 mx-2 text-lg font-bold">Contributed packages</h3>
                <div class="px-2 mb-4">
                    @each('collaborators.package-card', $collaborator->contributedPackages, 'package')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
