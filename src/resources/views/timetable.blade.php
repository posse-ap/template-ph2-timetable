<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>時間割</title>
</head>
<body>
    <h1>時間割</h1>

    <h2>月曜日の授業</h2>
    <table border="1">
        <tr>
            <th>時限</th>
            <th>授業名</th>
            <th>担当教員</th>
            <th>教室</th>
        </tr>
        @foreach ($courses as $course)
            <tr>
                <td>{{ $course->period }}限</td>
                <td>{{ $course->name }}</td>
                <td>{{ $course->teacher }}</td>
                <td>{{ $course->room }}</td>
            </tr>
        @endforeach
    </table>

    <h2>必修科目</h2>
    <table border="1">
        <tr>
            <th>授業名</th>
            <th>単位数</th>
        </tr>
        @foreach ($requiredCourses as $course)
            <tr>
                <td>{{ $course->name }}</td>
                <td>{{ $course->credits }}</td>
            </tr>
        @endforeach
    </table>
<!-- Do what you can, with what you have, where you are. - Theodore Roosevelt -->
</body>
</html>