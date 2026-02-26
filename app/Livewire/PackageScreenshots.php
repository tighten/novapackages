<?php

namespace App\Livewire;

use App\Models\Screenshot;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class PackageScreenshots extends Component
{
    use WithFileUploads;

    public $screenshots = [];

    #[Validate('image|max:2048')]
    public $upload;

    public function mount($screenshots = [])
    {
        $screenshots = collect($screenshots)->toArray();

        // Handle old() input which may be plain IDs from hidden inputs
        if (! empty($screenshots) && ! is_array(reset($screenshots))) {
            $this->screenshots = Screenshot::whereIn('id', $screenshots)
                ->get()
                ->map(fn ($s) => ['id' => $s->id, 'public_url' => $s->public_url])
                ->toArray();
        } else {
            $this->screenshots = $screenshots;
        }
    }

    public function updatedUpload()
    {
        $this->validate();

        $screenshot = Screenshot::create([
            'uploader_id' => auth()->id(),
            'path' => $this->upload->store('screenshots'),
        ]);

        $this->screenshots[] = [
            'id' => $screenshot->id,
            'public_url' => $screenshot->public_url,
        ];

        $this->reset('upload');
    }

    public function deleteScreenshot($id)
    {
        $screenshot = Screenshot::find($id);

        if ($screenshot) {
            $screenshot->delete();
        }

        $this->screenshots = array_values(
            array_filter($this->screenshots, fn ($s) => $s['id'] != $id)
        );
    }

    public function render()
    {
        return view('livewire.package-screenshots');
    }
}
