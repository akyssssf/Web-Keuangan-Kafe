<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ringkasan') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar: #0F1C2E;
            --sidebar-soft: #8C97AC;
            --sidebar-hover: rgba(255,255,255,0.06);
            --page-bg: #F4F6FA;
            --card-bg: #FFFFFF;
            --border: #E5E9F0;
            --ink: #101828;
            --ink-soft: #667085;
            --green: #16A34A;
            --green-soft: #E9F7EF;
            --red: #E4483B;
            --red-soft: #FDECEB;
            --amber: #F59E0B;
            --amber-soft: #FEF6E7;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--page-bg);
            color: var(--ink);
            font-family: 'Public Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 246px;
            flex-shrink: 0;
            background: var(--sidebar);
            color: #C9D0DC;
            padding: 22px 16px;
            display: flex;
            flex-direction: column;
        }
        .brand { display:flex; align-items:center; gap:10px; margin-bottom: 26px; padding: 0 6px; }
        .brand-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: #fff; color: var(--sidebar);
            display:flex; align-items:center; justify-content:center;
            font-size: 16px; font-weight:800;
        }
        .brand-name { color:#fff; font-weight: 700; font-size: 15px; }
        .nav-group { margin-bottom: 20px; }
        .nav-label {
            font-size: 10.5px; font-weight: 700; letter-spacing: 0.06em;
            color: #64708A; text-transform: uppercase;
            margin: 0 0 8px 10px;
        }
        .nav-link {
            display: flex; align-items:center; gap: 10px;
            padding: 9px 10px;
            border-radius: 8px;
            color: #C9D0DC;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 2px;
        }
        .nav-link svg { flex-shrink:0; width:17px; height:17px; }
        .nav-link:hover { background: var(--sidebar-hover); }
        .nav-link.active {
            background: #fff;
            color: var(--sidebar);
            font-weight: 700;
        }
        .nav-bottom { margin-top: auto; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.08); }

        /* Main */
        .main-area { flex: 1; display:flex; flex-direction:column; min-width:0; }
        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--border);
            padding: 16px 32px;
            display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap;
        }
        .page-title { font-size: 19px; font-weight: 700; margin: 0 0 3px; }
        .page-desc { color: var(--ink-soft); font-size: 13.5px; margin: 0; }
        .topbar-right { display:flex; align-items:center; gap:14px; }
        .user-chip { display:flex; align-items:center; gap:10px; cursor:pointer; padding:4px 6px; border-radius:8px; color: var(--ink); }
        .user-chip:hover { background: var(--page-bg); }
        .avatar {
            width: 34px; height:34px; border-radius:50%;
            background: var(--sidebar); color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:13px;
        }
        .user-meta { line-height:1.25; }
        .user-name { font-size: 13.5px; font-weight:700; }
        .user-role { font-size: 11.5px; color: var(--ink-soft); }

        .main { flex:1; padding: 26px 32px 40px; max-width: 1240px; width:100%; }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 1px 2px rgba(16,24,40,0.03);
        }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 16px; border-radius: 8px;
            font-size: 14px; font-weight: 600;
            text-decoration: none; border: none; cursor: pointer; font-family: inherit;
        }
        .btn-primary { background: var(--sidebar); color: #fff; }
        .btn-primary:hover { background: #182B44; }
        .btn-ghost { background: #fff; color: var(--ink); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--page-bg); }
        .btn-danger-text { background: none; border: none; color: var(--red); cursor: pointer; font-size: 13px; font-family: inherit; padding: 0; text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11.5px; color: var(--ink-soft); font-weight: 700; text-transform:uppercase; letter-spacing:0.03em; padding: 10px 12px; border-bottom: 1px solid var(--border); }
        td { padding: 12px; border-bottom: 1px solid #F0F2F6; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        .num { font-family: 'IBM Plex Mono', monospace; font-variant-numeric: tabular-nums; }
        .tag { display: inline-block; padding: 3px 9px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        .tag-in { background: var(--green-soft); color: var(--green); }
        .tag-out { background: var(--red-soft); color: var(--red); }
        .flash { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
        .flash-ok { background: var(--green-soft); color: var(--green); }
        .flash-err { background: var(--red-soft); color: var(--red); }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--ink); }
        input, select, textarea {
            width: 100%; padding: 9px 11px;
            border: 1px solid var(--border); border-radius: 8px;
            font-family: inherit; font-size: 14px; background: #fff; color: var(--ink);
        }
        input:focus, select:focus, textarea:focus { outline: 2px solid var(--sidebar); outline-offset: 1px; border-color: var(--sidebar); }
        .field { margin-bottom: 16px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .stat-value { font-family: 'IBM Plex Mono', monospace; font-size: 25px; font-weight: 700; }
        .stat-label { font-size: 13px; color: var(--ink-soft); margin-bottom: 6px; }
        .divider-line { border: none; border-top: 1px dashed var(--border); margin: 18px 0; }
        a { color: var(--sidebar); }

        /* pine/brick aliases kept for existing views */
        :root {
            --pine: var(--green); --pine-soft: var(--green-soft);
            --brick: var(--red); --brick-soft: var(--red-soft);
            --brass: var(--amber);
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">☕</div>
            <div class="brand-name">{{ config('app.name') }}</div>
        </div>

        <div class="nav-group">
            <div class="nav-label">Umum</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                Ringkasan
            </a>
            <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15h6M9 11h3"/></svg>
                Catat Transaksi
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-label">Laporan (SPS)</div>
            <a href="{{ route('reports.laba-rugi') }}" class="nav-link {{ request()->routeIs('reports.laba-rugi') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M23 6l-9.5 9.5-5-5L1 18"/><path d="M17 6h6v6"/></svg>
                Laba Rugi
            </a>
            <a href="{{ route('reports.arus-kas') }}" class="nav-link {{ request()->routeIs('reports.arus-kas') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                Arus Kas
            </a>
            <a href="{{ route('reports.neraca') }}" class="nav-link {{ request()->routeIs('reports.neraca') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Neraca
            </a>
        </div>

        <div class="nav-group">
            <div class="nav-label">Pengaturan</div>
            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41L11 22l-9-9L2 3l10-1 9 9-.41 1.41z"/><circle cx="7" cy="7" r="1.5"/></svg>
                Kategori
            </a>
            <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                Pos Kas &amp; Aset
            </a>
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                Profil Saya
            </a>
        </div>

        <div class="nav-bottom">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link" style="width:100%; text-align:left; background:none; border:none; cursor:pointer; font-family:inherit;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="main-area">
        <div class="topbar">
            <div>
                <h1 class="page-title">@yield('title', 'Ringkasan')</h1>
                <p class="page-desc">@yield('desc', '')</p>
            </div>
            <div class="topbar-right">
                @yield('actions')
                @auth
                    <a href="{{ route('profile.edit') }}" class="user-chip" style="text-decoration:none;">
                        <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <div class="user-meta">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <div class="user-role">Admin</div>
                        </div>
                    </a>
                @endauth
            </div>
        </div>

        <main class="main">
            @if (session('status'))
                <div class="flash flash-ok">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="flash flash-err">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
