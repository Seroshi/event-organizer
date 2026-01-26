<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::get('/events', function () {
//     return view('events');
// })->name('events');

// Route::get('/events', function () {
//     return view('create-event'); // This should be the page that contains your component
// });

Volt::route('/events', 'create-event')->name('events');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
