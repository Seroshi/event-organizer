<x-layouts::app :title="__(config('app.name').' | Events')">
    <div class="w-3xl mx-auto p-6 ">
        SHow somehing!
        <livewire:create-event />
    </div>
</x-layouts::app>