<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TimetableEntryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/timetable', [TimetableController::class, 'index']);

Route::get('/entries', [TimetableEntryController::class, 'index']);
Route::get('/entries/create', [TimetableEntryController::class, 'create']);
Route::post('/entries', [TimetableEntryController::class, 'store']);
Route::delete('/entries/{id}', [TimetableEntryController::class, 'destroy']);

Route::get('/courses', [CourseController::class, 'index']);
Route::middleware('auth')->group(function () {

    Route::get('/courses/{course}', [
        CourseController::class, 'show'
    ]);

    Route::post('/courses/{course}/reviews', [
        ReviewController::class, 'store'
    ]);

    Route::delete('/reviews/{review}', [
        ReviewController::class, 'destroy'
    ]);

});