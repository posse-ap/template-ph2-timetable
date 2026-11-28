<?php

namespace App\Http\Controllers;

use App\Models\TimetableEntry;
use Illuminate\Support\Facades\DB;

class AdminEntryController extends Controller
{
    // 申請中の一覧
    public function index()
    {
        $entries = TimetableEntry::with([
            'user',
            'course' => function ($query) {
                $query->withCount('approvedEntries');
            }
        ])
        ->where('status', 'pending')
        ->orderBy('id')
        ->get();

        return view('admin.entries.index', [
            'entries' => $entries
        ]);
    }

    // 承認
    public function approve(TimetableEntry $entry)
    {
        DB::transaction(function () use ($entry) {

            /*
             * TODO: 自分で実装
             *
             * 1. 対象授業の行をロックする
             * 2. 申請の最新状態を取得する
             * 3. まだ申請中か確かめる
             * 4. 承認済み人数を数える
             * 5. 定員と比較する
             * 6. 承認状態と決定日時を保存する
             */

            throw new \LogicException(
                '承認処理を実装してください。'
            );
        });

        return back()->with('success', '承認しました。');
    }

    // 却下
    public function reject(TimetableEntry $entry)
    {
        /*
         * TODO: 自分で実装
         *
         * 1. 対象申請の最新状態を取得する
         * 2. 申請中か確かめる
         * 3. 却下状態に変更する
         * 4. 決定日時を保存する
         */

        throw new \LogicException(
            '却下処理を実装してください。'
        );
    }
}