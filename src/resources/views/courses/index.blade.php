<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>授業一覧</title>
    <style>
        body { color: #1f2937; font-family: sans-serif; margin: 40px; }
        main { max-width: 1100px; }
        nav { display: flex; gap: 16px; margin-bottom: 28px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #d1d5db; padding: 10px; text-align: left; }
        th { background: #eff6ff; }
        button { background: #2563eb; border: 0; border-radius: 4px; color: white; cursor: pointer; padding: 8px 14px; }
        button[disabled] { background: #9ca3af; cursor: default; }
        .error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 12px; }
    </style>
</head>
<body>
    <main>
        <nav>
            <a href="{{ url('/entries') }}">自分の時間割</a>
            <a href="{{ url('/courses') }}">授業一覧</a>
        </nav>

        <h1>授業一覧</h1>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>曜日</th>
                    <th>時限</th>
                    <th>科目コード</th>
                    <th>授業名</th>
                    <th>担当教員</th>
                    <th>教室</th>
                    <th>登録</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $course)
                    <tr>
                        <td>{{ $course->day_name }}</td>
                        <td>{{ $course->period }}限</td>
                        <td>{{ $course->code }}</td>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->teacher ?? '未定' }}</td>
                        <td>{{ $course->room }}</td>
                        <td>
                            @if ($course->timetableEntries->isNotEmpty())
                                <button type="button" disabled>登録済み</button>
                            @else
                                <form action="{{ url('/entries') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <button type="submit">登録</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
