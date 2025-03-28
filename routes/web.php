<?php

use App\Http\Controllers\PointController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return Inertia::render('Map', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/', [PointController::class, 'index'])->name('map');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
   
    Route::post('/store', [PointController::class, 'store'])->name('store');
    Route::get('/gestion', [PointController::class, 'gestion'])->name('gestion');
    
    Route::delete('/delete/{id}', [PointController::class, 'destroy'])->name('delete');

    Route::put('/update/{id}', [PointController::class, 'update'])->name('update');

    Route::get('/edit/{id}', [PointController::class, 'edit'])->name('edit');
    Route::get('/add-point', [PointController::class, 'addPoint'])->name('addPoint');
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
