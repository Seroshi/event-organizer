@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand {{ $attributes }}>
        <x-slot name="logo" class="flex 5/2 size-20 items-center justify-center rounded-md">
            <x-app-logo-icon />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-5/1 items-center justify-center rounded-md">
            <x-app-logo-icon />
        </x-slot>
    </flux:brand>
@endif
