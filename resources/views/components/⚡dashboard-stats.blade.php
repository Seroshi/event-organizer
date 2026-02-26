<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Number;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

use App\Models\Event;
use App\Models\Statistic;
use App\Enums\UserRole;

new class extends Component
{
	use WithPagination;

	#[Url]
	public $order = 'desc';
	#[Url]
	public $column = '';
	public $iconShow = 'chevron-down';
	public $eventMax = false;

	public function mount(): void
	{
		if(auth()->user()->role == UserRole::User){
			abort(403, 'U heeft geen toegang tot deze pagina.');
		}
		// To check if events are more than 8 items
		if($this->allEvents->count() >= 8) $this->eventMax = true;
	}

	public function toggleSort($column): void
	{
		// Toggle sorting to desc as well as the icon
		if($this->order === 'asc'){
			$this->order = 'desc';
			$this->iconShow = 'chevron-down';
		}

		// Toggle sorting to asc as well as the icon
		elseif($this->order === 'desc' && $this->column === $column){
			$this->order = 'asc';
			$this->iconShow = 'chevron-up';
		}

		$this->column = $column;

		// Making sure the pagination pages are updated
		$this->resetPage(pageName: 'events');
	}

	#[Computed] 
	public function allEvents(): LengthAwarePaginator
	{
		// Limit paginated items shown per page
		$paginateLimit = 33;

		$role = auth()->user()->role;

		if( $role == UserRole::Admin ){
			$events = Event::query();
		}
		elseif( $role == UserRole::Organizer ){
			$events = auth()->user()->events();
		}

		// Get sorted events based on the amount views/likes of statistic
		if($this->column === 'views' || $this->column === 'likes'){
			return $events->orderBy(
				Statistic::select($this->column)
					->whereColumn('event_id', 'events.id')
					->limit(1), $this->order
				)
				->paginate($paginateLimit ?? 33, pageName: 'events');
		}
		// Get sorted events based on the creation date
		else{
			return $events->orderBy('created_at', $this->order)
			->paginate($paginateLimit ?? 33, pageName: 'events');
		}
	}

};

?>

<div class="w-full sm:w-[48.2%] flex flex-col">

   <!-- Profile: Show statistics-->
   <section class="h-auto sm:h-80">

      <h2 class="font-bold text-md text-gray-300 flex items-center justify-center gap-1 mb-1">
         <span>
            <flux:icon.presentation-chart-bar variant="outline" class="size-5" />
         </span>
         <span>Statistieken</span>
      </h2>

      <div class="text-sm bg-zinc-700 rounded-lg p-4 pr-2">
			<div class="flex justify-between gap-2 text-gray-400 border-b border-gray-300 pb-1 mb-1">
				<div class="w-5">
					<span>nr.</span>
				</div>
				<div wire:click="toggleSort('date')" class="w-[60%] flex gap-1 hover:text-gray-100 items-center cursor-pointer">
					<span>startdatum</span>
					@if($this->column === 'date' || !$this->column)
					<flux:icon :icon="$this->iconShow" variant="outline" class="size-4" />
					@else
					<flux:icon.chevron-up-down variant="outline" class="size-4" />
					@endif
				</div>
				<div class="flex gap-2">
					<div wire:click="toggleSort('views')" class="w-12 flex gap-0.5 hover:text-gray-100 items-center cursor-pointer">
						<flux:icon.eye variant="outline" class="size-4" />
						@if($this->column === 'views')
						<flux:icon :icon="$this->iconShow" variant="outline" class="size-4" />
						@else
						<flux:icon.chevron-up-down variant="outline" class="size-4" />
						@endif
					</div>
					<div wire:click="toggleSort('likes')" class="w-12 flex gap-0.5 hover:text-gray-100 items-center cursor-pointer">
						<flux:icon.heart variant="outline" class="size-4" />
						@if($this->column === 'likes')
						<flux:icon :icon="$this->iconShow" variant="outline" class="size-4" />
						@else
						<flux:icon.chevron-up-down variant="outline" class="size-4" />
						@endif
					</div>
				</div>
			</div>
         <div class="h-105 sm:h-48.5 overflow-y-auto hide-scrollbar-until-hover">
				@forelse($this->allEvents as $index => $event)
				<div class="flex justify-between gap-2 pb-2 sm:pb-1 {{($event->is_still_active) ? 'text-gray-100' : 'text-gray-300'}} ">
					<span class="w-5">{{ $index + 1 }}.</span>
					<a href="{{ route('event.show', $event->id) }}" class="line-clamp-1 w-[60%] hover:text-gray-400">
						<span class="inline-flex whitespace-nowrap bg-teal-700 rounded-md px-1 w-13 py-0.3">
							{{ $event->start_time->format('d M') }}
						</span>
						<span class="pl-0.5">{{ $event->title }}</span>
					</a>
					<div class="flex gap-2">
						<p class="w-12 flex gap-1 items-center">
							<flux:icon.eye variant="outline" class="size-4" />
							<span>
							{{ $event->statistic?->viewsFormatted }}
							</span>
						</p>
						<p class="w-12 flex gap-1 items-center">
							<flux:icon.heart variant="outline" class="size-4" />
							<span>
								{{ $event->statistic?->likesFormatted }}
							</span>
						</p>
					</div>
				</div>
				@empty
				<p class="text-gray-300 mt-1">Geen evenement nog om te tonen.</p>
				@endforelse
        	</div>
		</div>

		<div class="mt-2">{{ $this->allEvents()->links(data: ['scrollTo' => false]) }}</div>

	</section>

</div>