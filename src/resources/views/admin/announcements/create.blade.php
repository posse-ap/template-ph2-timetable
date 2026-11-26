<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>お知らせを出す</title>
</head>
<body>
    <div>
        <span>{{ Auth::user()->name }}さん（管理者）</span>
        <form method="POST" action="/logout" style="display:inline">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    </div>
    <h1>{{ $course->name }} のお知らせを出す</h1>
    <p>出すと、この授業を履修している（承認済みの）学生にメールが届きます。</p>

    <form method="POST" action="/admin/courses/{{ $course->id }}/announcements">
        @csrf
        <p>
            <label>タイトル（40文字まで）<br>
                <input type="text" name="title" value="{{ old('title') }}" size="50">
            </label>
            @error('title')
                <br><span>{{ $message }}</span>
            @enderror
        </p>
        <p>
            <label>本文（200文字まで）<br>
                <textarea name="body" rows="8" cols="50">{{ old('body') }}</textarea>
            </label>
            @error('body')
                <br><span>{{ $message }}</span>
            @enderror
        </p>
        <button type="submit">お知らせを出す</button>
    </form>

    <p><a href="/courses/{{ $course->id }}">授業詳細へ戻る</a></p>
</body>
</html>
