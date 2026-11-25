<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>承認の画面</title>
</head>
<body>
    <div>
        <span>{{ Auth::user()->name }}さん（管理者）</span>
        <form method="POST" action="/logout" style="display:inline">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    </div>
    <h1>申請中の履修申請</h1>
    @error('entry')
        <p>{{ $message }}</p>
    @enderror
    @if ($entries->isEmpty())
        <p>申請中の履修申請はありません</p>
    @endif
    <a href="/courses">授業一覧へ戻る</a>
    <a href="/admin/entries">承認画面へ</a>
    <a href="/entries">自分の時間割へ</a>

    <table border="1">
        <thead>
            <tr>
                <th>学生</th>
                <th>授業</th>
                <th>承認済み / 定員</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $entry)
                <tr>
                    <td>{{ $entry->user->name }}</td>
                    <td>{{ $entry->course->name }}</td>
                    <td>{{ $entry->course->users_count }}人 / 定員：{{ $entry->course->capacity }}人</td>
                    <td>
                        <form method="POST" action="/admin/entries/{{ $entry->id }}/approve" style="display:inline">
                            @csrf
                            <button type="submit">承認</button>
                        </form>
                        <form method="POST" action="/admin/entries/{{ $entry->id }}/reject" style="display:inline">
                            @csrf
                            <button type="submit">却下</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">申請中の申請はありません</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
