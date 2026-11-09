<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Admin') - Samudera Fresh</title>
    <style>
        :root{--forest:#174c32;--forest-dark:#103d28;--berry:#df3d5b;--cream:#fffdf7;--paper:#fff;--ink:#242923;--muted:#77796f;--line:#eee9de;--soft:#faf8f2}
        *{box-sizing:border-box}body{margin:0;background:var(--cream);color:var(--ink);font-family:Arial,sans-serif}
        button,input,select,textarea{font:inherit}a{color:inherit;text-decoration:none}
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
        .logout-icon{width:24px;height:24px;display:grid;place-items:center;border:0;border-radius:50%;background:transparent;color:var(--berry);font-size:18px;cursor:pointer}
        .content{max-width:1280px;margin:0 auto;padding:15px 30px 40px}.page-heading{display:flex;justify-content:space-between;align-items:center;gap:16px;margin:0 0 18px}
        .page-heading h2{margin:0;color:var(--ink);font-size:20px;font-weight:700}.page-heading p{margin:5px 0 0;color:var(--muted);font-size:11px}
        .button{display:inline-flex;align-items:center;justify-content:center;gap:6px;border:0;border-radius:4px;background:var(--berry);color:#fff;padding:9px 15px;font-size:11px;cursor:pointer}
        .button:hover{background:#c8324e}.button-outline{background:#fff;color:var(--berry);border:1px solid var(--berry)}.button-small{padding:6px 10px;font-size:10px}
        .cards{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:14px}
        .card,.panel{background:var(--paper);border:1px solid var(--line);border-radius:22px}
        .card{padding:14px 16px;min-height:68px}.card-label{color:#77796f;font-size:10px}.card-value{font-size:18px;color:var(--berry);font-weight:700;margin-top:8px}
        .card-value.green{color:var(--forest)}.card-value.gold{color:#cf9d30}
        .panel{padding:16px;margin-bottom:14px}.panel h3{margin:0 0 12px;color:var(--forest);font-size:12px}
        .table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;min-width:600px}th,td{text-align:left;padding:10px 9px;border-bottom:1px solid #f0ede6;font-size:11px;vertical-align:middle}
        th{color:#55594f;font-size:10px;font-weight:700;background:#fbfaf6}tr:last-child td{border-bottom:0}
        .status{display:inline-block;padding:5px 10px;border-radius:4px;background:#fbe9ed;color:var(--berry);font-size:9px;white-space:nowrap}
        .status-diproses,.status-diverifikasi,.status-dikirim,.status-selesai,.status-diterima{background:#e9f2eb;color:var(--forest)}
        .status-ditolak,.status-dibatalkan{background:#fbe9ed;color:var(--berry)}.status-menunggu{background:#fbe9ed;color:var(--berry)}
        .inline-form{display:flex;align-items:center;gap:6px;min-width:180px}.field{margin-bottom:13px}.field label{display:block;margin-bottom:6px;font-size:10px;font-weight:600;color:#383c35}
        .field input,.field select,.field textarea,.toolbar input,.toolbar select{width:100%;border:1px solid #ece9e1;border-radius:4px;padding:9px 10px;background:#fff;font-size:10px}
        .field textarea{min-height:82px;resize:vertical}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 16px}
        .toolbar{display:flex;align-items:center;gap:9px;margin-bottom:12px}.toolbar input{max-width:330px}.toolbar form{display:flex;gap:8px;align-items:center}
        .flash{padding:10px 13px;border-radius:6px;margin-bottom:12px;font-size:11px}.flash-success{background:#e9f2eb;color:var(--forest)}.flash-error{background:#fbe9ed;color:#a3223d}.errors{margin-bottom:12px;color:#a3223d}
        .product-thumb{width:38px;height:34px;object-fit:cover;border-radius:4px;background:#f4f0e9}.pagination{margin-top:12px;font-size:11px}
        .actions{display:flex;gap:6px;align-items:center}.muted{color:var(--muted)}.empty{text-align:center;padding:24px;color:var(--muted)}
        .report-cards{grid-template-columns:repeat(3,minmax(0,1fr))}.profile-panel{max-width:800px}
        .chart-layout{display:grid;grid-template-columns:minmax(0,2.1fr) minmax(170px,.9fr);gap:12px}
        .chart{height:164px;display:flex;align-items:flex-end;gap:14px;padding:12px 8px 0;border-bottom:1px solid var(--line)}
        .chart-item{height:100%;flex:1;display:flex;align-items:center;justify-content:flex-end;flex-direction:column;gap:6px;font-size:9px;color:var(--muted)}
        .chart-bar{width:min(34px,75%);min-height:3px;background:var(--berry);border-radius:5px 5px 0 0}
        .chart-area{position:relative;min-height:165px}.chart-area svg{width:100%;height:150px;overflow:visible}.chart-area line{stroke:#eee9de;stroke-width:1}.chart-area polyline{fill:none;stroke:var(--berry);stroke-width:2}.chart-area circle{fill:var(--berry)}
        .chart-labels{display:flex;justify-content:space-between;color:var(--muted);font-size:9px;padding:0 4px}
        .new-orders{display:grid;gap:9px}.new-orders a{display:flex;justify-content:space-between;align-items:center;padding:10px;border-radius:4px;background:#fff4f5;color:var(--berry);font-size:10px}
        .product-list,.order-list,.shipment-list{display:grid;gap:7px}.product-row,.order-row,.shipment-row{display:flex;align-items:center;gap:10px;padding:8px 10px;border:1px solid var(--line);border-radius:5px;background:#fff}
        .product-row .product-thumb{width:42px;height:38px}.product-info{min-width:0;flex:1}.product-info strong{display:block;font-size:10px}.product-info small{display:block;margin-top:4px;color:var(--muted);font-size:9px}
        .row-action{color:var(--berry);font-size:12px;padding:5px}.row-add{float:right;margin-top:10px}
        .inventory-table{overflow:hidden;border-radius:7px}.inventory-table table{min-width:580px}.inventory-table td{background:#fbfaf6}.inventory-table tr:nth-child(even) td{background:#fff}
        .order-row{min-height:45px}.order-row .order-summary{flex:1;font-size:10px}.order-summary strong{font-weight:600}.order-summary small{margin-left:8px;color:var(--muted)}.row-arrow{color:#777;font-size:16px}
        .detail-card{padding:20px 22px}.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px 30px}.detail-label{font-size:9px;color:var(--muted);margin-bottom:5px}.detail-value{font-size:10px}
        .detail-products{display:grid;gap:6px}.detail-product{display:flex;align-items:center;gap:8px;font-size:9px}.detail-product .product-thumb{width:28px;height:25px}
        .detail-total{color:var(--berry);font-size:16px;font-weight:700}.payment-card{min-height:205px}.payment-total{color:var(--berry);font-weight:700;font-size:16px}.payment-actions{display:flex;justify-content:center;gap:10px;margin-top:25px}
        .shipment-row{min-height:46px}.shipment-row .status{min-width:72px;text-align:center}.shipment-route{flex:1;font-size:10px}
        .report-card{padding:22px}.report-summary{color:var(--berry);font-size:19px;font-weight:700}.report-chart{display:flex;align-items:flex-end;justify-content:center;gap:12px;height:150px;padding:15px 10px 0}
        .report-bar-group{height:100%;display:flex;align-items:center;justify-content:flex-end;flex-direction:column;gap:5px;font-size:9px;color:var(--muted);min-width:22px}
        .report-bar{width:15px;min-height:3px;background:#568866}.report-bar.pink{background:var(--berry)}
        @media(max-width:900px){.admin-shell{grid-template-columns:200px minmax(0,1fr)}.content{padding:14px 18px 32px}.cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media(max-width:620px){.admin-shell{display:block}.sidebar{min-height:0;padding:10px;gap:8px}.brand{margin:0 0 8px}.nav-list{display:flex;overflow-x:auto}.nav-list a{white-space:nowrap;padding:8px}.side-bottom{display:none}.topbar{height:34px;padding:0 14px}.admin-user{margin-right:8px}.content{padding:10px 12px 25px}.cards,.report-cards{grid-template-columns:repeat(2,minmax(0,1fr))}.chart-layout{grid-template-columns:1fr}.form-grid{grid-template-columns:1fr}.detail-grid{grid-template-columns:1fr}.page-heading h2{font-size:17px}.panel{padding:12px}.detail-card{padding:15px}}
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/logo samudera.jpeg') }}" alt="Logo Samudera Fresh">
            <span>SAMUDERA FRESH<small>STRAWBERRY</small></span>
        </a>
        <nav class="nav-list" aria-label="Navigasi admin">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Produk</a>
            <a class="{{ request()->routeIs('admin.inventory') ? 'active' : '' }}" href="{{ route('admin.inventory') }}">Harga &amp; Stok</a>
            <a class="{{ request()->routeIs('admin.orders*', 'admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.orders') }}">Pesanan</a>
            <a class="{{ request()->routeIs('admin.shipping-rates*') ? 'active' : '' }}" href="{{ route('admin.shipping-rates') }}">Tarif Ongkir</a>
            <a class="{{ request()->routeIs('admin.returns*') ? 'active' : '' }}" href="{{ route('admin.returns') }}">Return Barang</a>
            <a class="{{ request()->routeIs('admin.shipments') ? 'active' : '' }}" href="{{ route('admin.shipments') }}">Pengiriman</a>
            <a class="{{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports') }}">Laporan</a>
            <a class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">Profil</a>
            <a class="{{ request()->routeIs('admin.staff') ? 'active' : '' }}" href="{{ route('admin.staff') }}">Petugas</a>
        </nav>
        <div class="side-bottom"><form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">Keluar dari Admin</button></form></div>
    </aside>
    <div class="admin-main">
        <header class="topbar">
            <h1>@yield('title', 'Dashboard Admin')</h1>
            <div class="admin-user">Admin: <strong>{{ session('nama_petugas', 'Administrator') }}</strong></div>
            <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="logout-icon" type="submit" aria-label="Keluar">×</button></form>
        </header>
        <main class="content">
            @if (session('success'))<div class="flash flash-success" role="status">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="flash flash-error" role="alert">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="flash flash-error errors" role="alert">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
