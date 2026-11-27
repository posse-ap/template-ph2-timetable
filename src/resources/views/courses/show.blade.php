<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $course->name }}｜授業詳細</title>

    <style>
        body {
            font-family: sans-serif;
            color: #1f2937;
            margin: 40px;
            background: #f9fafb;
        }

        main {
            max-width: 900px;
            margin: 0 auto;
        }

        nav {
            display: flex;
            gap: 16px;
            margin-bottom: 28px;
        }

        a {
            color: #2563eb;
        }

        .card {
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
        }

        h1 {
            margin-top: 0;
        }

        h2 {
            border-bottom: 2px solid #dbeafe;
            padding-bottom: 10px;
        }

        .tag {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 6px 12px;
            border-radius: 6px;
            margin: 4px;
            text-decoration: none;
        }

        .tag:hover {
            background: #bfdbfe;
        }

        .student-list {
            padding-left: 25px;
        }

        .student-list li {
            margin-bottom: 8px;
        }

        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 18px;
            cursor: pointer;
            margin-top: 12px;
        }

        button:hover {
            background: #1d4ed8;
        }

        .delete-button {
            background: #dc2626;
        }

        .delete-button:hover {
            background: #b91c1c;
        }

        .error {
            color: #dc2626;
            margin-top: 8px;
        }

        .review {
            border-bottom: 1px solid #e5e7eb;
            padding: 15px 0;
        }

        .review-body {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .review-user {
            font-weight: bold;
        }

        .empty {
            color: #6b7280;
        }
    </style>
</head>

<body>
<main>

    <!-- ナビゲーション -->
    <nav>
        <a href="{{ url('/entries') }}">
            自分の時間割
        </a>

        <a href="{{ url('/courses') }}">
            授業一覧
        </a>
    </nav>


    <!-- ① 授業の基本情報 -->
    <section class="card">

        <h1>{{ $course->name }}</h1>

        <p>
            <strong>科目コード：</strong>
            {{ $course->code ?? '未設定' }}
        </p>

        <p>
            <strong>担当教員：</strong>
            {{ $course->teacher ?? '未定' }}
        </p>

        <p>
            <strong>曜日：</strong>
            {{ $course->day_name ?? '未設定' }}
        </p>

        <p>
            <strong>時限：</strong>
            {{ $course->period ?? '-' }}限
        </p>

        <p>
            <strong>教室：</strong>
            {{ $course->room ?? '未定' }}
        </p>

    </section>


    <!-- ② W9：授業のタグ -->
    <section class="card">

        <h2>授業のタグ</h2>

        @forelse ($course->tags as $tag)

            <a
                class="tag"
                href="{{ url('/courses') . '?tag=' . $tag->id }}"
            >
                {{ $tag->name }}
            </a>

        @empty

            <p class="empty">
                この授業にはまだタグがありません。
            </p>

        @endforelse

    </section>


    <!-- ③ W9：受講者数・受講者一覧 -->
    <section class="card">

        <h2>受講者一覧</h2>

        <p>
            <strong>受講者数：</strong>
            {{ $course->students_count }}人
        </p>

        <h3>この授業を履修している学生</h3>

        <ul class="student-list">

            @forelse ($course->students as $student)

                <li>
                    {{ $student->name }}
                </li>

            @empty

                <li class="empty">
                    まだ受講者はいません。
                </li>

            @endforelse

        </ul>

    </section>


    <!-- ④ W8：レビュー投稿フォーム -->
    <section class="card">

        <h2>レビューを投稿する</h2>

        <form
            method="POST"
            action="{{ url('/courses/' . $course->id . '/reviews') }}"
        >

            @csrf

            <label for="review-body">
                レビュー本文（1000文字以内）
            </label>

            <textarea
                id="review-body"
                name="body"
                rows="6"
                maxlength="1000"
                placeholder="授業を受けた感想を入力してください"
            >{{ old('body') }}</textarea>

            <!-- 入力エラーを表示 -->
            @error('body')
                <p class="error">
                    {{ $message }}
                </p>
            @enderror

            <button type="submit">
                レビューを投稿する
            </button>

        </form>

    </section>


    <!-- ⑤ W8：レビュー一覧 -->
    <section class="card">

        <h2>レビュー一覧</h2>

        @forelse ($reviews as $review)

            <div class="review">

                <!-- 投稿者 -->
                <p class="review-user">
                    投稿者：{{ $review->user->name }}
                </p>

                <!-- 投稿日時 -->
                <p class="empty">
                    投稿日時：
                    {{ $review->created_at->format('Y/m/d H:i') }}
                </p>

                <!-- レビュー本文：XSS対策 -->
                <p class="review-body">
                    {{ $review->body }}
                </p>

                <!-- 本人のみ削除ボタンを表示 -->
                @can('delete', $review)

                    <form
                        method="POST"
                        action="{{ url('/reviews/' . $review->id) }}"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="delete-button"
                            onclick="return confirm('このレビューを削除しますか？')"
                        >
                            レビューを削除
                        </button>

                    </form>

                @endcan

            </div>

        @empty

            <p class="empty">
                まだレビューは投稿されていません。
            </p>

        @endforelse

    </section>


    <!-- 授業一覧に戻る -->
    <p>
        <a href="{{ url('/courses') }}">
            ← 授業一覧に戻る
        </a>
    </p>

</main>
</body>
</html>
