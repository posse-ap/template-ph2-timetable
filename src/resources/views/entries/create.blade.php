<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>授業登録</title>
</head>
<body>
    <h1>授業登録</h1>

    <form method="POST" action="/entries">
        @csrf

        <div>
            <label for="course_id">授業</label>
            <select name="course_id" id="course_id">
                <option value="">選択してください</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ ['', '月', '火', '水', '木', '金'][$course->day] }}{{ $course->period }} {{$course->name}}({{$course->teacher}})</option>         
                @endforeach
            </select>
            @error('course_id')
                <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">登録する</button>
    </form>
</body>
</html>
