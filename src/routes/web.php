<?php

use App\Http\Controllers\AdminEntryController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TimetableEntryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/timetable', [TimetableController::class, 'index']);

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/entries', [TimetableEntryController::class, 'index']);
    Route::get('/entries/create', [TimetableEntryController::class, 'create']);
    Route::post('/entries', [TimetableEntryController::class, 'store']);
    Route::delete('/entries/{id}', [TimetableEntryController::class, 'destroy']);

    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{course}', [CourseController::class, 'show']);

    Route::post('/courses/{course}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/courses/{course}/favorite', [FavoriteController::class, 'destroy']);

    Route::post('/courses/{course}/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/entries', [AdminEntryController::class, 'index'])
            ->name('admin.entries.index');
        Route::post('/entries/{entry}/approve', [AdminEntryController::class, 'approve'])
            ->name('admin.entries.approve');
        Route::post('/entries/{entry}/reject', [AdminEntryController::class, 'reject'])
            ->name('admin.entries.reject');
    });

Route::post('/courses/{course}/announcements', [
    AnnouncementController::class,
    'store',
])->middleware(['auth', 'admin']);
