<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

use App\Models\Event;
use App\Models\User;
use App\Models\Statistic;
use App\Services\EventService;

new class extends Component
{
	use WithPagination;

	#[Computed] 
	public function allEvents(): LengthAwarePaginator
	{
		$paginateLimit = 33;

		$userId = auth()->user()->id;

		// Get all event ids liked by this user
		$groupId = Statistic::where('user_likes', 'LIKE', '%"userId":' . $userId . '%')
    		->orWhere('user_likes', 'LIKE', '%"userId": ' . $userId . '%')
			->orderBy('updated_at', 'desc')
    		->pluck('event_id');

		// Grab all user favourited events	
		return Event::whereIn('id', $groupId)
			->orderByRaw("CASE WHEN id IS NULL THEN 1 ELSE 0 END, CASE " . 
				$groupId->map(fn($id, $index) => "WHEN id = {$id} THEN {$index}")->implode(' ') . 
				" END")
			->paginate($paginateLimit, pageName: 'favourites');
	}
};
?>

<div class="w-full sm:w-[48.2%] flex flex-col">

   <!-- Profile: Show statistics-->
   <section class="h-auto sm:h-80">

      <h2 class="font-bold text-md text-gray-300 flex items-center justify-center gap-1 mb-1">
         <span>
            <flux:icon.heart variant="outline" class="size-5" />
         </span>
         <span>Favoriete evenementen</span>
      </h2>

      <div class="text-md sm:text-sm bg-zinc-700 rounded-lg p-2">
         <div class="h-125 sm:h-59.5 overflow-y-auto hide-scrollbar-until-hover">
			@foreach($this->allEvents as $event)
			<a href="{{ route('event.show', $event->id) }}" class="flex gap-2 hover:bg-gray-600 rounded-md cursor-pointer p-1">
				<div class="mr-1">
					<div class="bg-gray-600 flex items-center justify-center w-20 sm:w-15 aspect-square rounded-md overflow-hidden">
						@if($event->getFirstMediaUrl('banners'))
						<img 
							src="{{ $event->getFirstMediaUrl('banners', 'thumb') }}" 
							class="aspect-square object-cover" 
							alt="{{ $event->title }}"
						>
						@else
						<flux:icon.photo variant="solid" class="size-[40%] text-gray-400" />
						@endif
					</div>
				</div>
				<div class="content-center">
					<div class="flex gap-2">
						<div class="inline-flex whitespace-nowrap bg-teal-700 rounded-md px-2 py-0.3">
							{{ $event->start_time->format('d M') }}
						</div>
						<p class="text-gray-400 line-clamp-1">{{ $event->getSmartCountdown }}</p>
					</div>
					<p class="line-clamp-1 pt-1">
						{{ $event->title }}
					</p>
				</div>
			</a href="{{ route('event.show', $event->id) }}">
			@endforeach
			@if($this->allEvents()->count() == 0)
			<div class="text-gray-300 p-2">Je hebt op dit moment geen favorieten nog. </div>
			@endif
        	</div>
		</div>

		<div class="mt-2">{{ $this->allEvents->links(data: ['scrollTo' => false]) }}</div>

	</section>

</div>