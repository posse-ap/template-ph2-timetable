<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>履修申請の管理</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 12px; }
        th { background: #eff6ff; }
        form { display: inline-block; }
        button { padding: 8px 12px; cursor: pointer; }
        .approve { background: #2563eb; color: white; }
        .reject { background: #dc2626; color: white; }
        .error { color: #dc2626; }
        .success { color: #15803d; }
    </style>
</head>
<body>
    <h1>管理者：履修申請一覧</h1>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <p class="error">{{ $error }}</p>
        @endforeach
    @endif

    <table>
        <thead>
            <tr>
                <th>申請ID</th>
                <th>学生</th>
                <th>授業名</th>
                <th>承認済み / 定員</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->user->name }}</td>
                    <td>{{ $entry->course->name }}</td>
                    <td>
                        {{ $entry->course->approved_entries_count }}
                        /
                        {{ $entry->course->capacity }}
                    </td>
                    <td>
                        <form method="POST"
                              action="{{ route('admin.entries.approve', $entry) }}">
                            @csrf
                            <button class="approve" type="submit">
                                承認
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('admin.entries.reject', $entry) }}">
                            @csrf
                            <button class="reject" type="submit">
                                却下
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">申請中の授業はありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
