<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Services\EventService;
use App\Models\Event;

new class extends Component
{
   public $page = '';

   public function mount(Request $request)
   {
      // Get page parameter from request
      $this->page = $request->page;
   }

   public function delete(Event $event, EventService $service): RedirectResponse
   {
      $force = false;

      $deleted = $service->delete($event, $force);

      if($deleted) {
         session()->flash('success', 'Evenement is verwijderd.');
      } else {
         session()->flash('error', 'Evenement kon niet verwijderd worden.');
      }

      return $this->redirectRoute('event.list', navigate:true);
      // return redirect()->route('event.list');
   }

   public function restore($id, EventService $service): RedirectResponse
   {
      $event = Event::withTrashed()->findOrFail($id);

      $restored = $service->restore($event);

      if($restored){
         session()->flash('success', 'Evenement is hersteld.');
      }else{
         session()->flash('error', 'Evenement kon niet hersteld worden.');
      }

      return $this->redirectRoute('event.list', ['page' => 'trash'], navigate:true);
   }

   #[Computed]
   public function events(): Collection
   {
      $userId = auth()->user()?->id;

      if($this->page == 'trash'){
         try{
            return Event::where('user_id', $userId)
               ->with('media')->onlyTrashed()
               ->orderBy('updated_at', 'asc')
               ->get();
         } catch (\Exception $e){
            report($e);
            session()->flash('error', 'De prullenbak events kon niet worden geladen.');
            return collect();
         }
      }else{
         try{
            return Event::where('user_id', $userId)
               ->with('media')
               ->orderBy('updated_at', 'asc')
               ->get();
         } catch (\Exception $e){
            report($e);
            session()->flash('error', 'De events kon niet worden geladen.');
            return collect();
         }
      }
   }

   #[Computed]
   public function emptyText(): String
   {
      return ($this->page == 'trash') ? 'De prullenbak is leeg, mooi dat je het schoon houdt.' : 'Geen evenementen gevonden.';
   }

};
?>

<div class="w-full p-8">
   <div class="my-4">

      <!-- Breadcrumbs -->
      <section class="text-sm text-gray-400 flex gap-1 items-center mb-2">
         <a href="{{ route('event.index') }}" class="hover:text-gray-200">Evenementen</a>
         <flux:icon.chevron-right variant="solid" class="size-4" />
         <a href="{{ route('event.list') }}" class="{{ ($this->page == 'trash') ? 'hover:text-gray-200' : 'text-gray-200' }}">Mijn evenementen</a>
         @if($this->page == 'trash')
         <flux:icon.chevron-right variant="solid" class="size-4" />
         <span class="text-gray-200">Evenementen prullenbak</span>
         @endif
      </section>

      <div class="mb-10">
         <a href="{{ route('event.list', ['page' => 'trash']) }}" class="gap-2 text-sm bg-zinc-700 hover:brightness-130 inline-block rounded-md px-3 py-1">
            <div class="flex items-center gap-1">
               <flux:icon.trash variant="outline" class="size-4 text-gray-200" />
               <span>Prullenbak</span>
            </div>
         </a>
      </div>

      <section class="w-full overflow-x-auto hide-scrollbar-until-hover">
         <div class="min-w-3xl">
            <!-- Header -->
            <div class="flex gap-1 w-full font-bold rounded-md bg-gray-500 px-2 py-1 mb-1">
               <p class="w-7">ID</p>
               <p class="w-40 md:w-30 mr-2 text-center">Afbeelding</p>
               <p class="w-80">Titel</p>
               <p class="w-30">Bewerkt</p>
               <p class="w-30">Gemaakt</p>
               <p class="w-20 text-center">Status</p>
               <p class="w-70 md:w-50 text-center">Opties</p>
            </div>
            @foreach($this->events as $event)
            <div class="flex gap-1 w-full px-2 py-1">

               <!-- Event ID -->
               <div class="flex items-center w-7">{{ $event->id }}</div>

               <!-- Event image -->
               <div class="flex items-center w-40 md:w-30 mr-2">
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
                        <flux:icon.check-circle variant="outline" class="size-10 md:size-5 text-blue-400" />
                     </div>
                     @else 
                     <div title="offline">
                        <flux:icon.minus-circle variant="outline" class="size-10 md:size-5 text-gray-400" />
                     </div>
                     @endif
                  </div>
               </div>

               <!-- Event Opties -->
               <div class="flex gap-2 items-center justify-center flex-wrap md:flex-nowrap w-70 md:w-50">
                  @if($this->page == 'trash')
                  <a wire:click="restore({{ $event->id }})" title="Herstellen" class="color-sub px-3 py-2 rounded-md">
                     <flux:icon.arrow-left-start-on-rectangle variant="outline" class="size-7 md:size-4 text-white" />
                  </a>
                  @else
                  <a href="{{ route('event.edit', $event->id) }}" title="Bewerk" class="color-sub px-3 py-2 rounded-xl md:rounded-md">
                     <flux:icon.pencil-square variant="solid" class="size-7 md:size-4 text-white" />
                  </a>
                  <a href="{{ route('event.show', $event->id) }}" title="Bekijken" class="bg-gray-500 hover:bg-gray-400 px-3 py-2 rounded-xl md:rounded-md">
                     <flux:icon.computer-desktop variant="solid" class="size-7 md:size-4 text-white" />
                  </a>
                  @endif
                  <a wire:click="delete({{ $event->id }})" title="Verwijderen" class="bg-gray-500 hover:bg-gray-400 px-3 py-2 rounded-xl md:rounded-md">
                     <flux:icon.x-mark variant="outline" class="size-7 md:size-4 text-white" />
                  </a>
               </div>

            </div>
            @endforeach
         </div>
         @if($this->events->count() == 0)
         <div class="flex items-center gap-2 mt-2">
            <flux:icon.information-circle variant="outline" class="size-7 md:size-10 text-[#49d144]" />
            <p>{{ $this->emptyText }}</p>
         </div>
         @endif
      </section>

   </div>
</div>