<p>{{ $student->name }} さん</p>

<h2>{{ $announcement->title }}</h2>

<p>{{ $announcement->body }}</p>

<p>
    <a href="{{ url('/courses/' . $announcement->course_id) }}">
        授業のページを開く
    </a>
</p>

<p>POSSE大学 教務課</p>