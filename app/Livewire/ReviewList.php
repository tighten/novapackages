<?php

namespace App\Livewire;

use App\Package;
use App\Review;
use Livewire\Component;

class ReviewList extends Component
{
    public int $packageId;

    public ?int $editingReviewId = null;
    public string $editingContent = '';

    public function mount(int $packageId)
    {
        $this->packageId = $packageId;
    }

    public function edit(int $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $this->editingReviewId = $reviewId;
        $this->editingContent = $review->content;
    }

    public function cancelEdit()
    {
        $this->editingReviewId = null;
        $this->editingContent = '';
    }

    public function update(int $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        $this->authorize('delete', $review);

        Package::findOrFail($this->packageId)->updateReview($this->editingContent);

        $this->editingReviewId = null;
        $this->editingContent = '';
        $this->dispatch('toast', message: 'Review updated.');
    }

    public function delete(int $reviewId)
    {
        $review = Review::findOrFail($reviewId);

        $this->authorize('delete', $review);

        $review->delete();
        $this->dispatch('toast', message: 'Review deleted.');
    }

    public function render()
    {
        $reviews = Review::where('package_id', $this->packageId)
            ->with(['user', 'rating'])
            ->orderByDesc('updated_at')
            ->get();

        return view('livewire.review-list', [
            'reviews' => $reviews,
        ]);
    }
}
