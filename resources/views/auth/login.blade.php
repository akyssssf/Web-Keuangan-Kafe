<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F4F6FA;
            font-family: 'Public Sans', sans-serif;
            color: #101828;
        }
        .login-card {
            width: 100%;
            max-width: 380px;
            background: #fff;
            border: 1px solid #E5E9F0;
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 1px 3px rgba(16,24,40,0.06);
        }
        .logo-box {
            width: 44px; height: 44px;
            background: #0F1C2E;
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 18px;
        }
        h1 { font-size: 20px; font-weight: 700; margin: 0 0 4px; }
        .sub { font-size: 13.5px; color: #667085; margin: 0 0 26px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        input[type=email], input[type=password] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #D0D5DD;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            margin-bottom: 16px;
        }
        input:focus { outline: 2px solid #0F1C2E; outline-offset: 1px; border-color: #0F1C2E; }
        .row-check { display:flex; align-items:center; gap:8px; margin-bottom: 20px; font-size: 13.5px; color:#475467; }
        button {
            width: 100%;
            padding: 11px;
            background: #0F1C2E;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14.5px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
        }
        button:hover { background: #182B44; }
        .err {
            background: #FEF3F2;
            color: #B42318;
            border: 1px solid #FEE4E2;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .hint {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #EEF1F5;
            font-size: 12.5px;
            color: #98A2B3;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-box">☕</div>
        <h1>Masuk ke {{ config('app.name') }}</h1>
        <p class="sub">Kelola pencatatan keuangan kafe kamu di sini.</p>

        @if ($errors->any())
            <div class="err">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@kafe.test" required autofocus>

            <label>Kata Sandi</label>
            <input type="password" name="password" placeholder="••••••••" required>

            <div class="row-check">
                <input type="checkbox" name="remember" id="remember" style="width:auto; margin:0;">
                <label for="remember" style="margin:0; font-weight:400;">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit">Masuk</button>
        </form>

        <div class="hint">
            Akun bawaan: <strong>admin@kafe.test</strong> / <strong>kafe12345</strong><br>
            (silakan ganti setelah login pertama kali)
        </div>
    </div>
</body>
</html>
