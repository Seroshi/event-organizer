<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Services\EventService;
use App\Models\Event;

new class extends Component
{
    use WithFileUploads;

    // Form fields starting value
    public $title = "";
    public $category_id = "";
    public $image = null;
    public $start_time = null;
    public $content = "";
    public $status = true;
    
    public ?Event $event = null;
    public $statusLabel = 'Show event';
    private $action = '';

    public function mount(Event $event){
        if($event->exists){
            $this->event = $event;
            $this->title = $event->title;
            $this->category_id = $event->category_id;
            $this->start_time = $event->start_time->format('Y-m-d\TH:i');
            $this->content = $event->content;
            $this->status = (bool) $event->status;
        }
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

        // If no event is found, create a new one
        if(!$this->event) $service->createEvent($validated, $this->image);

        // Else update the existing event
        else $service->updateEvent($this->event, $validated);
        
        return dd( 'Event created!' );
    }

    #[Computed]
    public function allCategories(){ 
        return \App\Models\Category::all(); 
    }

    #[Computed]
    public function title(){ 
        return $this->event ? 'Bewerk evenement' : 'Creëer" evenement'; 
    }

    #[Computed]
    public function statusLabel(){
        return $this->status ? 'Evenement zichtbaar' : 'Evenement verborgen';
    }

    #[Computed]
    public function formButton(){ 
        return $this->event ? 'Update evenement' : 'Creëer evenement'; 
    }
};
?>

<div class="w-full sm:w-2xl mx-auto p-6">
    <section class="my-8">

        <h2 class="text-xl styling-h mb-8">
            <div class="flex items-center gap-2">
                <span><flux:icon.chevron-double-down variant="solid" class="size-6" /></span>
                <span>{{ $this->title() }}</span>
            </div>
        </h2>

        <form wire:submit.prevent="save">

            <!-- Title text input -->
            <div class="mb-6">
                <flux:input wire:model.live="title" type="text" label="Titel" />
            </div>

            <!-- Category select options -->
            <div class="mb-6">
                <flux:select wire:model="category_id"
                    label="Categorie" 
                    placeholder="Kies een categorie"
                >
                    @foreach($this->allCategories as $category)
                        <flux:select.option value="{{ $category->id }}">
                            {{ $category->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Image file upload -->
            <div class="mb-6">
                <flux:input type="file" wire:model="image" label="Afbeelding uploaden" class="mb-4"/>

                {{-- Show a loading state while the temp file is uploading --}}
                <div wire:loading wire:target="image" class="text-blue-500 text-xs mt-1">
                    Upload afbeelding...
                </div>

                @if ($image)
                    {{-- Show the temporary preview --}}
                    <img src="{{ $image->temporaryUrl() }}" class="size-[50%] object-cover">
                @endif
            </div>

            <!-- Start_time date selector -->
            <div class="mb-6">
                <flux:input 
                    type="datetime-local" wire:model="start_time" label="Begintijd" 
                />
            </div>

            <!-- Content trix-editor -->
            <div class="mb-6" >
                <label>Beschrijving</label>
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
                <flux:switch wire:model.live="status"
                    :label="$this->statusLabel()" 
                    description="Maak dit evenement zichtbaar of verborgen voor het publiek"
                />
            </div>

            <!-- Submit button -->
            <flux:button type="submit" class="cursor-pointer color-sub transition delay-2s mt-6">
                <div class="flex items-center gap-x-1">
                    <span>{{ $this->formButton() }}</span>
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