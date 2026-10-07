<?php

use Illuminate\Support\Facades\Route;


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('apps', \App\Livewire\Apps\Index::class)
    ->middleware(['auth', 'verified', 'role.zero'])
    ->name('apps.index');

Route::get('contacts', \App\Livewire\Contacts\Index::class)
    ->middleware(['auth', 'verified', 'level.two'])
    ->name('contacts.index');

Route::get('templates', \App\Livewire\Templates\Index::class)
    ->middleware(['auth', 'verified', 'level.two'])
    ->name('templates.index');

Route::get('template-pesan', fn() => redirect()->route('templates.index'));

Route::get('message-schedulers', \App\Livewire\MessageSchedulers\Index::class)
    ->middleware(['auth', 'verified', 'level.two'])
    ->name('message-schedulers.index');

Route::get('whatsapp', \App\Livewire\Whatsapp\Index::class)
    ->middleware(['auth', 'verified'])
    ->name('whatsapp.index');

require __DIR__.'/auth.php';
