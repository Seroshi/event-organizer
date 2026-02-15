<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

use App\Models\User;
use App\Models\Event;
use App\Enums\UserRole;

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
      $role = auth()->user()->role;

      // Grab all active events for admin users
      if( $role == UserRole::Admin ){
         $count = Event::isActive()->count();
      }

      // Grab user events for organizers
      elseif( $role == UserRole::Organizer ){
         $count = auth()->user()?->events->count();
      }

      return [
         'number'  => $count . ' ',
         'text' => ($count > 1) ? 'evenementen' : 'evenement',
      ];
   }
};
?>

<section class="w-full sm:w-[48.2%]">

   <div class="h-auto sm:h-80">
      <h2 class="font-bold text-md text-gray-300 flex items-center justify-center gap-1 mb-1">
         <span>
            <flux:icon.calendar variant="outline" class="size-6" />
         </span>
         <span>Evenement opties</span>
      </h2>
      <div class="text-lg sm:text-sm bg-zinc-700 rounded-lg py-4 px-4">
         <div class="sm:h-55.5 flex flex-col gap-3">
            
            @admin
            <!-- Counter of all made events -->
            @if($this->eventCount['number'] >= 6)
            <div class="bg-emerald-600 rounded-md p-3">
               <p>Er zijn momenteel 
                  <span class="text-md font-bold px-1">
                     {{ $this->eventCount['number'] }} 
                  </span> 
                  {{ $this->eventCount['text'] }} actief
               </p>
            </div>
            @else
            <div class="bg-red-500 rounded-md p-3">
               <p>Er zijn maar 
                  <span class="text-md font-bold px-1">
                     {{ $this->eventCount['number'] }} 
                  </span> 
                  {{ $this->eventCount['text'] }} actief
               </p>
            </div>
            @endif
            @endadmin

            @organizer
            <!-- Counter of all user's made events -->
            <div class="color-sub rounded-md p-3">
               <p>Je hebt in totaal 
                  <span class="text-md font-bold px-1">
                     {{ $this->eventCount['number'] }} 
                  </span> 
                  {{ $this->eventCount['text'] }} aangemaakt!
               </p>
            </div>
            @endorganizer
            <a href="{{ route('event.list') }}" class="w-fit flex gap-1 items-center bg-gray-500 hover:bg-gray-400 transition delay-2s px-2 py-1 rounded-xl">
               <flux:icon.list-bullet variant="solid" class="size-4" />
               <p>Mijn evenementen</p>
            </a>
            <a href="{{ route('event.create') }}" class="w-fit flex gap-1 items-center bg-gray-500 hover:bg-gray-400 transition delay-2s px-2 py-1 rounded-xl">
               <flux:icon.plus variant="solid" class="size-4" />
               <p>Maak een nieuwe aan</p>
            </a>
         </div>
      </div>
   </div>

</section>