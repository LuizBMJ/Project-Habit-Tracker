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
    
    Route::get('/dashboard', [SiteController::class, 'dashboard'])->name('site.dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('auth.logout');

    // HABITS
    
    Route::get('/dashboard/habits/create', [HabitControler::class, 'create'])->name('habit.create');

    Route::post('/dashboard/habits', [HabitControler::class, 'store'])->name('habit.store');

    Route::delete('/dashboard/habits/{habit}', [HabitControler::class, 'destroy'])->name('habit.destroy');

    Route::get('/dashboard/habits/{habit}/edit', [HabitControler::class, 'edit'])->name('habit.edit');

    Route::put('/dashboard/habits/{habit}', [HabitControler::class, 'update'])->name('habit.update');
});