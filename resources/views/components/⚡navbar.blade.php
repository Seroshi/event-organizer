<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

use App\Livewire\Actions\Logout;

new class extends Component
{
    /**
     * Allow logout with the logout button 
     */
    public function logout(Logout $logout): void
    {
        // Logout through a livewire action that triggers __invoke 
        $logout();
        
        $this->redirect('/', navigate: true);
    }

    /**
     * Decide the user route of the switch button  
     */
    #[Computed]
    public function account() 
    {
        $role = auth()->user()?->role->value;

        if($role === 'admin') return 'organizer';
        elseif($role === 'organizer') return 'user';
        elseif($role === 'user') return 'admin';
        else return 'admin';
    }
};
?>

<nav x-data="{ mobile: false }" @click.outside="if(mobile) mobile = false">
    <div class="color-main relative w-full border-b border-gray-100 h-16.5 z-50">
        <div class="text-xl md:text-sm flex justify-between h-16">
            <div class="flex">
                <div class="flex items-center md:hidden ml-3">
                    <button @click="mobile =! mobile" class="inline-flex items-center justify-center p-1 rounded-md text-gray-100 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                        {{-- Switch icons based on "mobile" state --}}
                        <svg class="size-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': mobile, 'inline-flex': ! mobile }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! mobile, 'inline-flex': mobile }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <a href="{{ route('event.index') }}" class="relative left-2 md:left-5 flex items-center font-bold aspect-3/2 mr-3 z-60">
                    <x-app-logo-icon />
                </a>
                <div class="left-0 pl-0.5 md:pl-5 absolute md:relative -translate-x-full md:translate-x-0 md:translate-y-0 transition-all duration-300 color-main flex w-sm md:w-full items-center"
                    :class="mobile ? 'translate-x-0 flex-wrap translate-y-16.5' : '-translate-x-full flex-wrap md:translate-x-0 translate-y-16.5'"
                >
                    <a href="{{ route('event.index') }}" class="color-main hover-color-main px-2 py-2 rounded-md w-full flex items-center gap-4 md:gap-1 md:w-auto">
                        <flux:icon.home variant="outline" class="size-7 md:size-5" />
                        <span>Home</span>
                    </a>
                    @if(auth()->user())
                    <a href="{{ route('dashboard') }}" class="color-main hover-color-main px-2 py-2 rounded-md w-full flex items-center gap-4 md:gap-1  md:w-auto">
                        <flux:icon.squares-2x2 variant="outline" class="size-7 md:size-5" />
                        <span>Dashboard</span>
                    </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center pr-3">
                @if(Auth::guest())
                <a href="{{ route('login') }}" class="hover:bg-gray-100 hover:text-gray-500 p-1 rounded-md w-full flex items-center gap-3 md:gap-1  md:w-auto">
                    <flux:icon.user-circle variant="outline" class="size-9 md:size-7" />
                    <span class="hidden md:block md:pr-1">Log in</span>
                </a>
                @else
                <a wire:click="logout" class="hover:bg-gray-100 hover:text-gray-500 p-1 rounded-md w-full flex items-center gap-3 md:gap-1  md:w-auto">
                    <flux:icon.arrow-right-end-on-rectangle variant="outline" class="size-9 md:size-6" />
                    <span class="hidden md:block md:pr-1">Log uit</span>
                </a>
                @endif
            </div>

        </div>

        <!-- Switch user mode -->
        <div class="flex w-full justify-end pt-3 pr-2">
            <div class="inline-flex bg-zinc-700 hover:bg-white backdrop-blur-[20px] rounded-md px-3 py-1">
                <a href="{{route('account.switch', $this->account)}}" class="flex gap-1 items-center text-gray-300 hover:text-gray-700 group">
                    <flux:icon.arrow-path variant="outline" class="size-6 md:size-4 transition-transform duration-500 group-hover:rotate-180" />
                    <span>account wissel</span>
                </a>
            </div>
        </div>

    </div>

</nav>