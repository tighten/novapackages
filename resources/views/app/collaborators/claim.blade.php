@extends('layouts.app')

@section('title', 'Claim collaborator')

@section('content')
<div class="max-w-4xl mx-auto pt-16 px-4 md:px-0">
    <div class="flex flex-col mb-12 items-center justify-between sm:flex-row">
        <h1 class="text-gray-800 text-3xl font-bold mb-4 sm:mb-0">Claim Collaborator</h1>

        <a href="{{ route('app.collaborators.index') }}" class="text-indigo-600 font-bold text-sm uppercase no-underline hover:text-indigo-900">
            &larr; Back to Collaborators
        </a>
    </div>

    <div class="bg-white p-8 rounded-lg text-gray-800 shadow-sm sm:p-12">
        @include('partials.errors')

        <form method="post" action="{{ route('app.collaborators.claims.store', [$collaborator]) }}">
            @csrf

            <p class="mb-6 text-gray-700">Just to make sure: you're claiming that you're authorized/permitted to be the user in control of this collaborator record, right?</p>

            <p class="font-bold mb-8 text-gray-800">Yes, we're fully aware that this isn't validating. We're trusting you to not lie.</p>

            <div class="flex items-center gap-4">
                <button type="submit" class="button--indigo">Claim {{ $collaborator->name }}</button>

                <a href="{{ route('app.collaborators.index') }}" class="px-6 py-3 text-sm text-gray-600 no-underline border border-gray-300 rounded-full hover:bg-gray-50 hover:text-gray-800 transition-colors">Just kidding</a>
            </div>
        </form>
    </div>
</div>
@endsection
