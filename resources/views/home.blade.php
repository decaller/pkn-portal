<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PKN Portal') }}</title>
    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #475569;
            --accent: #0f766e;
            --accent-hover: #115e59;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            color: var(--text);
            background: radial-gradient(circle at top, #d1fae5 0%, var(--bg) 45%);
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 620px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
        }

        h1 {
            margin: 0 0 10px;
            font-size: 1.875rem;
            line-height: 1.2;
        }

        p {
            margin: 0 0 20px;
            color: var(--muted);
        }

        .links {
            display: grid;
            gap: 12px;
        }

        .btn {
            display: block;
            text-decoration: none;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            border-radius: 10px;
            padding: 12px 14px;
            font-weight: 600;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }

        .btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn.primary {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .btn.primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
        }
    </style>
</head>

<body>
    <main class="card">
        <h1>{{ config('app.name', 'PKN Portal') }}</h1>
        <p>Select where you want to sign in.</p>

        <div class="links">
            <a class="btn primary" href="{{ route('filament.user.auth.login') }}">User Login</a>
            <a class="btn" href="{{ route('filament.user.auth.register') }}">User Register</a>
            <a class="btn" href="{{ route('filament.admin.auth.login') }}">Admin Login</a>
            <a class="btn" href="{{ url('/public') }}">Public Panel</a>
        </div>
    </main>
</body>

</html>