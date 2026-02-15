<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component
{
   

};
?>

<div class="w-full sm:w-2xl md:w-3xl lg:w-4xl mx-auto px-12 py-6">

   <div class="flex gap-5 items-center flex-wrap mb-6">

      <livewire:dashboard-profile />

      <!-- Event options -->
      @auth 
      <livewire:dashboard-options />
      @endauth
      
      @admin
      <!-- User roles management -->
      <livewire:dashboard-user />
      @endadmin
      
      @auth 
      <!-- Event statistics -->
      <livewire:dashboard-stats />
      @endauth

      <livewire:dashboard-likes />

   </div>

</div>