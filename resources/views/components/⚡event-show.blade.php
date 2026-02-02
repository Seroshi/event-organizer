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

      @can('update', $this->event)
      <section class="mb-6 text-sm">
			<p class="text-gray-400">Admin opties:</p>
			<div class="inline-block">
				<div class="flex justify-center rounded-md gap-1 p-1 bg-gray-700">
					<a href="{{ route('event.edit', $this->event->id) }}" class="flex gap-1 items-center bg-zinc-800 hover:bg-gray-500 transition delay-2s px-2 py-1 rounded-md">
						<flux:icon.pencil-square variant="solid" class="size-5" />
						<p>Bewerk deze</p>
					</a>
               <a href="{{ route('event.list') }}" class="flex gap-1 items-center bg-zinc-800 hover:bg-gray-500 transition delay-2s px-2 py-1 rounded-md">
						<flux:icon.list-bullet variant="solid" class="size-5" />
						<p>Zie alle evenementen</p>
					</a>
					<a href="{{ route('event.create') }}" class="flex gap-1 items-center bg-zinc-800 hover:bg-gray-500 transition delay-2s px-2 py-1 rounded-md">
						<flux:icon.plus variant="solid" class="size-4" />
						<p>Maak nieuwe aan</p>
					</a>
				</div>
			</div>
		</section>
      @endcan

      <!-- Breadcrumbs -->
      <section class="text-sm text-gray-400 flex gap-1 items-center mb-10">
         <a href="{{ route('event.index') }}" class="hover:text-gray-200">Evenementen</a>
         <flux:icon.chevron-right variant="solid" class="size-4" />
         <span class="text-gray-200">{{ $this->event->title }}</span>
      </section>

      <section>

         <!-- Banner image -->
         <div class="mb-6">
            @if($this->event->hasMedia('banners'))
            {{ $this->event->getFirstMedia('banners') }}
            @else
            <div class="bg-gray-600 flex items-center justify-center aspect-video w-full">
                <flux:icon.photo variant="solid" class="size-20 text-gray-400" />
            </div>
            @endif
         </div>

         <!-- Event title -->
         <p class="text-gray-400 font-light">#{{ $this->event->category?->name }}</p>
         <h2 class="text-2xl mb-6">{{ $this->event->title }}</h2>

         <div class="mb-6">
            <p class="color-main inline-block rounded-sm px-2 py-0.5">{{ $this->event->start_time->format('D d M Y') }}</p>
            <span wire:poll.60s class="text-gray-400 ml-2">{{ $this->countdown }}</span>
         </div>

         <div class="show-content">
            {!! $this->event->content !!}
         </div>

      </section>

   </div>
</div>