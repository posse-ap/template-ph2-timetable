<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>授業一覧</title>
    <style>
        body {
            color: #1f2937;
            font-family: sans-serif;
            margin: 40px;
        }
        main { max-width: 1200px; }
        nav {
            display: flex;
            gap: 16px;
            margin-bottom: 28px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
        }
        th { background: #eff6ff; }
        button {
            background: #2563eb;
            border: 0;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            padding: 8px 12px;
        }
        button[disabled] {
            background: #9ca3af;
            cursor: default;
        }
        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px;
            margin-bottom: 15px;
        }
        .course-link { color: #2563eb; }
        .tag {
            display: inline-block;
            background: #dbeafe;
            padding: 4px 7px;
            border-radius: 6px;
            margin: 2px;
            color: #1d4ed8;
            font-size: 13px;
        }
        .favorite-button {
            background: transparent;
            color: #d97706;
            font-size: 24px;
            padding: 0;
        }
        .search-form { margin-bottom: 20px; }
        .search-form input {
            padding: 8px;
            width: 250px;
        }
    </style>
</head>
<body>
    <main>
        <nav>
            <a href="{{ url('/entries') }}">自分の時間割</a>
            <a href="{{ url('/courses') }}">授業一覧</a>
        </nav>

        <h1>授業一覧</h1>

        <!-- W8：授業名・教員名の検索 -->
        <form class="search-form"
              method="GET"
              action="{{ url('/courses') }}">

            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="授業名・教員名で検索"
            >

            <!-- タグ選択中は検索時にも引き継ぐ -->
            @if (request()->filled('tag'))
                <input type="hidden"
                       name="tag"
                       value="{{ request('tag') }}">
            @endif

            <button type="submit">検索</button>
        </form>

        @if (request()->filled('tag'))
            <p>
                タグで絞り込み中：
                <a href="{{ url('/courses') . '?' . http_build_query(['q' => request('q', '')]) }}">
                    タグ絞り込みを解除
                </a>
            </p>
        @endif

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>曜日</th>
                    <th>時限</th>
                    <th>科目コード</th>
                    <th>授業名</th>
                    <th>担当教員</th>
                    <th>教室</th>
                    <th>タグ</th>
                    <th>登録</th>
                    <th>気になる</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($courses as $course)
                    <tr>
                        <td>{{ $course->day_name }}</td>
                        <td>{{ $course->period }}限</td>
                        <td>{{ $course->code }}</td>

                        <!-- 授業詳細へ移動 -->
                        <td>
                            <a class="course-link"
                               href="{{ url('/courses/' . $course->id) }}">
                                {{ $course->name }}
                            </a>
                        </td>

                        <td>{{ $course->teacher ?? '未定' }}</td>
                        <td>{{ $course->room }}</td>

                        <!-- W9：タグ表示・絞り込み -->
                        <td>
                            @forelse ($course->tags as $tag)
                                <a class="tag"
                                   href="{{ url('/courses') . '?' . http_build_query(['q' => request('q', ''), 'tag' => $tag->id]) }}">
                                    {{ $tag->name }}
                                </a>
                            @empty
                                <span>タグなし</span>
                            @endforelse
                        </td>

                        <!-- W7：自分の時間割に登録 -->
                        <td>
                            @if ($course->timetableEntries->isNotEmpty())
                                <button type="button" disabled>
                                    登録済み
                                </button>
                            @else
                                <form action="{{ url('/entries') }}"
                                      method="POST">
                                    @csrf

                                    <input type="hidden"
                                           name="course_id"
                                           value="{{ $course->id }}">

                                    <button type="submit">登録</button>
                                </form>
                            @endif
                        </td>

                        <!-- W9：気になる追加・解除 -->
                        <td>
                            @if (in_array($course->id, $favoriteIds))
                                <form method="POST"
                                      action="{{ url('/courses/' . $course->id . '/favorite') }}">
                                    @csrf
                                    @method('DELETE')

                                    <button class="favorite-button"
                                            type="submit"
                                            title="気になるを解除">★</button>
                                </form>
                            @else
                                <form method="POST"
                                      action="{{ url('/courses/' . $course->id . '/favorite') }}">
                                    @csrf

                                    <button class="favorite-button"
                                            type="submit"
                                            title="気になるに追加">☆</button>
                                </form>
                            @endif

                            <small>
                                {{ $course->favorited_by_users_count }}人
                            </small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            条件に一致する授業はありません。
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>