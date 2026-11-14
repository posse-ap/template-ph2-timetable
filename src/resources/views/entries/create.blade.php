<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>授業の新規登録</title>
    <style>
        body { color: #1f2937; font-family: sans-serif; margin: 40px; }
        main { max-width: 720px; }
        nav { display: flex; gap: 16px; margin-bottom: 28px; }
        label { display: block; font-weight: bold; margin-bottom: 8px; }
        select { box-sizing: border-box; font-size: 16px; max-width: 100%; padding: 10px; width: 520px; }
        button { background: #2563eb; border: 0; border-radius: 4px; color: white; cursor: pointer; margin-top: 20px; padding: 10px 20px; }
        .error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 12px; }
    </style>
</head>
<body>
    <main>
        <nav>
            <a href="{{ url('/entries') }}">自分の時間割</a>
            <a href="{{ url('/courses') }}">授業一覧</a>
        </nav>

        <h1>授業の新規登録</h1>

        @error('course_id')
            <p class="error">{{ $message }}</p>
        @enderror

        <form action="{{ url('/entries') }}" method="POST">
            @csrf

            <label for="course_id">登録する授業</label>
            <select id="course_id" name="course_id" required>
                <option value="">授業を選択してください</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                        {{ $course->day_name }}{{ $course->period }}限
                        {{ $course->name }}（{{ $course->teacher ?? '担当未定' }}）
                    </option>
                @endforeach
            </select>

            <button type="submit">登録する</button>
        </form>
    </main>
</body>
</html>
