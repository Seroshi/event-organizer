<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Event;

new class extends Component
{
   use WithPagination;

   public $userId = null;
   public $userProfile = null;
   public $userRole = 'user';
   public $userName = '';
   public $roleOptions;

   public function mount(): void
   {
      // Assign role options from the enum static function
      $this->roleOptions = UserRole::options();
   }

   public function getUserData($id): void
   {
      // 1 Open up the main modal
      $this->dispatch('open-modal', name: 'edit-user-role');

      // 2 Find the corresponding user model
      $user = User::withCount('events')->findOrFail($id);

      // 3 Assign the user data to the form variables
      $this->userId = $user->id;
      $this->userProfile = $user->profile;
      $this->userName = $user->name;
      $this->userRole = $user->role->value;
   }
   
   // Form function upon button confirm
   public function save(): Void
   {
      $user = User::findOrFail($this->userId);
      try{
         $user->update([
         'role' => $this->userRole,
         ]);
         session()->flash('success', 'Gebruiker is succesvol aangepast.');
      }
      catch (\Exception $e){
         report($e);
         session()->flash('error', 'Gebruiker was niet gevonden.');
      }

      $this->redirectRoute('dashboard', navigate: true);

      return;
   }

   #[Computed]
   public function getUserRoles(): ?LengthAwarePaginator
   {
      // Decide pagination limit per page
      $paginateLimit = 33;

      try{
         return User::where('id', '!=', auth()->id())
            ->orderByRaw("FIELD(role, 'master', 'admin', 'organizer', 'user')")
            ->paginate($paginateLimit ?? 33, pageName: 'users');
      } catch (\Exception $e){
         report($e);
         session()->flash('error', 'De gebruikerslijst kon niet worden geladen.');
         return new LengthAwarePaginator([], 0, 33);
      }
   }

   // Helper function to retrieve the right data from the selected user role 
   #[Computed]
   public function getRoleData(): UserRole
   {
      // Convert a string into the Enum UserRole::case
      return UserRole::tryFrom( $this->userRole ?? 'user' );
   }

   // For displaying the user counter
   #[Computed] 
   public function userCount(): string
   {
      return User::all()->count() ?? '0';
   }

};
?>

<div class="w-full sm:w-[48.2%]">

   <section class="h-auto sm:h-80">
      <h2 class="font-bold text-md text-gray-300 flex items-center justify-center gap-1 mb-1">
         <span>
            <flux:icon.users variant="outline" class="size-5" />
         </span>
         <span>Alle gebruikers ({{ $this->userCount }})</span>
      </h2>
      <div class="text-md sm:text-sm bg-zinc-700 rounded-lg py-2 pl-2 pr-1">
         <div class="h-105 sm:h-59.5 overflow-y-auto hide-scrollbar-until-hover">
            @foreach($this->getUserRoles as $user)
            <a wire:click="getUserData({{ $user->id }})" wire:click.prevent href="#"
               class="flex justify-between rounded-md hover:bg-gray-600 cursor-pointer px-2 py-1 "
            >
               <p>{{ $user->name }}</p>
               <p class="{{ $user->role->labelColor() }} rounded-md px-2">
                  {{ strtolower($user->role->label()) }}
               </p>
            </a>
            @endforeach
         </div>
      </div>
      <div class="mt-2">{{ $this->getUserRoles()->links(data: ['scrollTo' => false]) }}</div>
   </section>

   <!-- Modal: Edit User Role -->
   <x-modal-layout name="edit-user-role">

      <form wire:submit.prevent="save" class="w-full md:w-110">

         <!-- Modal: Name -->
         <h3 class="text-lg font-bold flex items-center gap-x-1">
            <flux:icon.user variant="outline" class="size-5" />
            <span>{{ $this->userName }}</span>
         </h3>
         <div class="w-35 sm:w-25 aspect-square rounded-full flex items-center overflow-hidden justify-center bg-zinc-500 border-3 border-gray-300 inset-shadow-zinc-300 mt-1">
            @if($this->userProfile?->hasMedia('profiles'))
            <img src="{{ $this->userProfile?->getFirstMediaUrl('profiles', 'thumb') }}" 
               class="aspect-square object-cover" 
               alt="profile-image"
            >
            @else
               <flux:icon.user-circle variant="solid" class="size-26 text-gray-400" />
            @endif
         </div>
         <div class="mb-4"></div>

         <!-- Modal: Role selection -->
         <flux:select wire:model.live="userRole" label="Huidige gebruikerspositie">
            @foreach(UserRole::options() as $value => $label)
            <option value="{{ $value }}">
                  {{ $label }}
            </option>
            @endforeach
         </flux:select>

         <!-- Modal: Role information -->
         <div class="my-5">
            <h3 class="{{ $this->getRoleData->labelColor() }} rounded-md hidden md:inline-block px-2 mt-2 mb-1">
               {{ $this->getRoleData->label() }}
            </h3>
            <p>{{ $this->getRoleData->description() }}</p>
         </div>

         <!-- Modal: Submit button -->
         <flux:button type="submit" class="cursor-pointer color-sub hover-color-sub transition delay-2s">
            <div class="flex items-center gap-x-1">
               <span>Bevestig aanpassing</span>
               <flux:icon.check class="size-4"/>
            </div>
         </flux:button>

      </form>

   </x-modal-layout>

</div>