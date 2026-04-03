<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HabitControler;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;


Route::get('/', [SiteController::class, 'index'])->name('site.index');

Route::get('/login', [LoginController::class, 'index'])->name('site.login');
Route::post('/login', [LoginController::class, 'login'])->name('auth.login');

Route::get('/cadastro', [RegisterController::class, 'index'])->name('site.register');
Route::post('/cadastro', [RegisterController::class, 'store'])->name('auth.register');

Route::middleware('auth')->group(function() {

    Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout');

    Route::prefix('/dashboard')->group(function () {

        // ✅ Specific routes FIRST, before resource()
        Route::get('habits/historico/{year?}', [HabitControler::class, 'history'])->name('habits.history');
        Route::get('habits/configurar', [HabitControler::class, 'settings'])->name('habits.settings');
        Route::post('habits/{habit}/toggle', [HabitControler::class, 'toggle'])->name('habits.toggle');

        Route::prefix('habits/calendar')->name('habits.calendar.')->group(function () {
        Route::get('/', [HabitControler::class, 'calendar'])->name('index');
        Route::get('/events', [HabitControler::class, 'calendarEvents'])->name('events');
        Route::post('/toggle-date', [HabitControler::class, 'calendarToggle'])->name('toggle'); // 👈 changed
    });

    // ✅ Resource route LAST
    Route::resource('habits', HabitControler::class)->except('show');

    });

});