<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Samudera Fresh Strawberry</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #fffdf8;
            color: #222;
        }

        header {
            min-height: 115px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 5%;
            background: #fffdf8;
            border-bottom: 1px solid #eee;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .brand-text h3 {
            color: #245b3b;
            font-size: 17px;
        }

        .brand-text span {
            color: #e93658;
            font-size: 13px;
            font-weight: bold;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 35px;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-size: 14px;
            padding-bottom: 10px;
        }

        nav a.active {
            color: #e93658;
            font-weight: bold;
            border-bottom: 3px solid #e93658;
        }

        .cart {
            background: #ffe7ec;
            padding: 10px 14px;
            border-radius: 5px;
            font-size: 16px;
        }

        .container {
            width: 91%;
            max-width: 1400px;
            margin: 35px auto 0;
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .page-title {
            font-size: 38px;
        }

        .search-box {
            width: 100%;
            height: 48px;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            background: white;
            padding: 0 15px;
            font-size: 14px;
            outline: none;
        }

        .filter-form {
            display: grid;
            grid-template-columns: minmax(220px, 1.5fr) repeat(2, minmax(150px, 1fr)) auto;
            align-items: end;
            gap: 14px;
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 14px;
        }

        .filter-field {
            display: grid;
            gap: 8px;
            color: #245b3b;
            font-size: 14px;
            font-weight: bold;
        }

        .range-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .range-inputs input {
            width: 100%;
            min-width: 0;
            height: 42px;
            padding: 0 10px;
            border: 1px solid #e8e8e8;
            border-radius: 7px;
            font: inherit;
            font-weight: normal;
        }

        .filter-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-button,
        .reset-button {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 16px;
            border: 0;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
        }

        .filter-button {
            background: #e93658;
            color: #fff;
        }

        .filter-button:hover {
            background: #c92547;
        }

        .reset-button {
            background: #e5f2e9;
            color: #245b3b;
        }

        .product-weight,
        .result-count {
            color: #777;
            font-size: 14px;
        }

        .product-weight {
            margin: -10px 0 15px;
        }

        .result-count {
            margin: -10px 0 18px;
        }

        .categories {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 30px;
        }

        .category {
            min-width: 175px;
            padding: 13px 25px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 5px;
            background: #e5f2e9;
            color: #245b3b;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        .category.active {
            background: #fde3e8;
            color: #e93658;
        }

        .section-title {
            font-size: 25px;
            margin-top: 38px;
            margin-bottom: 22px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 28px;
        }

        .product-card {
            background: white;
            border: 1px solid #eee;
            border-radius: 24px;
            overflow: hidden;
            min-width: 0;
        }

        .product-image {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
        }

        .product-info {
            padding: 25px 24px 20px;
            min-height: 150px;
        }

        .product-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .product-price {
            color: #e93658;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 18px;
        }

        .product-actions {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .detail-link {
            display: inline-block;
            padding: 11px 14px;
            border: 1px solid #e93658;
            border-radius: 4px;
            color: #e93658;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-product {
            display: inline-block;
            border: none;
            background: #e93658;
            color: white;
            padding: 12px 22px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-product:hover {
            background: #c92547;
        }

        .package-card {
            background: #205936;
            color: white;
        }

        .package-card .product-name {
            font-size: 20px;
            margin-bottom: 22px;
        }

        .package-card .product-price {
            color: white;
            font-size: 17px;
        }

        .empty {
            background: white;
            border: 1px solid #eee;
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            color: #777;
        }

        .alert {
            padding: 12px 16px;
            margin-bottom: 18px;
            border-radius: 6px;
            background: #e5f2e9;
            color: #245b3b;
        }

        .alert-error {
            background: #fde3e8;
            color: #b42342;
        }

        .validation-errors {
            margin-top: 14px;
        }

        footer {
            width: 91%;
            margin: 105px auto 25px;
            font-size: 14px;
            color: #555;
        }

        @media (max-width: 1000px) {
            .product-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-form {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            nav {
                gap: 20px;
            }
        }

        @media (max-width: 650px) {
            header {
                flex-direction: column;
                gap: 20px;
            }

            nav {
                gap: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .container {
                margin-top: 25px;
            }

            .top-section {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-title {
                font-size: 30px;
            }

            .search-box {
                width: 100%;
            }

            .filter-form {
                grid-template-columns: 1fr;
                padding: 16px;
            }

            .filter-actions {
                flex-wrap: wrap;
            }

            .product-grid {
                grid-template-columns: 1fr;
            }

            footer {
                margin-top: 60px;
            }
        }
    </style>
</head>

<body>
    @include('partials.navigation', ['active' => 'produk'])

    <main class="container">
        <div class="top-section">
            <h1 class="page-title">Beranda Pelanggan</h1>
        </div>

        <div class="categories">
            @php
                $kategoriAktif = $filters['kategori'] ?? null;
                $queryKategori = request()->except('kategori');
            @endphp
            <a
                href="{{ route('produk.index', $queryKategori) }}"
                class="category {{ !$kategoriAktif ? 'active' : '' }}"
            >Semua Produk</a>
            <a
                href="{{ route('produk.index', array_merge($queryKategori, ['kategori' => 'segar'])) }}"
                class="category {{ $kategoriAktif === 'segar' ? 'active' : '' }}"
            >Strawberry Segar</a>
            <a
                href="{{ route('produk.index', array_merge($queryKategori, ['kategori' => 'keripik'])) }}"
                class="category {{ $kategoriAktif === 'keripik' ? 'active' : '' }}"
            >Keripik Strawberry</a>
            <a
                href="{{ route('produk.index', array_merge($queryKategori, ['kategori' => 'selai'])) }}"
                class="category {{ $kategoriAktif === 'selai' ? 'active' : '' }}"
            >Selai Strawberry</a>
        </div>

        <form method="GET" action="{{ route('produk.index') }}" class="filter-form">
            @if($kategoriAktif)
                <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
            @endif

            <label class="filter-field">
                Cari produk
                <input
                    type="search"
                    name="q"
                    class="search-box"
                    value="{{ $filters['q'] ?? '' }}"
                    placeholder="Cari nama atau deskripsi produk..."
                >
            </label>

            <div class="filter-field">
                <span>Rentang harga (Rp)</span>
                <div class="range-inputs">
                    <input
                        type="number"
                        name="harga_min"
                        min="0"
                        step="1000"
                        value="{{ $filters['harga_min'] ?? '' }}"
                        placeholder="Minimum"
                        aria-label="Harga minimum"
                    >
                    <input
                        type="number"
                        name="harga_max"
                        min="0"
                        step="1000"
                        value="{{ $filters['harga_max'] ?? '' }}"
                        placeholder="Maksimum"
                        aria-label="Harga maksimum"
                    >
                </div>
            </div>

            <div class="filter-field">
                <span>Rentang berat (gram)</span>
                <div class="range-inputs">
                    <input
                        type="number"
                        name="berat_min"
                        min="0"
                        step="1"
                        value="{{ $filters['berat_min'] ?? '' }}"
                        placeholder="Minimum"
                        aria-label="Berat minimum dalam gram"
                    >
                    <input
                        type="number"
                        name="berat_max"
                        min="0"
                        step="1"
                        value="{{ $filters['berat_max'] ?? '' }}"
                        placeholder="Maksimum"
                        aria-label="Berat maksimum dalam gram"
                    >
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="filter-button">Terapkan Filter</button>
                <a href="{{ route('produk.index') }}" class="reset-button">Reset</a>
            </div>
        </form>

        <h2 class="section-title">Produk Populer</h2>

        @if(session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error validation-errors" role="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <p class="result-count">{{ $produk->count() }} produk ditemukan</p>

        @if($produk->count() > 0)
            <div class="product-grid">
                @foreach($produk as $item)
                    @php
                        $nama = strtolower($item->nama_produk);

                        if ($item->foto_produk) {
                            $foto = $item->foto_produk;
                        } elseif (str_contains($nama, 'keripik')) {
                            $foto = 'keripik.jpg';
                        } elseif (str_contains($nama, 'selai')) {
                            $foto = 'selai.jpeg';
                        } else {
                            $foto = 'strawberry.jpeg';
                        }

                        $isPaket = str_contains($nama, 'paket');
                    @endphp

                    <div class="product-card {{ $isPaket ? 'package-card' : '' }}">
                        <img
                            src="{{ asset('images/' . $foto) }}"
                            class="product-image"
                            alt="{{ $item->nama_produk }}"
                        >

                        <div class="product-info">
                            <h3 class="product-name">{{ $item->nama_produk }}</h3>

                            <div class="product-price">
                                Rp{{ number_format($item->harga_produk, 0, ',', '.') }}
                            </div>

                            @if($item->berat_gram)
                                <p class="product-weight">{{ number_format($item->berat_gram, 0, ',', '.') }} gram</p>
                            @endif

                            <div class="product-actions">
                                <a href="{{ route('produk.show', $item->id_produk) }}" class="detail-link">
                                    Detail
                                </a>
                                <form
                                    action="{{ route('keranjang.tambah', $item->id_produk) }}"
                                    method="POST"
                                >
                                    @csrf
                                    <input type="hidden" name="jumlah" value="1">
                                    <button type="submit" class="btn-product">Tambah</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty">
                Belum ada produk yang tersedia.
            </div>
        @endif
    </main>

    <footer>
        © Ciwidey • Produk olahan stroberi
    </footer>
</body>
</html>