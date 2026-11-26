<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p>{{ $announcement->course->name }} のお知らせです。</p>
    <h2>{{ $announcement->title }}</h2>
    <p style="white-space: pre-wrap;">{{ $announcement->body }}</p>
    <p>発信日時：{{ $announcement->published_at->format('Y年n月j日 H:i') }}</p>
    <p>POSSE大学 教務課</p>
</body>
</html>
