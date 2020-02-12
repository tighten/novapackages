@extends('layouts.app')

@section('title', 'Claim collaborator')

@section('content')
<div class="flex items-center">
    <div class="md:w-1/2 md:mx-auto">
        <div class="rounded shadow">
            <div class="font-medium text-lg text-brand-darker bg-brand-lighter p-3 rounded-t">
                Claim Collaborator
            </div>
            <div class="bg-white p-3 rounded-b">
                @include('partials.errors')

                <form method="post" action="{{ route('app.collaborators.claims.store', [$collaborator]) }}">
                    @csrf

                    <p class="mb-8">Just to make sure: you're claiming that you're authorized/permitted to be the user in control of this collaborator record, right?</p>

                    <p class="font-bold mb-8">Yes, we're fully aware that this isn't validating. We're trusting you to not lie.</p>

                    <input type="submit" value="Claim {{ $collaborator->name }}" class="inline-block border border-gray-600 p-2 px-6 cursor-pointer bg-gray-400 hover:border-gray-700">

                    <a href="{{ route('app.collaborators.index') }}" class="inline-block border border-gray-600 hover:border-gray-700 p-2 px-6 no-underline bg-gray-200 text-gray-600">Just kidding</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
