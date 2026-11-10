<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>授業の新規登録</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .field { margin-bottom: 20px; }
        label { display: block; margin-bottom: 6px; }
        input { padding: 8px; width: 250px; }
        .error { color: red; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>授業の新規登録</h1>

    <form action="{{ url('/entries') }}" method="POST">
        @csrf

        <div class="field">
            <label for="day">曜日（1〜5）</label>

            <input
                type="number"
                id="day"
                name="day"
                min="1"
                max="5"
                value="{{ old('day') }}"
                required
            >

            @error('day')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="period">時限（1〜6）</label>

            <input
                type="number"
                id="period"
                name="period"
                min="1"
                max="6"
                value="{{ old('period') }}"
                required
            >

            @error('period')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="title">授業名</label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                required
            >

            @error('title')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="room">教室（任意）</label>

            <input
                type="text"
                id="room"
                name="room"
                value="{{ old('room') }}"
            >

            @error('room')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">登録する</button>

        <a href="{{ url('/entries') }}">
            一覧に戻る
        </a>
    </form>
</body>
</html>
