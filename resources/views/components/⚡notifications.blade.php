<?php

use Livewire\Component;

new class extends Component
{

    public function render()
    {
        return view('components.⚡notifications', [
            'success' => session()->get('success'),
            'error' => session()->get('error'),
        ]);
    }
};
?>

<div class="flex flex-col items-center relative pt-4">
    
    {{-- Success Message --}}
    @if (session()->has('success'))
    <section 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 5000)"
        x-transition
        class="fixed top-20 bg-green-600 text-white px-6 py-3 rounded shadow-lg w-xl z-60"
    >
        {{ session('success') }}
    </section>
    @endif

    {{-- Error Message --}}
    @if (session()->has('error'))
    <section 
        x-data="{ show: true }" 
        x-show="show" 
        x-init="setTimeout(() => show = false, 5000)"
        x-transition
        class="fixed top-20 bg-red-600 text-white px-6 py-3 rounded shadow-lg w-xl z-60"
    >
        {{ session('error') }}
    </section>
    @endif
</div>