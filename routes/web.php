<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/events', 'event-index')->name('event.index');
Volt::route('/events/create', 'event-manage')->name('event.create');
Volt::route('/events/list', 'event-list')->name('event.list');
Volt::route('/events/{event}', 'event-show')->name('event.show');
Volt::route('/events/{event}/edit', 'event-manage')->name('event.edit');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
