<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Services\EventService;

new class extends Component
{
    use WithFileUploads;

    // Form fields starting value
    public $title = "";
    public $category_id = null;
    public $image = null;
    public $start_time = null;
    public $content = "";
    public $status = true;
    
    public $statusLabel = 'Show event';

    #[Computed]
    public function allCategories()
    {
        return \App\Models\Category::all();
    }

    // Change status label text based on switch value
    function statusSwitch()
    {
        if($this->status) $this->statusLabel = 'Show event';
        else $this->statusLabel = 'Hide event';
    }

    // Process the form data upon button click
    function save(EventService $service)
    {

        // Validate all form fields
        $validated = $this->validate(
            [ 
                'title' => 'required|min:3',
                'category_id' => 'required',
                'image' => 'nullable|max:2048',
                'start_time' => 'required',
                'content' => 'required',
                'status' => 'nullable'
            ],
            [
                'title.required' => 'Een titel is verplicht',
                'title.min' => 'Een titel moet minimaal 3 karakters hebben',
                'category_id.required' => 'Een categorie is verplicht',
                'image.max' => 'Afbeelding mag niet groter dan 2MB',
                'start_time.required' => 'Een start tijd is verplicht',
                'content.required' => 'Content is verplicht',
            ]
        );

        return $service->createEvent($validated, $this->image);

        return dd( $validated );
    }
};
?>

<div class="w-full sm:w-2xl mx-auto p-6">
    <section class="my-8">

        <h2 class="text-xl styling-h mb-8">
            <div class="flex items-center gap-2">
                <span><flux:icon.chevron-double-down variant="solid" class="size-6" /></span>
                <span>Create next event</span>
                
            </div>
        </h2>

        <form wire:submit.prevent="save">

            <!-- Title text input -->
            <div class="mb-6">
                <flux:input wire:model.live="title" type="text" label="Title" />
            </div>

            <!-- Category select options -->
            <div class="mb-6">
                <flux:select wire:model="category_id"
                    label="Category" 
                    placeholder="Choose a category"
                >
                    @foreach($this->allCategories as $category)
                        <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Image file upload -->
            <div class="mb-6">
                <flux:input type="file" wire:model="image" label="Image upload" class="mb-4"/>

                {{-- Show a loading state while the temp file is uploading --}}
                <div wire:loading wire:target="image" class="text-blue-500 text-xs mt-1">
                    Uploading image...
                </div>

                @if ($image)
                    {{-- Show the temporary preview --}}
                    <img src="{{ $image->temporaryUrl() }}" class="size-[50%] object-cover">
                @endif
            </div>

            <!-- Start_time date selector -->
            <div class="mb-6">
                <flux:input 
                    type="datetime-local" wire:model="start_time" label="Event Date" 
                />
            </div>

            <!-- Content trix-editor -->
            <div class="mb-6" >
                <label>Description</label>
                <div wire:ignore class="prose mt-2" 
                    x-data="{ content: @entangle('content') }"
                    {{-- Make sure to wait until trix is fully updated --}}
                    @trix-change="$nextTick(() => { content = $event.target.value })"
                >
                    <input id="trix_input" type="hidden">
                    <trix-editor wire:model="content"
                        x-ref="trix"
                        input="trix_input" 
                        {{-- Only load HTML once on init to avoid loops --}}
                        x-init="$refs.trix.editor.loadHTML(content)"
                    >
                    </trix-editor>
                </div>
                @error('content')
                    <flux:error name="content">
                        {{ $message }}
                    </flux:error>
                @enderror
            </div>

            <!-- Status switch -->
            <div class="mb-6 w-full sm:w-[50%]">
                <flux:switch wire:model="status" wire:click="statusSwitch"
                    label="{{ $statusLabel }}" 
                    description="Make this event visible or invisible for the public"
                />
            </div>

            <!-- Submit button -->
            <flux:button type="submit" class="cursor-pointer color-sub transition delay-2s mt-6">
                <div class="flex items-center gap-x-1">
                    <span>Create event</span>
                    <flux:icon.check class="size-4"/>
                </div>
            </flux:button>

        </form>

    </section>

    <script type="module">
        import { Editor } from 'https://esm.sh/@tiptap/core'
        import StarterKit from 'https://esm.sh/@tiptap/starter-kit'

        new Editor({
            element: document.querySelector('.element'),
            extensions: [StarterKit],
            content: '<p>Hello from CDN!</p>',
        })
    </script>

</div>