@extends('layouts.app')

@section('content')
    <a href="/" class="container block mx-auto mb-4">
        <img src="/images/hero.svg" alt="Laravel Nova hero" class="w-full md:w-2/3 lg:w-1/2 mx-auto block">
    </a>

    <package-search-results
        :auth="{{ auth()->check() ? 'true' : 'false' }}"
        :query="'{{ $query }}'"
        :initial-packages="{{ json_encode($packages) }}">
    </package-search-results>

    <div v-if="false" class="text-4xl text-center text-grey tracking-wide py-8 my-8">LOADING...</div>
@endsection
