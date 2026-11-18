<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TimetableEntryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FavoriteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/timetable', [TimetableController::class, 'index']);

Route::get('/entries', [TimetableEntryController::class, 'index']);
Route::get('/entries/create', [TimetableEntryController::class, 'create']);
Route::post('/entries', [TimetableEntryController::class, 'store']);
Route::delete('/entries/{id}', [TimetableEntryController::class, 'destroy']);

Route::get('/courses', [CourseController::class, 'index']);

Route::post('/courses/{course}/favorite', [
    FavoriteController::class,
    'store'
])->middleware('auth');
Route::delete('/courses/{course}/favorite', [
    FavoriteController::class,
    'destroy'
])->middleware('auth');