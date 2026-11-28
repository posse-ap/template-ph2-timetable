<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->name }} | 授業詳細</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 40px;
            color: #1f2937;
            line-height: 1.7;
        }

        main {
            max-width: 800px;
            margin: auto;
        }

        section {
            margin-top: 32px;
            padding: 24px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        input, textarea {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin: 8px 0 16px;
        }

        button {
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .announcement {
            border-bottom: 1px solid #d1d5db;
            padding: 12px 0;
        }

        .error {
            color: #dc2626;
        }
    </style>
</head>
<body>
<main>

    <a href="/courses">← 授業一覧に戻る</a>

    <h1>{{ $course->name }}</h1>

    <p>科目コード：{{ $course->code }}</p>

    <!-- 管理者用：お知らせ投稿 -->
    @can('decide-enrollments')
        <section>
            <h2>お知らせを投稿する</h2>

            <form method="POST"
                  action="/courses/{{ $course->id }}/announcements">

                @csrf

                <label for="title">タイトル（100文字以内）</label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    maxlength="100"
                    value="{{ old('title') }}"
                    required
                >

                @error('title')
                    <p class="error">{{ $message }}</p>
                @enderror

                <label for="body">本文（100文字以内）</label>
                <textarea
                    id="body"
                    name="body"
                    maxlength="100"
                    rows="5"
                    required
                >{{ old('body') }}</textarea>

                @error('body')
                    <p class="error">{{ $message }}</p>
                @enderror

                <button type="submit">お知らせを投稿</button>
            </form>
        </section>
    @endcan

    <!-- お知らせ一覧 -->
    <section>
        <h2>最近のお知らせ（過去24時間）</h2>

        @forelse ($announcements as $announcement)
            <div class="announcement">

                <h3>{{ $announcement->title }}</h3>

                <p>{{ $announcement->body }}</p>

                <small>
                    投稿日時：
                    {{ $announcement->created_at->format('Y/m/d H:i') }}
                </small>

            </div>
        @empty
            <p>最近のお知らせはありません。</p>
        @endforelse
    </section>

</main>
</body>
</html>
