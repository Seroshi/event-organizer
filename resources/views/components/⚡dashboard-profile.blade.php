<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Collection;

use Livewire\Component;
use Livewire\WithFileUploads;

use App\Services\DashboardService;
use App\Models\Profile;
use App\Models\Event;
use App\Models\User;

new class extends Component
{
   public $profile = null;
   public $profileImage = null;
   public $profileName = '';
   public $profileEmail = '';
   public $profileCompany = '';

   public function mount(): void
   {
      $profile = Profile::where('user_id', auth()->user()->id)->first();
      $this->profile = $profile;
   }

   public function getProfileData(): void
   {
      // 1 Open up the main modal
      $this->dispatch('open-modal', name: 'edit-profile');

      if($this->profile){
         $this->profileName = $this->profile->name;
         $this->profileEmail = $this->profile->email;
         $this->profileCompany = $this->profile->company;
      }
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

};
?>


<div class="w-full flex flex-col">

   <!-- Profile: Show User Info -->
   <section>

      <h2 class="font-bold text-md text-gray-300 flex items-center justify-center gap-1 mb-1">
         <span>
            <flux:icon.user-circle variant="outline" class="size-6" />
         </span>
         <span>Mijn profiel</span>
      </h2>

      <div class="text-lg sm:text-sm bg-zinc-700 rounded-lg p-4">
         <div class="sm:h-55.5">

            <div class="flex gap-2">

               <!-- Profile image -->
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

               <!-- Profile Name & Role -->
               <div class="flex flex-col justify-center p-2">
                  <p class="text-lg text-center text-bold mb-1">{{ $this->profile?->name }}</p>
                  <p class="{{ auth()->user()->role?->labelColor() }} inline-block text-center rounded-md px-2">
                     {{ strtolower(auth()->user()->role?->label()) }}
                  </p>
               </div>

            </div>

            <!-- Profile Email & Company -->
            <div class="mt-4 flex flex-col gap-y-2">
               <div class="flex items-center gap-2">
                  <flux:icon.envelope variant="outline" class="size-5" />
                  <p>{{ $this->profile?->email }}</p>
               </div>
               <div class="flex items-center gap-2">
                  <flux:icon.building-office variant="outline" class="size-5" />
                  <p>{{ ($this->profile?->company) ? $this->profile?->company : '-' }}</p>
               </div>
               <a class="w-fit flex items-center bg-zinc-800 hover:bg-zinc-400 rounded-md px-2 py-1 mt-3"
                  wire:click="getProfileData()" 
               >
                  <flux:icon.pencil-square variant="outline" class="size-5"/>
                  <span class="ml-1 align-middle">Profiel aanpassen</span>
               </a>
            </div>

         </div>
      </div>
   </section>

   <!-- Modal: Edit Profile -->
   <x-modal-layout name="edit-profile">

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

   </x-modal-layout>

</div>