<?php

use Livewire\Component;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Artisan;

new class extends Component
{
    public function mount(): void
    {
        if(session()->pull('show_welcome_modal'))
        {
            Cookie::queue('portfolio_visited', 'true', 10080);

            // Run seerder to update all old events
            Artisan::call('db:seed', [
                '--class' => 'UpdateEventsSeeder'
            ]);

            // Making sure the events are visisually updated (target: event.index)
            $this->dispatch('refresh-event-index'); 

            // Opens the modal for newcoming visitors
            $this->dispatch('open-modal', name: 'receive-visitor');
        }
    }
};
?>

<section>
    <x-modal-layout name="receive-visitor">
        <p class="mb-4">Welkom nieuwe bezoeker!</p>
        <p class="mb-4">Voor deze demo is het mogelijk om van account types te wisselen, 
            puur en alleen om verschillende weergaves te tonen.
        </p>
        <img src="{{ asset('storage/account_wissel.jpg') }}" alt="account_wissel" width="100%" height="auto">
        <p class="mt-6 mb-2">Gebruik hiervoor de <strong>[ account wissel ]</strong> knop rechts bovenaan. Deze wisselt tussen de account types: </p>
        <ul>
            <li>Admin</li>
            <li>Organisator</li>
            <li>Deelnemer</li>
        </ul>
        <p>Hiermee is een eigen account aanmaken niet nodig, maar het mag uiteraard wel.</p>
    </x-modal-layout>
</section>