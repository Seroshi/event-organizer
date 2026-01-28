<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Services\EventService;
use App\Models\Event;

new class extends Component
{
    public $event = null;

    public function mount(Event $event){
        $this->event = $event;
    }

    #[Computed]
    public function countdown(){
		return app(EventService::class)->getSmartCountdown($this->event);;
	}
};
?>

<div class="w-full md:w-2xl mx-auto p-6">
   <div class="my-8">

      <section class="mb-6">
			<p class="text-sm text-gray-300">Admin opties:</p>
			<div class="flex gap-3 mt-1">
				<a href="{{ route('event.edit', $this->event->id) }}" class="flex gap-2 items-center bg-gray-700 hover:brightness-130 transition delay-2s px-3 py-1 rounded-md">
					<flux:icon.pencil-square variant="solid" class="size-4" />
					<p>Bewerk deze</p>
				</a>
			</div>
		</section>

      <!-- Breadcrumbs -->
      <section class="text-sm text-gray-400 flex gap-1 items-center mb-4">
         <a href="{{ route('event.index') }}" class="hover:text-gray-200">Evenementen</a>
         <flux:icon.chevron-right variant="solid" class="size-4" />
         <span class="text-gray-200">{{ $this->event->title }}</span>
      </section>

      <section>

         <!-- Banner image -->
         <div class="mb-4">
            @if($this->event->image)
            <img src="" alt="">testing
            @else
            <div class="bg-gray-600 flex items-center justify-center aspect-video w-full">
                <flux:icon.photo variant="solid" class="size-20 text-gray-400" />
            </div>
            @endif
         </div>

         <!-- Event title -->
         <h2 class="text-2xl">{{ $this->event->title }}</h2>
         <p class="text-gray-400 font-light mb-1">#{{ $this->event->category?->name }}</p>
         
         <span class="color-main inline-block rounded-sm px-2 py-0.5">{{ $this->event->start_time->format('D d M Y') }}</span>
         <span wire:poll.60s class="text-gray-400 ml-2">{{ $this->countdown }}</span>

      </section>

   </div>
</div>