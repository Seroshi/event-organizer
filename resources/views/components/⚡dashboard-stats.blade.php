<?php

use Illuminate\Pagination\LengthAwarePaginator;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

use App\Models\Event;
use App\Models\Statistic;

new class extends Component
{
	use WithPagination;

	#[Url]
	public $order = 'desc';
	#[Url]
	public $column = '';
	public $iconShow = 'chevron-down';

	public function toggleSort($column): void
	{
		// Toggle sorting to desc as well as the icon
		if($this->order === 'asc'){
			$this->order = 'desc';
			$this->iconShow = 'chevron-down';
		}
		// Toggle sorting to asc as well as the icon
		elseif($this->order === 'desc' && $this->column == $column){
			$this->order = 'asc';
			$this->iconShow = 'chevron-up';
		}
		$this->column = $column;
		$this->resetPage(pageName: 'events');
	}

	#[Computed] 
	public function allEvents(): LengthAwarePaginator
	{
	   // Get useer ID
		$userId = auth()->user()?->id;

		// Limit paginated items shown per page
		$paginateLimit = 33;

		// Get sorted events based on the amount views/likes of statistic
		if($this->column === 'views' || $this->column === 'likes'){
			return $events = Event::where('user_id', $userId)
				->orderBy(
					Statistic::select($this->column)
						->whereColumn('event_id', 'events.id')
						->limit(1), $this->order
				)
				->paginate($paginateLimit, pageName: 'events');
		}
		// Get sorted events based on the creation date
		else{
			return $events = Event::where('user_id', $userId)
			->orderBy('created_at', $this->order)
			->paginate($paginateLimit, pageName: 'events');
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

      <div class="text-lg sm:text-sm bg-zinc-700 rounded-lg p-4">
         <div class="sm:h-55.5">
				<div class="flex justify-between gap-2 border-b border-gray-300 pb-1 mb-1">
					<div class="w-5">
						<span>nr.</span>
					</div>
					<div wire:click="toggleSort('date')" class="w-[60%] flex gap-1 items-center cursor-pointer">
						<span>aangemaakt</span>
						@if($this->column === 'date' || !$this->column)
						<flux:icon :icon="$this->iconShow" variant="outline" class="size-4" />
						@else
						<flux:icon.chevron-up-down variant="outline" class="size-4" />
						@endif
					</div>
					<div class="flex gap-2">
						<div wire:click="toggleSort('views')" class="w-12 flex gap-0.5 items-center cursor-pointer">
							<flux:icon.eye variant="outline" class="size-4" />
							@if($this->column === 'views')
							<flux:icon :icon="$this->iconShow" variant="outline" class="size-4" />
							@else
							<flux:icon.chevron-up-down variant="outline" class="size-4" />
							@endif
						</div>
						<div wire:click="toggleSort('likes')" class="w-12 flex gap-0.5 items-center cursor-pointer">
							<flux:icon.heart variant="outline" class="size-4" />
							@if($this->column === 'likes')
							<flux:icon :icon="$this->iconShow" variant="outline" class="size-4" />
							@else
							<flux:icon.chevron-up-down variant="outline" class="size-4" />
							@endif
						</div>
					</div>
				</div>
				@foreach($this->allEvents as $index => $event)
				<div class="flex justify-between gap-2">
					<span class="w-5">{{ $index + 1 }}.</span>
					<p class="line-clamp-1 w-[60%]">{{ $event->title }}</p>
					<div class="flex gap-2">
						<p class="w-12 flex gap-1 items-center">
							<flux:icon.eye variant="outline" class="size-4" />
							<span>{{ $event->statistic?->views }}</span>
						</p>
						<p class="w-12 flex gap-1 items-center">
							<flux:icon.heart variant="outline" class="size-4" />
							<span>{{ $event->statistic?->likes }}</span>
						</p>
					</div>
				</div>
				@endforeach
        	</div>
		</div>

		<div class="mt-2">{{ $this->allEvents()->links() }}</div>

	</section>

</div>