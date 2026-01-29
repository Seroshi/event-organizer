<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="flex flex-col items-center relative pt-4">
    <section class="fixed top bg-green-600 text-white px-6 py-3 rounded shadow-lg w-xl"
        x-data="{ show: @js(session()->has('success')) }" 
        x-show="show" x-cloak
        x-init="if(show) setTimeout(() => show = false, 5000)"
    >
        @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
    </section>
</div>