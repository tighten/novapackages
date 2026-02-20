<div>
    <div
        x-data="{
            dragging: false,
            handleDrop(e) {
                this.dragging = false;
                const files = e.dataTransfer.files;
                if (files.length) {
                    @this.upload('upload', files[0]);
                }
            }
        }"
        x-on:dragover.prevent="dragging = true"
        x-on:dragleave.prevent="dragging = false"
        x-on:drop.prevent="handleDrop($event)"
        class="border-2 border-dashed rounded-sm p-6 text-center mb-4 transition-colors"
        :class="dragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'"
    >
        <div class="text-gray-500 mb-2">Drop screenshots here or click to upload</div>
        <input type="file" wire:model="upload" accept="image/*" class="text-sm">
        @error('upload') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror

        <div wire:loading wire:target="upload" class="text-indigo-600 text-sm mt-2">
            Uploading...
        </div>
    </div>

    @if(count($screenshots))
        <div class="flex flex-wrap items-end mt-4 mb-6">
            @foreach($screenshots as $screenshot)
                <div class="text-center w-1/5 p-2" wire:key="screenshot-{{ $screenshot['id'] }}">
                    <img class="rounded-sm shadow-md mb-2" src="{{ $screenshot['public_url'] }}" />
                    <button
                        wire:click="deleteScreenshot({{ $screenshot['id'] }})"
                        class="bg-red-400 hover:bg-red-600 text-white font-bold py-1 px-2 rounded-sm text-sm"
                    >Delete</button>
                </div>
            @endforeach
        </div>
    @endif

    @foreach($screenshots as $screenshot)
        <input type="hidden" name="screenshots[]" value="{{ $screenshot['id'] }}" />
    @endforeach
</div>
