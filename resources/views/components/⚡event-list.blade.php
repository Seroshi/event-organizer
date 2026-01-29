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
      return Event::all()->sortByDesc('updated_at');
   }
};
?>

<div class="w-full md:w-3xl mx-auto p-8">
   <div class="my-8">

      <!-- Breadcrumbs -->
      <section class="text-sm text-gray-400 flex gap-1 items-center mb-10">
         <a href="{{ route('event.index') }}" class="hover:text-gray-200">Evenementen</a>
         <flux:icon.chevron-right variant="solid" class="size-4" />
         <span class="text-gray-200">Alle evenementen</span>
      </section>

      <section>
         <!-- Header -->
         <div class="flex gap-1 w-full font-bold bg-gray-600 px-2 py-1 mb-1">
            <p class="w-7">ID</p>
            <p class="w-30 mr-2">Image</p>
            <p class="w-80">Titel</p>
            <p class="w-30">Bewerkt</p>
            <p class="w-30">Gemaakt</p>
            <p class="w-20 text-center">Status</p>
            <p class="w-30 text-center">Opties</p>
         </div>
         @foreach($this->events as $event)
         <div class="flex gap-1 w-full px-2 py-1">

            <!-- Event ID -->
            <div class="flex items-center w-7">{{ $event->id }}</div>

            <!-- Event image -->
            <div class="flex items-center w-30 mr-2">
               <div class="bg-gray-600 flex items-center justify-center aspect-square rounded-md overflow-hidden w-full">
                  @if($event->getFirstMediaUrl('banners'))
                  <img 
                     src="{{ $event->getFirstMediaUrl('banners') }}" 
                     class="aspect-square object-cover" 
                     alt="{{ $event->title }}"
                  >
                  @else
                  <flux:icon.photo variant="solid" class="size-[40%] text-gray-400" />
                  @endif
               </div>
            </div>

            <!-- Event updated -->
            <div class="flex items-center w-80">{{ $event->title }}</div>
            <div class="w-30 flex items-center">
               <div>
                  <span>{{ $event->updated_at->format('d-m-y') }}</span>
                  <div class="flex gap-1 items-center">
                     <flux:icon.clock variant="solid" class="size-4 text-gray-400" />
                     <span>{{ $event->updated_at->format('H:i') }}</span>
                  </div>
               </div>
            </div>

            <!-- Event created  -->
            <div class="w-30 flex items-center">
               <div>
                  <span>{{ $event->created_at->format('d-m-y') }}</span>
                  <div class="flex gap-1 items-center">
                     <flux:icon.clock variant="solid" class="size-4 text-gray-400" />
                     <span>{{ $event->created_at->format('H:i') }}</span>
                  </div>
               </div>
            </div>

            <!-- Event status -->
            <div class="w-20 flex items-center justify-center">
               <div class="flex gap-1 items-center">
                  @if($event->status) 
                  <div title="online">
                     <flux:icon.check-circle variant="outline" class="size-5 text-blue-400" />
                  </div>
                  @else 
                  <div title="offline">
                     <flux:icon.minus-circle variant="outline" class="size-5 text-gray-400" />
                  </div>
                  @endif
               </div>
            </div>

            <!-- Event Opties -->
            <div class="flex gap-2 items-center w-30">
               <a href="{{ route('event.edit', $event->id) }}" title="Bewerk" class="color-sub px-3 py-2 rounded-md">
                  <flux:icon.pencil-square variant="solid" class="size-4 text-white" />
               </a>
               <a href="{{ route('event.show', $event->id) }}" title="Bekijk" class="bg-gray-500 hover:bg-gray-400 px-3 py-2 rounded-md">
                  <flux:icon.computer-desktop variant="solid" class="size-4 text-white" />
               </a>
            </div>

         </div>
         @endforeach
      </section>

   </div>
</div>