<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>授業の編集</title>
</head>
<body>

    <div class="container">
        <h1>授業の編集</h1>
        <form method="POST" action="{{ route('entries.update', $entry->id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="title">授業名</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $entry->title) }}">
                @error('title')
                    <p> {{ $message }}</p>
                @enderror
                <label for="room">教室</label>
                <input type="text" class="form-control" id="room" name="room" value="{{ old('room', $entry->room) }}">
                @error('room')
                    <p> {{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">更新</button>

        </form>
    </div>
</body>
</html>