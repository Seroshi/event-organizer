<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Services\EventService;
use App\Models\Event;

new class extends Component
{

   public function mount(){
      // $this->event = $event->orderBy;
   }

   #[Computed]
   public function events(){
      return Event::all()->sortBy('updated_at');
   }
};
?>

<div class="w-full md:w-3xl mx-auto p-8">
   <div class="my-8">

      <section>
			<h2 class="text-xl styling-h mb-8">
				<div class="flex items-center gap-2">
					<span><flux:icon.calendar variant="solid" class="size-6" /></span>
					<span>Alle evenementen</span>
				</div>
			</h2>
         <!-- Header -->
         <div class="flex gap-1 w-full font-bold bg-gray-600 px-2 py-1">
            <p class="w-7">ID</p>
            <p class="w-80">Titel</p>
            <p class="w-30">Bewerkt</p>
            <p class="w-30">Gemaakt</p>
            <p class="w-30">Opties</p>
         </div>
         @foreach($this->events as $event)
         <div class="flex gap-1 w-full px-2 py-1">
            <div class="flex items-center w-7">{{ $event->id }}</div>
            <div class="flex items-center w-80">{{ $event->title }}</div>
            <div class="w-30">
               <span>{{ $event->updated_at->format('d-m-y') }}</span>
               <div class="flex gap-1 items-center">
                  <flux:icon.clock variant="solid" class="size-4 text-gray-400" />
                  <span>{{ $event->updated_at->format('H:i') }}</span>
               </div>
            </div>
            <div class="w-30">
               <span>{{ $event->created_at->format('d-m-y') }}</span>
               <div class="flex gap-1 items-center">
                  <flux:icon.clock variant="solid" class="size-4 text-gray-400" />
                  <span>{{ $event->created_at->format('H:i') }}</span>
               </div>
            </div>
            <div class="flex gap-2 items-center w-30">
               <a href="{{ route('event.edit', $event->id) }}" title="Bewerk" class="color-sub px-2 py-1 rounded-md">
                  <flux:icon.pencil-square variant="solid" class="size-4 text-white" />
               </a>
               <a href="{{ route('event.show', $event->id) }}" title="Bekijk" class="bg-gray-500 hover:bg-gray-400 px-2 py-1 rounded-md">
                  <flux:icon.eye variant="solid" class="size-4 text-white" />
               </a>
            </div>
         </div>
         @endforeach
      </section>

   </div>
</div>