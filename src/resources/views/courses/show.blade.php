<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>授業詳細</title>
</head>
<body>

    <h1>{{ $course->name }}</h1>

    <p>授業コード：{{ $course->code ?? '-' }}</p>
    <p>曜日：{{ $course->day ?? '-' }}</p>
    <p>時限：{{ $course->period ?? '-' }}</p>

    <a href="/courses">授業一覧へ戻る</a>

    <hr>

    <h2>レビューを投稿する</h2>

    <form method="POST"
          action="/courses/{{ $course->id }}/reviews">

        @csrf

        <textarea name="body" rows="5" cols="50"
                  maxlength="1000">{{ old('body') }}</textarea>

        @error('body')
            <p style="color:red">{{ $message }}</p>
        @enderror

        <button type="submit">投稿する</button>
    </form>

    <hr>

    <h2>レビュー一覧</h2>

    @forelse ($reviews as $review)
        <div>
            <p>投稿者：{{ $review->user->name }}</p>

            <p style="white-space: pre-line">{{ $review->body }}</p>

            @can('delete', $review)
                <form method="POST"
                      action="/reviews/{{ $review->id }}">
                    @csrf
                    @method('DELETE')

                    <button type="submit">削除する</button>
                </form>
            @endcan
        </div>

        <hr>
    @empty
        <p>まだレビューはありません。</p>
    @endforelse

</body>
</html>