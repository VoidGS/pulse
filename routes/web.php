<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('services', ServiceController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::resource('customers', CustomerController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('guardians', GuardianController::class)->only(['index']);

    Route::resource('schedules', ScheduleController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('schedules/filter', [ScheduleController::class, 'filter'])->name('schedules.filter');
});
