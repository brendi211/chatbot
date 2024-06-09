<?php

use App\Http\Controllers\BotManController;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Http\Controllers\MainController::class);

Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle'])->name('botman');
