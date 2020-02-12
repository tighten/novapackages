@extends('layouts.app')

@section('title', 'Collaborators')

@section('content')
<div class="max-w-4xl mx-auto pt-16 px-4 md:px-0">
    <div class="flex flex-col mb-12 items-center justify-between sm:flex-row">
        <h1 class="text-gray-800 text-3xl font-bold mb-4 sm:mb-0">Collaborators</h1>

        <a href="{{ route('app.collaborators.create') }}" title="Create a collaborator" class="button--indigo">
            <img src="{{ asset('images/icon-plus.svg') }}" alt="Plus icon" class="mr-2 inline"> Create Collaborator
        </a>
    </div>

    <div class="bg-white p-8 rounded-lg leading-loose text-gray-800 shadow sm:p-12">
        <h2 class="text-gray-800 text-2xl font-bold mb-4">What's a collaborator?</h2>

        <p class="mb-4">A collaborator is anyone with a GitHub username: a person, a company, or an organization. Collaborators exist in NovaPackages to represent the primary <strong>author</strong> of a package or a <strong>contributor</strong> to the package.</p>

        <p class="mb-4">Each package has <em>one</em> author. This corresponds to the GitHub namespace of a package. For example, <strong>Tighten's <code>tightenco</code></strong>, or <strong>Barry vd. Heuvel's <code>barryvdh</code></strong>. But collaborators can also be <strong>contributors</strong>, which are individuals who worked on the package. Some packages (for example, Barry's packages) will have a single author and a single contributor: Barry. Others (for example, Tighten's packages) will have a single author (Tighten) and multiple contributors (e.g. Keith and Samantha).</p>

        <blockquote class="border-l-4 border-grey pl-4 text-gray-600 mb-8">You can now claim your ownership over a collaborator. Need to edit it, or claimed the wrong collaborator? For now, just email matt at tighten dot co.</blockquote>

        @if (auth()->user()->collaborators)
            <h3 class="text-gray-800 text-lg font-bold">Your {{ Str::plural('Collaborator', auth()->user()->collaborators->count()) }}</h3>
            <ul class="mb-4 list-disc pl-10">
            @foreach (auth()->user()->collaborators as $collaborator)
                <li><a href="{{ route('collaborators.show', $collaborator) }}" class="text-indigo-600 underline">{{ $collaborator->name }}</a> <a href="{{ route('app.collaborators.edit', $collaborator) }}" title="Edit collaborator" class="text-indigo-700 no-underline">(Edit)</a></li>
            @endforeach
            </ul>
        @endif

        <h3 class="text-gray-800 text-lg font-bold">Un-claimed collaborators</h3>
        <ul class="list-disc pl-10">
        @forelse ($unclaimed_collaborators as $collaborator)
            <li><a href="{{ route('collaborators.show', $collaborator) }}" class="text-indigo-600 underline">{{ $collaborator->name }}</a>
                <a href="{{ route('app.collaborators.claims.create', [$collaborator]) }}" class="no-underline bg-gray-600 hover:bg-gray-700 rounded p-1 inline-block mb-1 text-sm text-white">Claim this collaborator</a>
            </li>
        @empty
            <li class="italic">No un-claimed collaborators at this time.</li>
        @endforelse
        </ul>
    </div>
</div>
@endsection
