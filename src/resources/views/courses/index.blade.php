<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>授業一覧</title>
</head>
<body>
    <h1>授業一覧</h1>
    @error('course_id')
        <p>{{ $message }}</p>
    @enderror

    <form method="GET" action="/courses">
        <input type="text" name="q" value="{{ $q }}">
        <button type="submit">検索</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>曜日</th>
                <th>時限</th>
                <th>授業名</th>
                <th>担当教員</th>
                <th>教室</th>
                <th>受講者数</th>
                <th>タグ</th>
                <th>状態</th>
                <th>お気に入り</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $course)
                <tr>
                    <td>{{ ['', '月', '火', '水', '木', '金'][$course->day] }}</td>
                    <td>{{ $course->period }}限</td>
                    <td>
                        <a href="/courses/{{ $course->id }}">{{ $course->name }}</a>
                    </td>
                    <td>{{ $course->teacher }}</td>
                    <td>{{ $course->room }}</td>
                    <td>{{ $course->users_count }}人 / 定員：{{ $course->capacity }}人</td>
                    <td>
                        @foreach ($course->tags as $tag)
                            <a href="/courses?tag={{ $tag->id }}&q={{ $q }}">{{ $tag->name }}</a>
                        @endforeach
                    </td>
                    <td>
                        @php($myEntry = $course->entries->first())
                        @if (!$myEntry)
                            <form method="POST" action="/entries" style="display:inline">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <button type="submit">登録申請</button>
                            </form>
                        @elseif ($myEntry->status === \App\Models\TimetableEntry::PENDING)
                            <span>申請中</span>
                        @elseif ($myEntry->status === \App\Models\TimetableEntry::APPROVED)
                            <span>承認済み</span>
                        @elseif ($myEntry->status === \App\Models\TimetableEntry::REJECTED)
                            <span>却下</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="/courses/{{ $course->id }}/favorites" style="display:inline">
                            @csrf
                            <button type="submit">
                                {{ $course->favoritedBy->isNotEmpty() ? '★' : '☆' }}
                            </button>
                        </form>
                        {{ $course->favorited_by_count }}人
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>