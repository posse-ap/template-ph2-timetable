<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>時間割</title>
</head>
<body>

<h1>時間割</h1>
<table border="1">
    <thead>
        <tr>
            <th>授業コード</th>
            <th>授業名</th>
            <th>曜日</th>
            <th>時限</th>
            <th>教室</th>
        </tr>
    </thead>

   <tbody>
    @foreach ($courses as $course)
    <tr>
                <td>{{ $course->code }}</td>
                <td>{{ $course->name }}</td>
                <td>{{ $course->day }}</td>
                <td>{{ $course->period }}</td>
                <td>{{ $course->room }}</td>
            </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
