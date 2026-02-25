<div>
    @foreach ($reviews as $review)
        @include('livewire.partials.review', ['review' => $review])
    @endforeach
</div>
