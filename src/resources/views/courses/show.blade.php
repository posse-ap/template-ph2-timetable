<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $course->name }}｜授業詳細</title>
    <style>
        body { background: #f9fafb; color: #1f2937; font-family: sans-serif; margin: 40px; }
        main { margin: 0 auto; max-width: 900px; }
        nav { display: flex; gap: 16px; margin-bottom: 28px; }
        .card { background: white; border: 1px solid #d1d5db; border-radius: 8px; margin-bottom: 20px; padding: 24px; }
        .tag { background: #dbeafe; border-radius: 6px; color: #1d4ed8; display: inline-block; margin: 4px; padding: 6px 12px; }
        input, textarea { box-sizing: border-box; display: block; margin: 8px 0 16px; padding: 10px; width: 100%; }
        button { background: #2563eb; border: 0; border-radius: 5px; color: white; cursor: pointer; padding: 10px 18px; }
        .delete-button { background: #dc2626; }
        .error { color: #dc2626; }
        .item { border-bottom: 1px solid #e5e7eb; padding: 15px 0; }
    </style>
</head>
<body>
<main>
    <nav>
        <a href="{{ url('/entries') }}">自分の時間割</a>
        <a href="{{ url('/courses') }}">授業一覧</a>
    </nav>

    <section class="card">
        <h1>{{ $course->name }}</h1>
        <p>科目コード：{{ $course->code }}</p>
        <p>担当教員：{{ $course->teacher ?? '未定' }}</p>
        <p>{{ $course->day_name }} {{ $course->period }}限／{{ $course->room ?? '未定' }}</p>
    </section>

    <section class="card">
        <h2>授業のタグ</h2>
        @forelse ($course->tags as $tag)
            <a class="tag" href="{{ url('/courses') . '?tag=' . $tag->id }}">{{ $tag->name }}</a>
        @empty
            <p>この授業にはまだタグがありません。</p>
        @endforelse
    </section>

    <section class="card">
        <h2>受講者一覧（{{ $course->students_count }}人）</h2>
        <ul>
            @forelse ($course->students as $student)
                <li>{{ $student->name }}</li>
            @empty
                <li>まだ受講者はいません。</li>
            @endforelse
        </ul>
    </section>

    @can('decide-enrollments')
        <section class="card">
            <h2>お知らせを投稿する</h2>
            <form method="POST" action="{{ url('/courses/' . $course->id . '/announcements') }}">
                @csrf
                <label for="title">タイトル（100文字以内）</label>
                <input id="title" name="title" maxlength="100" value="{{ old('title') }}" required>
                @error('title')<p class="error">{{ $message }}</p>@enderror

                <label for="announcement-body">本文（100文字以内）</label>
                <textarea id="announcement-body" name="body" maxlength="100" rows="5" required>{{ old('body') }}</textarea>
                @error('body')<p class="error">{{ $message }}</p>@enderror

                <button type="submit">お知らせを投稿</button>
            </form>
        </section>
    @endcan

    <section class="card">
        <h2>最近のお知らせ（過去24時間）</h2>
        @forelse ($announcements as $announcement)
            <article class="item">
                <h3>{{ $announcement->title }}</h3>
                <p>{{ $announcement->body }}</p>
                <small>{{ $announcement->created_at->format('Y/m/d H:i') }}</small>
            </article>
        @empty
            <p>最近のお知らせはありません。</p>
        @endforelse
    </section>

    <section class="card">
        <h2>レビューを投稿する</h2>
        <form method="POST" action="{{ url('/courses/' . $course->id . '/reviews') }}">
            @csrf
            <textarea name="body" rows="6" maxlength="1000" placeholder="授業を受けた感想を入力してください">{{ old('body') }}</textarea>
            @error('body')<p class="error">{{ $message }}</p>@enderror
            <button type="submit">レビューを投稿する</button>
        </form>
    </section>

    <section class="card">
        <h2>レビュー一覧</h2>
        @forelse ($reviews as $review)
            <article class="item">
                <p><strong>投稿者：{{ $review->user->name }}</strong></p>
                <p>{{ $review->body }}</p>
                <small>{{ $review->created_at->format('Y/m/d H:i') }}</small>
                @can('delete', $review)
                    <form method="POST" action="{{ url('/reviews/' . $review->id) }}">
                        @csrf
                        @method('DELETE')
                        <button class="delete-button" type="submit">レビューを削除</button>
                    </form>
                @endcan
            </article>
        @empty
            <p>まだレビューは投稿されていません。</p>
        @endforelse
    </section>
</main>
</body>
</html>
