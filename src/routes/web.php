<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\TimetableEntryController;
use App\Http\Controllers\AdminEntryController;
use App\Http\Controllers\AnnouncementController;

// ========================================
// トップページ
// ========================================

Route::get('/', function () {
    return view('welcome');
});


// ========================================
// 時間割ページ
// ========================================

Route::get('/timetable', [
    TimetableController::class,
    'index'
]);


// ========================================
// 授業一覧・授業詳細
// ========================================

// 授業一覧
Route::get('/courses', [
    CourseController::class,
    'index'
]);

// 授業詳細（Week11で追加）
Route::get('/courses/{course}', [
    CourseController::class,
    'show'
]);


// ========================================
// 学生の時間割・履修申請
// ========================================

Route::middleware('auth')->group(function () {

    // 自分の時間割一覧
    Route::get('/entries', [
        TimetableEntryController::class,
        'index'
    ]);

    // 履修申請画面
    Route::get('/entries/create', [
        TimetableEntryController::class,
        'create'
    ]);

    // 履修申請
    Route::post('/entries', [
        TimetableEntryController::class,
        'store'
    ]);

    // 申請の削除・取り消し
    Route::delete('/entries/{id}', [
        TimetableEntryController::class,
        'destroy'
    ]);

});


// ========================================
// 管理者専用ページ
// ========================================

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        // 申請中の履修一覧
        Route::get('/entries', [
            AdminEntryController::class,
            'index'
        ])->name('admin.entries.index');

        // 履修の承認
        Route::post('/entries/{entry}/approve', [
            AdminEntryController::class,
            'approve'
        ])->name('admin.entries.approve');

        // 履修の却下
        Route::post('/entries/{entry}/reject', [
            AdminEntryController::class,
            'reject'
        ])->name('admin.entries.reject');

    });


// ========================================
// Week11：お知らせ投稿機能
// ========================================

// 管理者だけがお知らせを投稿できる
Route::post('/courses/{course}/announcements', [
    AnnouncementController::class,
    'store'
])->middleware(['auth', 'admin']);

