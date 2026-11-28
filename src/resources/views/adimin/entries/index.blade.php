<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>履修申請の管理</title>
</head>
<body>

    <h1>履修申請一覧（管理者）</h1>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    @endif

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>学生</th>
                <th>授業名</th>
                <th>承認人数 / 定員</th>
                <th>状態</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody>
            @forelse($entries as $entry)
                <tr>
                    <td>{{ $entry->user->name }}</td>

                    <td>{{ $entry->course->name }}</td>

                    <td>
                        {{ $entry->course->approved_entries_count }}
                        /
                        {{ $entry->course->capacity }}
                    </td>

                    <td>申請中</td>

                    <td>
                        <form method="POST"
                            action="{{ route('admin.entries.approve', $entry->id) }}">
                            @csrf
                            <button type="submit">承認</button>
                        </form>

                        <form method="POST"
                            action="{{ route('admin.entries.reject', $entry->id) }}">
                            @csrf
                            <button type="submit">却下</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        現在、申請中の授業はありません。
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>