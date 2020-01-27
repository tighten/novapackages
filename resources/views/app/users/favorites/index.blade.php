@extends('layouts.app')

@section('title', 'Favorites')

@section('content')
<div class="max-w-xl mx-auto pt-16 px-4 md:px-0">
    <div class="bg-white p-8 rounded-lg leading-loose text-grey-darkest shadow sm:p-12">
        <h3 class="text-grey-darkest mb-4">My Favorites</h3>
        <ul>
        @foreach ($favorites as $favorite)
            <li>
                <a href="{{ route('packages.show', [
                    'namespace' => $favorite->package->composer_vendor,
                    'name' => $favorite->package->composer_package
                    ]) }}">{{ $favorite->package->name }}</a>
            </li>
        @endforeach
        </ul>
    </div>
</div>
@endsection
