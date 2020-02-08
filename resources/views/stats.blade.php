@extends('layouts.app')

@section('title', 'Stats')

@section('content')
<div class="flex items-center">
    <div class="md:w-1/2 mx-2 md:mx-auto">
        <div class="font-bold text-2xl py-3">
            Stats
        </div>
        <div class="rounded shadow">
            <div class="bg-white p-3 rounded-b leading-normal">
                <ul class="list-disc pl-10">
                    <li><strong>Package Count</strong>: {{ $stats->packageCount() }}</li>
                    <li><strong>Collaborators Count</strong>: {{ $stats->collaboratorsCount() }}</li>
                    <li><strong>Packagist Download Count</strong>: {{ $stats->packagistDownloadsCount() }}</li>
                    <li><strong>GitHub Star Count</strong>: {{ $stats->githubStarsCount() }}</li>
                    <li><strong>Total number of ratings</strong>: {{ $stats->ratingsCount() }}</li>
                    <li><strong>Global average rating</strong>: {{ $stats->globalAverageRating() }} / 5
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
