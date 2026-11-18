<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>授業の編集</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .field { margin-bottom: 20px; }
        label { display: block; margin-bottom: 6px; }
        input { padding: 8px; width: 250px; }
        .error { color: red; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>授業の編集</h1>

    <p>
        曜日：
        @switch($entry->day)
            @case(1) 月曜日 @break
            @case(2) 火曜日 @break
            @case(3) 水曜日 @break
            @case(4) 木曜日 @break
            @case(5) 金曜日 @break
        @endswitch
    </p>

    <p>時限：{{ $entry->period }}限</p>

    <form
        action="{{ url('/entries/' . $entry->id) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <div class="field">
            <label for="title">授業名</label>

            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title', $entry->title) }}"
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
                value="{{ old('room', $entry->room) }}"
            >

            @error('room')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">
            変更を保存
        </button>

        <a href="{{ url('/entries') }}">
            キャンセル
        </a>
    </form>
</body>
</html>
