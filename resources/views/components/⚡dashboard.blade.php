<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Collection;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use App\Livewire\Actions\Logout;
use App\Services\EventService;
use App\Services\DashboardService;
use App\Models\Event;
use App\Models\User;
use App\Models\Profile;
use App\Enums\UserRole;



use Illuminate\Support\Str;

new class extends Component
{
   use WithFileUploads;

   public $selectedUserId = null;
   public $roleOptions;
   public $userData = [
      'selectedRole' => 'user',
      'name'         => '',
   ];
   public $userRole = 'user';

   public $profile = null;
   public $profileImage = null;
   public $profileName = '';
   public $profileEmail = '';
   public $profileCompany = '';

   public $modalContent = 'profile';

   public function mount(): void
   {
      // Assign role options from the enum static function
      $this->roleOptions = UserRole::options();

      $profile = Profile::where('user_id', auth()->user()->id)->first();
      $this->profile = $profile;
   }

   // Logout through a livewire action that triggers __invoke 
   public function logout(Logout $logout): void
   {
      $logout(); 

      $this->redirect('/', navigate: true);
   }

   // Decide which modal content to show
   public function loadModalContent(String $content): void
   {
      $this->dispatch('open-modal'); // Open up the main modal
      $this->modalContent = $content;
   }

   public function getProfileData(): void
   {
      
      if($this->profile){
         $this->profileName = $this->profile->name;
         $this->profileEmail = $this->profile->email;
         $this->profileCompany = $this->profile->company;
      }
   }

   public function getUserData($id): void
   {
      // Find the corresponding user and event model
      $user = User::withCount('events')->findOrFail($id);
      $this->selectedUserId = $user?->id;

      // Assign the needed user data
      $this->userData = [
         'name'         => $user->name,
         'selectedRole' => $user->role->value,
         'posted'       => $user->events_count,
      ];

      // Open up the main modal
      $this->dispatch('open-modal');
   }

   public function profileSave(DashboardService $service)
   {
      // Validate all profile form fields
      $validated = $this->validate(
         [ 
            'profileName' => 'required|min:2',
            'profileEmail' => 'required|email',
            'profileCompany' => 'nullable',
            'profileImage' => 'nullable',
         ],
         [
            'profileName.required' => 'Je naam is verplicht',
            'profileName.min' => 'Je naam moet minimaal 2 karakters hebben',
            'profileEmail.required' => 'Een email is verplicht',
            'profileEmail.email' => 'Vul een geldig email adres in.',
         ]
      );

      // Update the profile
      $newProfile = $service->updateProfile(auth()->user(), $this->profile, $validated);

      // Upload the image if provided by the user
      if($this->profileImage) $service->uploadImage($newProfile, $this->profileImage);

      // Dispatch a success notification
      session()->flash('success', 'Profiel succesvol aangepast!');

      $this->redirectRoute('dashboard', navigate: true);

      return;
   }

   // Form function upon button confirm
   public function save(): Void
   {
      $user = User::findOrFail($this->selectedUserId);
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
   public function getUserRoles(): Collection
   {
      try{
         return User::where('id', '!=', auth()->id())
            ->orderByRaw("FIELD(role, 'master', 'admin', 'organizer', 'user')")
            ->get();
      } catch (\Exception $e){
         report($e);
         session()->flash('error', 'De gebruikerslijst kon niet worden geladen.');
         return collect();
      }
   }

   // Helper function to retrieve the right data from the selected user role 
   #[Computed]
   public function getRoleData(): UserRole
   {
      // Convert a string into the Enum UserRole::case
      return UserRole::tryFrom( $this->userRole ?? 'user' );
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


<div class="w-full sm:w-2xl md:w-3xl lg:w-4xl mx-auto px-12 py-6"
   x-data="{ showModal: false }" @open-modal.window="showModal = true"
>

   <section>
      <a wire:click="logout" href="#" class="text-sm bg-gray-500 hover:bg-gray-700 rounded-md px-2 py-1">
         Afmelden
      </a>
   </section>

   <section class="flex gap-8 items-center flex-col sm:flex-row">

      <!-- Profile information -->
      <div class="w-full flex flex-col">
         <h2 class="font-bold text-md text-gray-300 flex items-center justify-center gap-1 mb-1">
            <span>
               <flux:icon.user-circle variant="outline" class="size-6" />
            </span>
            <span>Mijn profiel</span>
         </h2>
         <div class="text-lg sm:text-sm bg-zinc-700 rounded-lg py-4 pl-4">
            <div class="sm:h-55.5">
               <div class="flex gap-2">
                  <div class="flex flex-col items-center relative">
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
                  </div>
                  <div class="flex flex-col justify-center p-2">
                     <p class="text-lg text-center text-bold mb-1">{{ $this->profile?->name }}</p>
                     <p class="{{ auth()->user()->role?->labelColor() }} inline-block text-center rounded-md px-2">
                        {{ strtolower(auth()->user()->role?->label()) }}
                     </p>
                  </div>
               </div>
               <div class="mt-4 p-1 flex flex-col gap-y-2">
                  <div class="flex items-center gap-2">
                     <flux:icon.envelope variant="outline" class="size-5" />
                     <p>{{ $this->profile?->email }}</p>
                  </div>
                  <div class="flex items-center gap-2">
                     <flux:icon.building-office variant="outline" class="size-5" />
                     <p>{{ ($this->profile?->company) ? $this->profile?->company : '-' }}</p>
                  </div>
                  <a href="#" wire:click="getProfileData(); loadModalContent('profile')" class="w-fit flex items-center bg-zinc-800 hover:bg-zinc-400 rounded-md px-2 py-1 mt-3">
                     <flux:icon.pencil-square variant="outline" class="size-5"/>
                     <span class="ml-1 align-middle">Profiel aanpassen</span>
                  </a>
               </div>
            </div>
         </div>
      </div>

      <!-- All users list -->
      @admin
      <div class="w-full flex flex-col">
         <h2 class="font-bold text-md text-gray-300 flex items-center justify-center gap-1 mb-1">
            <span>
               <flux:icon.users variant="outline" class="size-5" />
            </span>
            <span>Alle gebruikers</span>
         </h2>
         <div class="text-lg sm:text-sm bg-zinc-700 rounded-lg py-2 pl-2 pr-1">
            <div class="h-83 sm:h-59.5 overflow-y-auto hide-scrollbar-until-hover">
               @foreach($this->getUserRoles as $user)
               <a wire:click="loadModalContent('user'); getUserData({{ $user->id }})" href="#"
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
      </div>
      @endadmin

      <!-- Event options -->
      @organizer 
      <div class="w-full flex flex-col">
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
      </div>
      @endorganizer

   </section>

   <!-- Modal: Main content -->
   <section class="fixed w-screen h-screen top-0 left-0 flex justify-center items-center backdrop-blur-[20px] z-50 px-2" style="background:rgba(0,0,0,0.4);"
      x-show="showModal" x-cloak
   >
      <div class="bg-zinc-700 py-6 pl-6 mr-3 my-4 rounded-xl shadow-md w-[90%] h-[90%] md:max-w-xl relative" 
         @click.away="showModal = true"
         x-data="{ localLoading: false }" 
         @set-modal-message-data.window="localLoading = true"
         @set-modal-message-data-finished.window="localLoading = false"
      >
         
         <!-- Modal - Close button -->
         <div class="absolute -top-3.75 -right-3.75 text-xs text-white w-8 h-8 bg-gray-500 hover:bg-gray-700 rounded-full flex justify-center items-center 
            cursor-pointer shadow-md duration-200"
            @click="showModal = false"
         >
            <flux:icon.x-mark variant="solid" class="size-5" />
         </div>

         <!-- Modal:Profile - Start of form -->
         @if($this->modalContent == 'profile')
         <div class="w-full h-full overflow-y-auto hide-scrollbar-until-hover pr-6">
            <form wire:submit.prevent="profileSave">

               <h3 class="text-lg font-bold flex items-center gap-x-1">
                  <flux:icon.user-circle variant="outline" class="size-6" />
                  <span>Mijn profiel gegevens</span>
               </h3>
               <div class="mb-4"></div>

               <!-- Image file upload -->
               <div class="mb-6">
                  <label class="font-medium">Afbeelding keuze</label>
                  @if($this->profile && $this->profile?->hasMedia('profiles') && !$this->profileImage)
                  <div class="sm:w-[50%] pt-2 pb-3">
                     {{ $this->profile->getFirstMedia('profiles') }}
                  </div>
                  @endif
                  <flux:input type="file" wire:model="profileImage" class="mt-2 mb-4"/>

                  {{-- Show a loading state while the temp file is uploading --}}
                  <div wire:loading wire:target="profileImage" class="text-blue-500 text-xs mt-1">
                     Upload afbeelding...
                  </div>
                  

                  @if($this->profileImage)
                     {{-- Show the temporary preview --}}
                     <img src="{{ $this->profileImage->temporaryUrl() }}" class="size-[50%] object-cover">
                  @endif
               </div>

               <!-- Modal:User - Name -->
               <div class="mb-6">
                  <flux:input wire:model.live="profileName" type="text" label="Naam" />
               </div>

               <div class="mb-6">
                  <flux:input wire:model.live="profileEmail" type="text" label="Email" />
               </div>

               <div class="mb-10">
                  <flux:input wire:model.live="profileCompany" type="text" label="Bedrijf" />
               </div>

               <!-- Modal:User - Submit button -->
               <flux:button type="submit" class="cursor-pointer color-sub hover-color-sub transition delay-2s">
                  <div class="flex items-center gap-x-1">
                     <span>Bevestig aanpassing</span>
                     <flux:icon.check class="size-4"/>
                  </div>
               </flux:button>
            </form>
         </div>
         
         <!-- Modal:User - Start of User form -->
         @elseif($this->modalContent == 'user')
         <form wire:submit.prevent="save" class="w-full md:w-110">

            <!-- Modal:User - Name -->
            @if($this->userData)
            <h3 class="text-lg font-bold flex items-center gap-x-1">
               <flux:icon.user variant="outline" class="size-5" />
               <span>{{ $this->userData['name'] }}</span>
            </h3>
            <div class="mb-4"></div>
            @endif

            <!-- Modal:User - Role selection -->
            <flux:select wire:model.live="" label="Huidige gebruikerspositie">
               @foreach(UserRole::options() as $value => $label)
               <option value="{{ $value }}">
                     {{ $label }}
               </option>
               @endforeach
            </flux:select>

            <!-- Modal:User - Role information -->
            <div class="my-5">
               <h3 class="{{ $this->getRoleData->labelColor() }} rounded-md hidden md:inline-block px-2 mt-2 mb-1">
                  {{ $this->getRoleData->label() }}
               </h3>
               <p>{{ $this->getRoleData->description() }}</p>
            </div>

            <!-- Modal:User - Submit button -->
            <flux:button type="submit" class="cursor-pointer color-sub hover-color-sub transition delay-2s">
               <div class="flex items-center gap-x-1">
                  <span>Bevestig aanpassing</span>
                  <flux:icon.check class="size-4"/>
               </div>
            </flux:button>
         </form>
         @else
         <p>Data not found</p>
         @endif

      </div>
   </section>

</div>