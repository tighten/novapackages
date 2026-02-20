@extends('layouts.app')

@section('title', $package->display_name)

@section('content')
    @livewire('package-show', ['package' => $package, 'creatingReview' => true])
@endsection
