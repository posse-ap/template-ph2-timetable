<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>時間割</title>
</head>
<body>
    <h1>時間割</h1>

    <h2>区画1：月曜日の授業</h2>
    <table border="1">
        <thead>
            <tr>
                <th>時限</th>
                <th>授業名</th>
                <th>担当教員</th>
                <th>教室</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses1 as $course)
                <tr>
                    <td>{{ $course->period }}</td>
                    <td>{{ $course->name }}</td>
                    <td>{{ $course->teacher }}</td>
                    <td>{{ $course->room }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>区画2：必修科目</h2>
    <table border="1">
        <thead>
            <tr>
                <th>授業名</th>
                <th>単位数</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses2 as $course)
                <tr>
                    <td>{{ $course->name }}</td>
                    <td>{{ $course->credits }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
