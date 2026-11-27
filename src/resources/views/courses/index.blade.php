<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>授業一覧</title>
</head>
<body>
    <h1>授業一覧</h1>

    @if ($errors->any())
    <p role="alert">{{ $errors->first('course_id') }}</p>
    @endif

    @foreach ($courses as $course)
        <section>
            <p>
                {{ ['月', '火', '水', '木', '金'][$course->day - 1] }}曜
                {{ $course->period }}限
                {{ $course->name }}（{{ $course->teacher }}）
            </p>

            @if ($course->timetableEntries->isNotEmpty())
                <p>登録済み</p>
            @else
                <form action="{{ url('/entries') }}" method="POST">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <button type="submit">登録する</button>
                </form>
            @endif
        </section>
    @endforeach
</body>
</html>