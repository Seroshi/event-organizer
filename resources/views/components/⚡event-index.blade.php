<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Services\EventService;
use App\Models\Event;

new class extends Component
{
	#[Computed]
	public function events(){
		return Event::all();
	}

	public function countdown($event){
		return app(EventService::class)->getSmartCountdown($event);
	}
};
?>


<div class="w-full md:w-3xl mx-auto p-6">
   <div class="my-8">

		{{-- Admin quick options --}}
		<section class="mb-6">
			<p class="text-sm text-gray-300">Admin opties:</p>
			<div class="flex gap-3 mt-1">
				<a href="{{ route('event.list') }}" class="flex gap-2 items-center bg-gray-700 hover:brightness-130 transition delay-2s px-3 py-1 rounded-md">
					<flux:icon.list-bullet variant="solid" class="size-5" />
					<p>Bekijk alle</p>
				</a>
				<a href="{{ route('event.create') }}" class="flex gap-2 items-center bg-gray-700 hover:brightness-130 transition delay-2s px-3 py-1 rounded-md">
					<flux:icon.plus variant="solid" class="size-4" />
					<p>Maak nieuwe aan</p>
				</a>
			</div>
		</section>

		<section>

			<!-- Events: title -->
			<h2 class="text-xl styling-h mb-8">
				<div class="flex items-center gap-2">
					<span><flux:icon.calendar variant="solid" class="size-6" /></span>
					<span>Aankomende evenementen</span>
				</div>
			</h2>

			<!-- Events: All -->
			<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-2">

				<!-- Events: Collection -->
				@foreach($this->events as $event)
				<a href="{{ route('event.show', $event->id) }}" class="group">
					<div class="border rounded-lg cursor-pointer overflow-hidden transition delay-2s group-hover:bg-gray-800">
						<div class="bg-gray-600 flex items-center justify-center aspect-3/2 w-full">
							<!-- <img src="{{ asset('storage/'.$event->image) }}" 
								alt="{{ $event->title }}" class="w-full h-48 object-cover rounded-md mb-4" 
							/> -->
							<flux:icon.photo variant="solid" class="size-20 text-gray-400" />
						</div>
						<div class="p-4 text-center">
							<p class="text-xs text-gray-400">#{{ $event->category->name }}</p>
							<h2 class="text-md font-semibold line-clamp-1 mb-1">{{ $event->title }}</h2>
							<p wire:poll.60s class="text-sm">{{ $this->countdown($event) }}</p>
						</div>
					</div>
				</a>
				@endforeach

			</div>
		</section>

	</div>
</div>