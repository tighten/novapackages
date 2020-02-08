@extends('layouts.app')

@section('title', 'Packages')

@section('content')
<div class="max-w-xl mx-auto pt-16 px-4 md:px-0">
    <div class="flex flex-col mb-12 items-center justify-between sm:flex-row">
        @include('partials.errors')

        <h1 class="text-grey-darkest text-3xl font-bold mb-4 sm:mb-0">Packages</h1>

        <a href="{{ route('app.packages.create') }}" title="Create a package" class="button--indigo">
            <img src="{{ asset('images/icon-plus.svg') }}" alt="Plus icon" class="mr-2 inline"> Create Package
        </a>
    </div>

    <div class="bg-white p-8 rounded-lg leading-loose text-grey-darkest shadow sm:p-12">
        <h3 class="text-grey-darkest text-lg font-bold mb-4">My Packages</h3>

        @if (auth()->user()->collaborators()->count() === 0)
            <p>You currently don't have a collaborator attached to your user.<br><a href="{{ route('app.collaborators.index') }}">Claim or create one now.</a></p>
        @else
            @foreach (auth()->user()->collaborators as $collaborator)
            <h4 class="text-grey-darkest mb-2">Collaborator: {{ $collaborator->name }}</h4>

            <ul class="list-disc pl-10">
            @foreach ($collaborator->authoredPackages as $package)
                <li><a href="{{ route('packages.show', [
                                'namespace' => $package->composer_vendor,
                                'name' => $package->composer_package,
                            ]) }}"
                    >{{ $package->name }}</a>
                    (<a href="{{ route('app.packages.edit', [$package]) }}">edit</a>) - Author</li>
            @endforeach
            @foreach ($collaborator->submittedPackages as $package)
                @if (($package->author->id == $collaborator->id) || $package->contributors->contains($collaborator->id))
                    @continue
                @endif
                <li><a href="{{ route('packages.show', [
                                'namespace' => $package->composer_vendor,
                                'name' => $package->composer_package,
                            ]) }}"
                    >{{ $package->name }}</a>
                    @if(! $package->authorIsUser())
                        (<a href="{{ route('app.packages.edit', [$package]) }}">edit</a>)
                    @endif
                     - Submitter</li>
            @endforeach
            @foreach ($collaborator->contributedPackages as $package)
                <li><a href="{{ route('packages.show', [
                                'namespace' => $package->composer_vendor,
                                'name' => $package->composer_package,
                            ]) }}"
                    >{{ $package->name }}</a> - Contributor</li>
            @endforeach

            @if ($collaborator->authoredPackages->count() === 0 && $collaborator->contributedPackages->count() === 0 && $collaborator->submittedPackages->count() === 0)
                <li class="italic"><span class="text-grey-dark">(No packages for this collaborator)</span></li>
            @endif
            </ul>
            @endforeach
        @endif

        <h3 class="text-grey-darkest text-lg font-bold my-4">My Favorite Packages</h3>
        @if ($favoritePackages->count() === 0)
            <p>You currently do not have any favorite packages. Visit a package detail page to add it as a favorite</a></p>
        @else
            <ul class="list-disc pl-10">
            @foreach ($favoritePackages as $favoritePackage)
                <li>
                    <a href="{{ route('packages.show', [
                        'namespace' => $favoritePackage->composer_vendor,
                        'name' => $favoritePackage->composer_package
                        ]) }}"
                        class="text-blue underline"
                        >{{ $favoritePackage->name }}</a>
                </li>
            @endforeach
            </ul>
        @endif
        <h3 class="text-grey-darkest text-lg font-bold mt-6">All Packages</h3>
        <ul class="list-disc pl-10">
        @foreach ($packages as $package)
            <li><a href="{{ route('packages.show', [
                            'namespace' => $package->composer_vendor,
                            'name' => $package->composer_package,
                        ]) }}"
                class="text-indigo no-underline">{{ $package->name }}</a>

                @if (Auth::user()->isAdmin())
                    - (<a href="{{ route('app.packages.edit', [$package]) }}">edit</a>)</li>
                @endif
        @endforeach
        </ul>
    </div>
</div>
@endsection
