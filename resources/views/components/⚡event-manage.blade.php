<?php

use Illuminate\Support\Collection;

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
    public $end_time = null;
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
            $this->start_time = $event->start_time?->format('Y-m-d\TH:i');
            $this->end_time = $event->end_time?->format('Y-m-d\TH:i');
            $this->content = $event->content;
            $this->status = (bool) $event->status;
        }

    }

    // Process the form data upon button click
    function save(EventService $service): void
    {
        // Validate all form fields
        $validated = $this->validate(
            [ 
                'title' => 'required|min:3',
                'category_id' => 'required',
                'image' => 'nullable|max:2048',
                'start_time' => 'required|date', 
                'end_time' => 'nullable|date|after:start_date',
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

        // If no existing event is found from the route
        if(!$this->event){

            // Create the event
            $newEvent = $service->createEvent($validated);

            // Upload the image if provided by the user
            if($this->image) $service->uploadImage($newEvent, $this->image);

            // Dispatch a success notification
            session()->flash('success', 'Evenement succesvol aangemaakt!');

            $this->redirectRoute('event.list', navigate: true);
            return;
        } 

        else{

            // update the existing event
            $newEvent = $service->updateEvent($this->event, $validated);

            // Upload image if provided by the user
            if($this->image) $service->uploadImage($newEvent, $this->image);

            // Dispatch a success notification
            session()->flash('success', 'Evenement succesvol aangepast!');

            $this->redirectRoute('event.show', $newEvent->id, navigate: true);
            return;
        } 
    }

    #[Computed]
    public function allCategories(): Collection
    { 
        return \App\Models\Category::all(); 
    }

    #[Computed]
    public function title(): string
    { 
        return $this->event ? 'Bewerk deze evenement' : 'Evenement aanmaken'; 
    }

    #[Computed]
    public function titleIcon(): string
    { 
        return $this->event ? 'pencil-square' : 'plus'; 
    }

    #[Computed]
    public function statusLabel(): string
    {
        return $this->status ? 'Evenement zichtbaar' : 'Evenement verborgen';
    }

    #[Computed]
    public function formButton(): string
    { 
        return $this->event ? 'Update evenement' : 'Creëer evenement'; 
    }
};
?>

<div class="w-full sm:w-2xl mx-auto p-6">
    <section class="my-4">

        <!-- Breadcrumbs -->
        <section class="text-sm text-gray-400 flex gap-1 items-center mb-10">
            <a href="{{ route('event.index') }}" class="hover:text-gray-200">Evenementen</a>
            <flux:icon.chevron-right variant="solid" class="size-4" />
            <span class="text-gray-200">{{ $this->title() }}</span>
        </section>

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

            <!-- Start & End date selector -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row gap-x-2 gap-y-3">
                    <span class="flex-1">
                        <flux:input type="datetime-local" wire:model.live="start_time" label="Begintijd" />
                    </span>
                    <span class="flex-1">
                        <flux:input type="datetime-local" min="{{ $this->start_time }}" wire:model="end_time" label="Eindtijd" />
                    </span>
                </div>
            </div>

            <!-- Image file upload -->
            <div class="mb-6">
                <label class="font-medium">Afbeelding uploaden</label>
                @if($this->event?->hasMedia('banners') && !$this->image)
                <div class="sm:w-[50%] pt-2 pb-3">
                    {{ $this->event->getFirstMedia('banners') }}
                </div>
                @endif
                <flux:input type="file" wire:model="image" class="mt-2 mb-4"/>

                {{-- Show a loading state while the temp file is uploading --}}
                <div wire:loading wire:target="image" class="text-blue-500 text-xs mt-1">
                    Upload afbeelding...
                </div>

                @if ($image)
                    {{-- Show the temporary preview --}}
                    <img src="{{ $image->temporaryUrl() }}" class="size-[50%] object-cover">
                @endif
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

</div>