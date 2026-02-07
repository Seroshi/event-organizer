<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Collection;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Livewire\Actions\Logout;
use App\Services\EventService;
use App\Services\DashboardService;
use App\Models\Event;
use App\Models\User;
use App\Enums\UserRole;

use Illuminate\Support\Str;

new class extends Component
{
   public function mount(): void
   {
      // Assign role options from the enum static function
      $this->roleOptions = UserRole::options();
   }

   // For displaying the event counter text
   #[Computed] 
   public function eventCount(): array
   {
      $count = auth()->user()->events->count();
      return [
         'number'  => $count . ' ',
         'text' => ($count > 1) ? 'evenementen' : 'evenement',
      ];
   }

};
?>

<div class="w-full sm:w-2xl md:w-3xl lg:w-4xl mx-auto px-12 py-6">

   <div class="flex gap-5 items-center flex-col sm:flex-row">

      <livewire:dashboard-profile />

      @admin
      <livewire:dashboard-user />
      @endadmin

      <!-- Event options -->
      @organizer 
      <section class="w-full flex flex-col">
         <h2 class="font-bold text-md text-gray-300 flex items-center justify-center gap-1 mb-1">
            <span>
               <flux:icon.calendar variant="outline" class="size-6" />
            </span>
            <span>Evenement opties</span>
         </h2>
         <div class="text-lg sm:text-sm bg-zinc-700 rounded-lg py-4 px-4">
            <div class="sm:h-55.5 flex flex-col gap-3">
               <div class="color-sub rounded-md p-3">
                  <p>Je hebt in totaal 
                     <span class="text-md font-bold px-1">{{ $this->eventCount['number'] }} </span> 
                     {{ $this->eventCount['text'] }} aangemaakt!</p>
               </div>
               <a href="{{ route('event.create') }}" class="w-fit flex gap-1 items-center bg-gray-500 hover:bg-gray-400 transition delay-2s px-2 py-1 rounded-xl">
						<flux:icon.plus variant="solid" class="size-4" />
						<p>Maak een nieuwe aan</p>
					</a>
               <a href="{{ route('event.list') }}" class="w-fit flex gap-1 items-center bg-gray-500 hover:bg-gray-400 transition delay-2s px-2 py-1 rounded-xl">
						<flux:icon.list-bullet variant="solid" class="size-4" />
						<p>Mijn evenementen</p>
					</a>
            </div>
         </div>
      </section>
      @endorganizer

   </div>

</div>