<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Services\EventService;
use App\Models\Event;
use App\Models\Profile;

new class extends Component
{
   public $event = null;
   public $profile = null;

   public function mount(Event $event): void
   {
      if(!$event) abort(404);
      $this->event = $event;

      $userId = $event->user_id;
      $user = Profile::where('user_id', $userId)->first();
      ($user) ? $this->profile = $user : null;
   }

   // Display the organizer or company name
   #[Computed]
   public function organizerName(): string
   {
      $company = $this->profile->company;
      return ($company) ? $company : $this->profile->name;
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
						<p>Mijn evenementen</p>
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
         <span class="text-gray-200 line-clamp-1">{{ $this->event->title }}</span>
      </section>

      <section>
         <!-- Banner image -->
         <div class="mb-4 overflow-hidden rounded-md">
            @if($this->event->hasMedia('banners'))
            {{ $this->event->getFirstMedia('banners') }}
            @else
            <div class="bg-gray-600 flex items-center justify-center aspect-video w-full">
               <flux:icon.photo variant="solid" class="size-20 text-gray-400" />
            </div>
            @endif
         </div>

         <!-- Event statistics -->
         <div class="mb-6">
            <livewire:statistic :eventId="$this->event->id"/>
         </div>

         <!-- Event title -->
         <p class="text-gray-400 font-light">#{{ $this->event->category?->name }}</p>
         <h2 class="text-2xl mb-6">{{ $this->event->title }}</h2>

         <!-- Event data & countdown -->
         <div class="mb-6">
            @if(!$this->event->end_time || $this->event->start_time->format('dm') === $this->event->end_time->format('dm'))
            <p class="color-main inline-block rounded-sm px-2 py-0.5">
               {{ $this->event->start_time->format('D d M Y') }}
            </p>
            @else
            <span class="color-main inline-block rounded-sm px-2 py-0.5">
               {{ $this->event->start_time->format('D d M') }}
               <span> - </span>
               {{ $this->event->end_time->format('D d M Y') }}
            </span>
            @endif
            <span wire:poll.60s class="text-gray-400 ml-2">{{ $this->event->getSmartCountdown }}</span>
            <div class="text-lg pt-1 flex items-center gap-1">
               <flux:icon.clock variant="outline" class="size-6 text-gray-400" />
               <span>{{ $this->event->start_time->format('H:i') }}</span>
            </div>
         </div>

         <!-- Event content -->
         <div class="show-content">
            {!! $this->event->content !!}
         </div>
      </section>

      <section class="border-t border-gray-400 mt-10">
         <div class="flex items-center gap-4 mt-4">
            <div class="w-35 sm:w-25 aspect-square rounded-full flex items-center overflow-hidden justify-center bg-zinc-500 border-3 border-gray-300 inset-shadow-zinc-300">
               @if($this->profile?->hasMedia('profiles'))
               <img src="{{ $this->profile->getFirstMediaUrl('profiles', 'thumb') }}" 
                  class="aspect-square object-cover" 
                  alt="profile-image"
               >
               @else
               <flux:icon.user-circle variant="solid" class="size-26 text-gray-400" />
               @endif
            </div>
            <div>
               <p>Georganiseerd door</p>
               <p class="text-lg w-fit flex color-sub rounded-md px-4 py-0.5 mt-1">
                  {{ $this->organizerName }}
               </p>
            </div>
         </div>
      </section>

   </div>
</div>