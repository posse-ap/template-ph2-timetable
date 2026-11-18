<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ユーザー登録</title>
    <style>
        body { background: #f3f4f6; color: #1f2937; font-family: sans-serif; margin: 0; }
        main { background: white; border-radius: 8px; box-shadow: 0 8px 24px #0001; margin: 64px auto; max-width: 440px; padding: 32px; }
        h1 { margin-top: 0; }
        label { display: block; font-weight: bold; margin-top: 18px; }
        input { border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; font-size: 16px; margin-top: 6px; padding: 10px; width: 100%; }
        button { background: #2563eb; border: 0; border-radius: 4px; color: white; cursor: pointer; font-size: 16px; margin-top: 24px; padding: 11px 18px; width: 100%; }
        .error { color: #b91c1c; margin: 6px 0 0; }
        .link { margin-bottom: 0; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <main>
        <h1>ユーザー登録</h1>

        <form action="{{ url('/register') }}" method="POST">
            @csrf

            <label for="name">名前</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="email">メールアドレス</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="password">パスワード</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required>
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror

            <label for="password_confirmation">確認用パスワード</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

            <button type="submit">登録する</button>
        </form>

        <p class="link">登録済みの方は<a href="{{ url('/login') }}">ログイン</a></p>
    </main>
</body>
</html>
