<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Services\EventService;
use App\Models\Event;

new class extends Component
{
	#[Computed]
	public function events(){
		return Event::where('status', true)->get()->sortBy('start_time');
	}

	public function countdown($event){
		return app(EventService::class)->getSmartCountdown($event);
	}
};
?>


<div class="w-full md:w-3xl mx-auto p-6">
   <div class="my-8">

		{{-- Admin quick options --}}
		<section class="mb-6 text-center text-sm">
			<p class="text-gray-400">Admin opties:</p>
			<div class="inline-block">
				<div class="flex justify-center rounded-md gap-1 p-1 bg-gray-700">
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

		<section class="flex flex-col">

			<!-- Events: title -->
			<h2 class="text-xl styling-h mb-8 mx-auto text-center">
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
							@if($event->hasMedia('banners'))
							{{ $event->getFirstMedia('banners') }}
							@else
							<flux:icon.photo variant="solid" class="size-20 text-gray-400" />
							@endif
						</div>
						<div class="p-4 text-center">
							<p class="text-xs text-gray-400">#{{ $event->category->name }}</p>
							<h2 class="text-md font-semibold line-clamp-1 mb-1">{{ $event->title }}</h2>
							<div class="line-clamp-1">
								<span class="text-sm color-main rounded-md px-2 py-0.5 mr-2">{{ $event->start_time->format('d M') }}</span>
								<span wire:poll.60s class="text-sm">{{ $this->countdown($event) }}</span>
							</div>
						</div>
					</div>
				</a>
				@endforeach

			</div>
		</section>

	</div>
</div>