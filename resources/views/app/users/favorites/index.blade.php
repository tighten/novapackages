@extends('layouts.app')

@section('title', 'Favorites')

@section('content')
<div class="max-w-4xl mx-auto pt-16 px-4 md:px-0">
    <div class="bg-white p-8 rounded-lg leading-loose text-gray-800 shadow sm:p-12">
        <h3 class="text-gray-800 mb-4">My Favorites</h3>
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
