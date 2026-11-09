<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal Kurir') - Samudera Fresh</title>
    <style>
        :root{--forest:#174c32;--berry:#df3d5b;--cream:#fffdf7;--paper:#fff;--ink:#242923;--muted:#77796f;--line:#eee9de}
        *{box-sizing:border-box}body{margin:0;background:var(--cream);color:var(--ink);font-family:Arial,sans-serif}
        button,a{font:inherit}a{color:inherit;text-decoration:none}
        .admin-shell{min-height:100vh;display:grid;grid-template-columns:248px minmax(0,1fr)}
        .sidebar{min-height:100vh;background:var(--forest);color:#fff;padding:20px 14px 14px;display:flex;flex-direction:column}
        .brand{display:flex;align-items:center;gap:8px;margin:0 4px 28px;font-weight:700;font-size:12px;line-height:1.3}
        .brand img{width:36px;height:36px;object-fit:cover;border-radius:50%;border:1px solid #ffffff70}
        .brand small{display:block;font-size:8px;font-weight:400;letter-spacing:.6px;color:#d5e5d9}
        .nav-list{display:grid;gap:5px}.nav-list a{display:block;padding:10px 12px;border-radius:4px;color:#dce9df;font-size:12px}
        .nav-list a:hover,.nav-list a.active{background:#fff;color:var(--forest)}
        .side-bottom{margin-top:auto;padding-top:22px}.side-bottom button{width:100%;border:1px solid #ffffff50;color:#fff;background:transparent;padding:9px;border-radius:4px;font-size:11px;cursor:pointer}
        .admin-main{min-width:0}.topbar{height:48px;padding:0 30px;display:flex;align-items:center;justify-content:flex-end}
        .topbar h1{position:absolute;left:-10000px}.admin-user{font-size:11px;color:#696b65;margin-right:14px}
        .logout-icon{width:24px;height:24px;border:0;border-radius:50%;background:transparent;color:var(--berry);font-size:18px;cursor:pointer}
        .content{max-width:1280px;margin:0 auto;padding:15px 30px 40px}.page-heading{display:flex;justify-content:space-between;align-items:center;gap:16px;margin:0 0 18px}
        .page-heading h2{margin:0;color:var(--ink);font-size:20px}.page-heading p{margin:5px 0 0;color:var(--muted);font-size:11px}
        .button{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:4px;background:var(--berry);color:#fff;padding:9px 15px;font-size:11px;cursor:pointer}
        .button:hover{background:#c8324e}.button-outline{background:#fff;color:var(--berry);border:1px solid var(--berry)}
        .cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:14px}
        .card,.panel{background:var(--paper);border:1px solid var(--line);border-radius:22px}
        .card{padding:14px 16px;min-height:68px}.card-label{color:var(--muted);font-size:10px}.card-value{font-size:18px;color:var(--berry);font-weight:700;margin-top:8px}
        .card-value.green{color:var(--forest)}.panel{padding:18px;margin-bottom:14px}.panel h3{margin:0 0 13px;color:var(--forest);font-size:13px}
        .shipment-list{display:grid;gap:8px}.shipment-row{display:flex;align-items:center;gap:12px;padding:10px 12px;border:1px solid var(--line);border-radius:6px;background:#fff;min-height:54px}
        .status{display:inline-block;min-width:78px;text-align:center;padding:6px 9px;border-radius:4px;background:#fbe9ed;color:var(--berry);font-size:9px;white-space:nowrap}
        .status-diproses,.status-dikirim,.status-diterima{background:#e9f2eb;color:var(--forest)}
        .shipment-route{flex:1;min-width:0;font-size:11px}.shipment-route strong{display:block}.shipment-route small{display:block;margin-top:4px;color:var(--muted);font-size:10px}
        .destination-list{display:flex;flex-wrap:wrap;gap:12px}.destination-list span{min-width:100px;text-align:center;padding:9px 14px;background:#eaf3ec;color:var(--forest);font-size:10px}
        .empty{text-align:center;padding:28px;color:var(--muted);font-size:12px}.flash{padding:10px 13px;border-radius:6px;margin-bottom:12px;font-size:11px}.flash-success{background:#e9f2eb;color:var(--forest)}.flash-error{background:#fbe9ed;color:#a3223d}
        .detail-card{padding:22px 24px;min-height:235px}.detail-title{margin:0 0 15px;color:var(--forest);font-size:14px}.detail-address{font-size:11px;line-height:1.7;color:#565a53}.detail-address strong{color:var(--ink)}
        .field{margin-bottom:13px}.field label{display:block;margin-bottom:6px;font-size:10px;font-weight:600;color:#383c35}
        .field input,.field select,.field textarea{width:100%;border:1px solid #ece9e1;border-radius:4px;padding:9px 10px;background:#fff;font-size:10px}
        .field textarea{min-height:82px;resize:vertical}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 16px}
        .profile-panel{max-width:800px}.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px 30px}
        .detail-label{font-size:9px;color:var(--muted);margin-bottom:5px}.detail-value{font-size:10px}
        .progress{display:flex;align-items:center;margin:24px 0;max-width:650px}.progress-step{display:flex;align-items:center;gap:6px;white-space:nowrap;color:var(--muted);font-size:10px}
        .progress-dot{width:9px;height:9px;border:1px solid #d7cfd0;border-radius:50%;background:#fff}.progress-step.done{color:var(--forest)}.progress-step.done .progress-dot{background:var(--berry);border-color:var(--berry)}
        .progress-line{height:1px;flex:1;min-width:14px;background:#e8cbd1;margin:0 8px}
        .detail-actions{display:flex;justify-content:flex-end}.confirmation{min-height:255px;display:flex;align-items:center;justify-content:center;text-align:center}.confirmation-check{font-size:38px;color:var(--berry);line-height:1}.confirmation h3{margin:9px 0;color:var(--forest);font-size:17px}.confirmation strong{font-size:10px}.confirmation p{margin:22px 0 18px;color:var(--muted);font-size:10px}
        .pagination{margin-top:12px;font-size:11px}
        @media(max-width:900px){.admin-shell{grid-template-columns:200px minmax(0,1fr)}.content{padding:14px 18px 32px}}
        @media(max-width:620px){.admin-shell{display:block}.sidebar{min-height:0;padding:10px;gap:8px}.brand{margin:0 0 8px}.nav-list{display:flex}.nav-list a{white-space:nowrap;padding:8px}.side-bottom{display:none}.topbar{height:34px;padding:0 14px}.content{padding:10px 12px 25px}.cards{grid-template-columns:repeat(2,minmax(0,1fr))}.page-heading h2{font-size:17px}.panel{padding:13px}.detail-card{padding:16px}.progress{overflow-x:auto;padding-bottom:7px}.shipment-row{align-items:flex-start;flex-wrap:wrap}.shipment-row .button{margin-left:auto}.form-grid,.detail-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('kurir.dashboard') }}">
            <img src="{{ asset('images/logo samudera.jpeg') }}" alt="Logo Samudera Fresh">
            <span>SAMUDERA FRESH<small>STRAWBERRY</small></span>
        </a>
        <nav class="nav-list" aria-label="Navigasi kurir">
            <a class="{{ request()->routeIs('kurir.dashboard') ? 'active' : '' }}" href="{{ route('kurir.dashboard') }}">Dashboard</a>
            <a class="{{ request()->routeIs('kurir.shipments*', 'kurir.confirmation') ? 'active' : '' }}" href="{{ route('kurir.shipments') }}">Pengiriman</a>
            <a class="{{ request()->routeIs('kurir.profile*') ? 'active' : '' }}" href="{{ route('kurir.profile') }}">Profil</a>
        </nav>
        <div class="side-bottom"><form method="POST" action="{{ route('kurir.logout') }}">@csrf<button type="submit">Keluar dari Kurir</button></form></div>
    </aside>
    <div class="admin-main">
        <header class="topbar">
            <h1>@yield('title', 'Portal Kurir')</h1>
            <div class="admin-user">Kurir: <strong>{{ session('nama_petugas', 'Kurir') }}</strong></div>
            <form method="POST" action="{{ route('kurir.logout') }}">@csrf<button class="logout-icon" type="submit" aria-label="Keluar">×</button></form>
        </header>
        <main class="content">
            @if (session('success'))<div class="flash flash-success" role="status">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="flash flash-error" role="alert">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="flash flash-error" role="alert">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
