
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>わたしの時間割づくり | POSSE大学</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen">

    <header class="bg-[#1f3a5f] text-white">
        <div class="max-w-5xl mx-auto px-4 py-4">
            <p class="text-xs">POSSE大学 学務ポータル</p>
            <h1 class="text-xl font-bold">わたしの時間割づくり</h1>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-6">
        <div class="grid gap-6 md:grid-cols-2">

            <!-- 区画1：月曜日の授業 -->
            <section class="bg-white border rounded-lg p-4 overflow-x-auto">
                <h2 class="font-bold mb-2">区画1 月曜日の授業</h2>
                <p class="text-xs text-slate-500 mb-4">時限の早い順に並べています。</p>

                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-2">時限</th>
                            <th class="p-2">授業名</th>
                            <th class="p-2">担当教員</th>
                            <th class="p-2">教室</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mondayCourses as $course)
                            <tr class="border-t">
                                <td class="p-2">{{ $course->period }}</td>
                                <td class="p-2">{{ $course->name }}</td>
                                <td class="p-2">{{ $course->teacher }}</td>
                                <td class="p-2">{{ $course->room }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <!-- 区画2：必修科目 -->
            <section class="bg-white border rounded-lg p-4 overflow-x-auto">
                <h2 class="font-bold mb-2">区画2 必修科目</h2>
                <p class="text-xs text-slate-500 mb-4">科目コードの順に並べています。</p>

                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-2">授業名</th>
                            <th class="p-2">単位数</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requiredCourses as $course)
                            <tr class="border-t">
                                <td class="p-2">{{ $course->name }}</td>
                                <td class="p-2">{{ $course->credits }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

        </div>
    </main>
</body>
</html>
