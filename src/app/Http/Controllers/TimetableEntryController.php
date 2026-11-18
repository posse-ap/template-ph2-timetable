<?php

namespace App\Http\Controllers;

use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TimetableEntryController extends Controller
{
    public function index(Request $request)
    {
        // 曜日順・時限順に授業を取得
        $entries = $request->user()
        ->entries()
        ->with('course')
        ->get();

        // 取得した授業をBladeへ渡す
        return view('entries.index', [
            'entries' => $entries
        ]);
    }

    public function create()
    {
        // 授業の新規登録フォームを表示
        return view('entries.create');
    }

    public function store(Request $request)
    {
        // 入力値を検証し、登録に使う値だけを取得
        $request->user()->entries()->create([
            'course_id' => $course->id,
        ]);
        
        
        $validated = $request->validate([
            'day' => ['required', 'integer', 'between:1,5'],
            'period' => ['required', 'integer', 'between:1,6'],
            'title' => ['required', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:255'],
        ]);

        TimetableEntry::create($validated);

        return redirect('/entries');
    }
    public function destroy($id)
    {
        // ① 削除対象を取得
        $entry = TimetableEntry::findOrFail($id);

        // ② 削除してよいユーザーか確認
        Gate::authorize('delete', $entry);

        // ③ 許可された場合だけ削除
        $entry->delete();

        // ④ 時間割画面に戻る
        return redirect('/entries');
    }
}
