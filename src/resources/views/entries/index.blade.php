<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>自分の時間割</title>
    <style>
        body { color: #1f2937; font-family: sans-serif; margin: 40px; }
        main { max-width: 1000px; }
        nav { display: flex; gap: 16px; margin-bottom: 28px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d1d5db; padding: 12px; text-align: left; }
        th { background: #eff6ff; }
        form { display: inline; }
        button { background: #dc2626; border: 0; border-radius: 4px; color: white; cursor: pointer; padding: 8px 14px; }
        .actions { margin: 20px 0; }
        .notice { background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: 12px; }
    </style>
</head>
<body>
    <main>
        <nav>
            <a href="{{ url('/entries') }}">自分の時間割</a>
            <a href="{{ url('/courses') }}">授業一覧</a>
        </nav>

        <h1>自分の時間割</h1>

        @if (session('success'))
            <p class="notice">{{ session('success') }}</p>
        @endif

        <p class="actions"><a href="{{ url('/entries/create') }}">＋ 授業を登録する</a></p>

        <table>
            <thead>
                <tr>
                    <th>曜日</th>
                    <th>時限</th>
                    <th>授業名</th>
                    <th>担当教員</th>
                    <th>教室</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                    <tr>
                        <td>{{ $entry->course->day_name }}</td>
                        <td>{{ $entry->course->period }}限</td>
                        <td>{{ $entry->course->name }}</td>
                        <td>{{ $entry->course->teacher ?? '未定' }}</td>
                        <td>{{ $entry->course->room }}</td>
                        <td>
                            <form action="{{ url('/entries/' . $entry->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit">削除</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">まだ授業が登録されていません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>
