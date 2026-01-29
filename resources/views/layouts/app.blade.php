<x-layouts::app.header :title="$title ?? null">
    <div>
        <livewire:notifications />
        {{ $slot }}
    <div>
</x-layouts::app.header>
