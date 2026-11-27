<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>わたしの時間割づくり | POSSE大学</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen">

    <main class="mx-auto max-w-5xl px-4 py-6">
    <div class="mb-4 flex justify-end">
        <a href="{{ url('/courses') }}" class="rounded-lg bg-[#1f3a5f] px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">授業一覧に戻る</a>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
    <section class="min-w-0 bg-white border border-slate-200 rounded-lg">
        <div class="px-5 pt-4 pb-3 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-800"><span class="text-slate-500 mr-2">区画1</span>月曜日の授業</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-700">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                <th class="px-4 py-2 font-medium whitespace-nowrap">時限</th>
                <th class="px-4 py-2 font-medium whitespace-nowrap">授業名</th>
                <th class="px-4 py-2 font-medium whitespace-nowrap">担当教員</th>
                <th class="px-4 py-2 font-medium whitespace-nowrap">教室</th>
                <th class="px-4 py-2 font-medium whitespace-nowrap">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                <tr class="border-t border-slate-200">
                <td class="px-4 py-2 whitespace-nowrap">
                {{ $entry->course->day }} / {{ $entry->course->period }}
                </td>
                <td class="px-4 py-2 whitespace-nowrap">
                {{ $entry->course->name }}
                </td>
                <td class="px-4 py-2 whitespace-nowrap">
                {{ $entry->course->teacher }}
                </td>
                <td class="px-4 py-2 whitespace-nowrap">
                {{ $entry->course->room }}
                </td>
                <td class="px-4 py-2 whitespace-nowrap">
                    <form action="{{ url('/entries/' . $entry->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit">削除</button>
                    </form>
                </td>
                </tr>
                @endforeach        
            </tbody>
            </table>
        </div>
        </section>

        <section class="min-w-0 bg-white border border-slate-200 rounded-lg">
        <div class="px-5 pt-4 pb-3 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-800"><span class="text-slate-500 mr-2">区画2</span>必修科目</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-700">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                <th class="px-4 py-2 font-medium whitespace-nowrap">授業名</th>
                <th class="px-4 py-2 font-medium whitespace-nowrap">単位数</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requiredClasses as $course)
                <tr class="border-t border-slate-200">
                    <td class="px-4 py-2 whitespace-nowrap">{{ $course->name }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $course->credits }}</td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        </section>
    </div>
</main>
</body>
</html>
