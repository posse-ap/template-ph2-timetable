<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>授業詳細</title>
</head>
<body>
    <h1>{{ $course->name }}</h1>
    <p>
        {{ ['', '月', '火', '水', '木', '金'][$course->day] }}{{ $course->period }}限 /
        担当: {{ $course->teacher }} / 教室: {{ $course->room }}
    </p>

    <p>タグ：
        @forelse ($course->tags as $tag)
            {{ $tag->name }}
        @empty
            なし
        @endforelse
    </p>
    <p>受講者数：{{ $course->users->count() }}人 / 定員：{{ $course->capacity }}人</p>
    <ul>
        @forelse ($course->users as $user)
            <li>{{ $user->name }}</li>
        @empty
            <li>まだ受講者がいません</li>
        @endforelse
    </ul>

    <h2>お知らせ</h2>
    @can('admin')
        <p><a href="/admin/courses/{{ $course->id }}/announcements/create">お知らせを出す</a></p>
    @endcan
    @forelse ($announcements as $announcement)
        <p>{{ $announcement->published_at->format('Y/m/d H:i') }}　{{ $announcement->title }}</p>
    @empty
        <p>お知らせはありません</p>
    @endforelse

    <h2>レビュー</h2>
    @forelse ($reviews as $review)
        <p>{{ $review->user->name }}: {{ $review->body }}</p>
        <form method="POST" action="/reviews/{{ $review->id }}" style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit">削除</button>
        </form>
    @empty
        <p>まだレビューがありません</p>
    @endforelse

    <h2>レビューを投稿</h2>
    <form method="POST" action="/courses/{{ $course->id }}/reviews">
        @csrf
        <textarea name="body">{{ old('body') }}</textarea>
        @error('body')
            <p>{{ $message }}</p>
        @enderror
        <button type="submit">投稿</button>
    </form>

    <a href="/courses">授業一覧へ戻る</a>
</body>
</html>