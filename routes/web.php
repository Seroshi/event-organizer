<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers;

Route::middleware(['role:admin,organizer'])->group(function () {
    Volt::route('/events/list', 'event-list')->name('event.list');
    Volt::route('/events/create', 'event-manage')->name('event.create');
    Volt::route('/events/{event}/edit', 'event-manage')->name('event.edit');
});

Volt::route('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route('/', 'event-index')
    ->name('home');

Volt::route('/events', 'event-index')->name('event.index');
Volt::route('/events/{event}', 'event-show')->name('event.show');

Route::get('/switch-role/{role}', [Controllers\PortfolioController::class, 'switch'])->name('account.switch');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

require __DIR__.'/settings.php';
