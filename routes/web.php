<?php

use Illuminate\Support\Facades\Route;

Route::view('/playground', 'playground')->name('playground');
Route::get('/', fn() => redirect()->route('playground'));
