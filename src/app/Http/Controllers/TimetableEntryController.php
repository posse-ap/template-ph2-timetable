<?php

namespace App\Http\Controllers;

use App\Models\TimetableEntry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TimetableEntryController extends Controller
{
    // 一覧表示
    public function index()
    {
        $entries = TimetableEntry::orderBy('day')
            ->orderBy('period')
            ->get();

        return view('entries.index', [
            'entries' => $entries
        ]);
    }

    // 新規登録画面
    public function create()
    {
        return view('entries.create');
    }

    // 新規登録処理
    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => [
                'required',
                'integer',
                'between:1,5',
                Rule::unique('timetable_entries', 'day')
                    ->where('period', $request->input('period')),
            ],
            'period' => ['required', 'integer', 'between:1,6'],
            'title' => ['required', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:255'],
        ], [
            'day.required' => '曜日は1〜5で入力してください',
            'day.integer' => '曜日は1〜5で入力してください',
            'day.between' => '曜日は1〜5で入力してください',
            'day.unique' => 'その曜日・時限にはすでに授業が登録されています',

            'period.required' => '時限は1〜6で入力してください',
            'period.integer' => '時限は1〜6で入力してください',
            'period.between' => '時限は1〜6で入力してください',

            'title.required' => '授業名を入力してください',
            'title.string' => '授業名を入力してください',
            'title.max' => '授業名は255文字以内で入力してください',

            'room.string' => '教室名を正しく入力してください',
            'room.max' => '教室名は255文字以内で入力してください',
        ]);

        TimetableEntry::create($validated);

        return redirect('/entries');
    }

    // 編集画面
    public function edit(int $id)
    {
        $entry = TimetableEntry::findOrFail($id);

        return view('entries.edit', [
            'entry' => $entry
        ]);
    }

    // 編集内容の保存
    public function update(Request $request, int $id)
    {
        $entry = TimetableEntry::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:255'],
        ], [
            'title.required' => '授業名を入力してください',
            'title.string' => '授業名を入力してください',
            'title.max' => '授業名は255文字以内で入力してください',
            'room.string' => '教室名を正しく入力してください',
            'room.max' => '教室名は255文字以内で入力してください',
        ]);

        $entry->update($validated);

        return redirect('/entries');
    }

    // 削除処理
    public function destroy(int $id)
    {
        $entry = TimetableEntry::findOrFail($id);

        $entry->delete();

        return redirect('/entries');
    }
}
