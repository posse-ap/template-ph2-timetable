s<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TimetableEntryController;

Route::get('/', function () {
    return view('welcome');
});

// W3
Route::get('/timetable', [TimetableController::class, 'index']);

// W4
Route::get('/entries', [TimetableEntryController::class, 'index']);
Route::get('/entries/create', [TimetableEntryController::class, 'create']);
Route::post('/entries', [TimetableEntryController::class, 'store']);

// Week5：編集画面
Route::get('/entries/{id}/edit', [
    TimetableEntryController::class,
    'edit'
]);

// Week5：編集内容の保存
Route::put('/entries/{id}', [
    TimetableEntryController::class,
    'update'
]);

// Week5：授業の削除
Route::delete('/entries/{id}', [
    TimetableEntryController::class,
    'destroy'
]);