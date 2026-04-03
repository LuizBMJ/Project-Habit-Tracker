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

// AUTH
Route::middleware('auth')->group(function() {
    
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth.logout');

    // HABITS
    Route::resource('/dashboard/habits', HabitControler::class)->except('show');
    Route::get('/dashboard/habits/historico/{year?}', [HabitControler::class, 'history'])->name('habits.history');
    Route::get('/dashboard/habits/configurar', [HabitControler::class, 'settings'])->name('habits.settings');
    Route::post('/dashboard/habits/{habit}/toggle', [HabitControler::class, 'toggle'])->name('habits.toggle');

    // CALENDÁRIO DE HÁBITOS
    Route::prefix('/dashboard/habits/calendar')
    ->name('habits.calendar.')
    ->group(function () {

        Route::get('/', [HabitControler::class, 'calendar'])->name('index');

        Route::get('/events', [HabitControler::class, 'calendarEvents'])->name('events');

        Route::post('/toggle', [HabitControler::class, 'calendarToggle'])->name('toggle');
});

});