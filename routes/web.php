<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['role:admin,organizer'])->group(function () {
    Volt::route('/events/list', 'event-list')->name('event.list');
    Volt::route('/events/create', 'event-manage')->name('event.create');
    Volt::route('/events/{event}/edit', 'event-manage')->name('event.edit');
});

Volt::route('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/', function () {
    return view('welcome');
})->name('home');

Volt::route('/events', 'event-index')->name('event.index');
Volt::route('/events/{event}', 'event-show')->name('event.show');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

require __DIR__.'/settings.php';
