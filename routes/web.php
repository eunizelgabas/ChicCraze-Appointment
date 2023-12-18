<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
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
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class , 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('can:manage-user')->group(function() {
        Route::get('/users',[UserController::class, 'index'])->name('user.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/users',[UserController::class, 'store'])->name('user.store');
        Route::get('/users/edit/{user}', [UserController::class, 'edit']);
        Route::put('/users/{user}',[UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        });

     //Services
     Route::middleware('can:manage-service')->group(function() {
        Route::get('/service/create', [ServiceController::class, 'create'])->name('service.create');
        Route::get('/service',[ServiceController::class, 'index'])->name('service.index');
        Route::post('/service',[ServiceController::class, 'store']);
        Route::get('/service/edit/{service}', [ServiceController::class, 'edit']);
        Route::put('/service/{service}',[ServiceController::class, 'update']);
        Route::delete('/service/{service}', [ServiceController::class, 'destroy']);
        });

    Route::middleware('can:manage-appointment')->group(function() {
        Route::get('/appointment/index', [AppointmentController::class,  'index'])->name('appointment.index');
        Route::get('/appointment/create', [AppointmentController::class,  'create'])->name('appointment.create');
        Route::post('/appointment', [AppointmentController::class,  'store']);
        Route::get('/appointment/show/{appointment}', [AppointmentController::class, 'show']);
        Route::get('/appointment/edit/{appointment}', [AppointmentController::class, 'edit']);
        Route::delete('/appointment/{appointment}', [AppointmentController::class, 'destroy']);
        Route::put('/appointment/{appointment}',[AppointmentController::class, 'update']);
        Route::post('/appointment/accept/{appointment}', [AppointmentController::class, 'accept']);
        Route::post('/appointment/cancel/{appointment}', [AppointmentController::class, 'cancel']);
        Route::get('/appointment/create/{patient}', [AppointmentController::class, 'createForPatient']);
    });
});

require __DIR__.'/auth.php';
