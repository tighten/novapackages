@extends('layouts.app')

@section('top-id', 'vue-app')

@section('title', $package['name'])

@section('content')
    <package-detail-frame
        :auth="{{ auth()->check() ? 'true' : 'false' }}"
        :initial-package="{{ json_encode($package) }}"
        :creating-review="true"
    >
        <package-review-create
            :package="{{ json_encode($package) }}"
            :star-rating="{{ json_encode($userStarRating) }}"
        />
    </package-detail-frame>
@endsection
