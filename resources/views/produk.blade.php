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
            background: #fff9f5;
            color: #292524;
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
            width: min(1200px, 92%);
            margin: 34px auto 0;
        }

        .top-section {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 36px;
            overflow: hidden;
            margin-bottom: 22px;
            padding: 44px 48px;
            border: 1px solid #f2e2d9;
            border-radius: 30px;
            background:
                radial-gradient(circle at 82% 10%, rgba(255, 255, 255, .76), transparent 25%),
                linear-gradient(120deg, #fff0e7 0%, #fff8f2 54%, #edf6ec 100%);
            box-shadow: 0 16px 40px rgba(104, 57, 48, .055);
        }

        .top-section > * {
            position: relative;
            z-index: 1;
        }

        .page-title {
            max-width: 650px;
            margin: 10px 0 12px;
            color: #245b3b;
            font-size: clamp(32px, 4.2vw, 48px);
            line-height: 1.08;
            letter-spacing: -1.5px;
        }

        .hero-kicker {
            color: #bb4858;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1.8px;
            text-transform: uppercase;
        }

        .hero-copy {
            max-width: 620px;
            color: #665c55;
            font-size: 16px;
            line-height: 1.75;
        }

        .hero-note {
            flex: 0 0 250px;
            padding: 24px;
            border: 1px solid rgba(36, 91, 59, .13);
            border-radius: 22px;
            background: rgba(255, 255, 255, .8);
            color: #245b3b;
            box-shadow: 0 12px 30px rgba(36, 91, 59, .07);
        }

        .hero-note strong {
            display: block;
            margin-bottom: 7px;
            font-size: 19px;
            letter-spacing: -.3px;
            white-space: nowrap;
        }

        .hero-note span {
            color: #687168;
            font-size: 13px;
            line-height: 1.6;
        }

        .search-box {
            width: 100%;
            height: 44px;
            border: 1px solid #e9e3dd;
            border-radius: 9px;
            background: white;
            padding: 0 15px;
            font-size: 14px;
            outline: none;
        }

        .search-box:focus,
        .range-inputs input:focus {
            border-color: #df7181;
            box-shadow: 0 0 0 3px rgba(233, 54, 88, .1);
        }

        .filter-form {
            display: grid;
            grid-template-columns: minmax(220px, 1.5fr) repeat(2, minmax(150px, 1fr)) auto;
            align-items: end;
            gap: 14px;
            margin-top: 20px;
            padding: 20px;
            background: #fff;
            border: 1px solid #f0e8e2;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(79, 45, 37, .055);
        }

        .filter-field {
            display: grid;
            gap: 8px;
            color: #3b604b;
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
            border: 1px solid #e9e3dd;
            border-radius: 8px;
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
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
        }

        .filter-button {
            background: #e93658;
            color: #fff;
            box-shadow: 0 5px 12px rgba(233, 54, 88, .18);
        }

        .filter-button:hover {
            background: #c92547;
        }

        .reset-button {
            background: #eaf3e9;
            color: #245b3b;
        }

        .filter-button:focus-visible,
        .reset-button:focus-visible,
        .category:focus-visible,
        .detail-link:focus-visible,
        .btn-product:focus-visible {
            outline: 3px solid rgba(36, 91, 59, .35);
            outline-offset: 3px;
        }

        .product-weight,
        .result-count {
            color: #77716c;
            font-size: 13px;
        }

        .product-weight {
            margin: 9px 0 15px;
        }

        .result-count {
            margin: 0;
            padding: 9px 13px;
            border: 1px solid #e8eee5;
            border-radius: 999px;
            background: #fff;
            color: #42644e;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .categories {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 24px 0 18px;
        }

        .category {
            min-height: 42px;
            padding: 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e7ece4;
            border-radius: 999px;
            background: #fff;
            color: #245b3b;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background .2s ease, color .2s ease, transform .2s ease, border-color .2s ease;
        }

        .category:hover {
            border-color: #b7cfbd;
            transform: translateY(-1px);
        }

        .category.active {
            border-color: #e93658;
            background: #e93658;
            color: #fff;
        }

        .catalog-results-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            margin: 34px 0 18px;
        }

        .section-title {
            margin: 0 0 6px;
            color: #245b3b;
            font-size: 26px;
            letter-spacing: -.5px;
        }

        .section-subtitle {
            margin: 0;
            color: #77716c;
            font-size: 14px;
            line-height: 1.5;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .product-card {
            display: flex;
            flex-direction: column;
            background: white;
            border: 1px solid #efe8e2;
            border-radius: 22px;
            overflow: hidden;
            min-width: 0;
            box-shadow: 0 8px 24px rgba(79, 45, 37, .06);
            transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            border-color: #e7d8ce;
            box-shadow: 0 18px 34px rgba(79, 45, 37, .12);
        }

        .product-image-wrap {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1.38 / 1;
            background: #fff4ed;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .35s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.035);
        }

        .grade-chip {
            position: absolute;
            top: 14px;
            left: 14px;
            padding: 7px 11px;
            border: 1px solid rgba(255, 255, 255, .75);
            border-radius: 999px;
            background: rgba(255, 255, 255, .92);
            color: #245b3b;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: .5px;
        }

        .product-info {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 21px;
        }

        .product-name {
            margin-bottom: 8px;
            color: #245b3b;
            font-size: 18px;
            line-height: 1.35;
        }

        .product-description {
            min-height: 38px;
            margin: 0 0 12px;
            color: #77716c;
            font-size: 13px;
            line-height: 1.5;
        }

        .product-price {
            color: #e93658;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 0;
        }

        .product-price span {
            color: #77716c;
            font-size: 13px;
            font-weight: normal;
        }

        .product-actions {
            margin-top: auto;
            padding-top: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .detail-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 13px;
            border: 1px solid #e93658;
            border-radius: 9px;
            color: #e93658;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            white-space: nowrap;
        }

        .product-actions form {
            flex: 1;
        }

        .btn-product {
            width: 100%;
            min-height: 42px;
            border: none;
            background: #e93658;
            color: white;
            padding: 0 14px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-product:hover {
            background: #c92547;
        }

        .btn-product:disabled {
            background: #aaa;
            cursor: not-allowed;
        }

        .availability-badge {
            align-self: flex-start;
            display: inline-flex;
            margin: 10px 0 4px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
        }

        .availability-badge.available { background: #eaf3e9; color: #245b3b; }
        .availability-badge.sold { background: #fde8eb; color: #b42342; }

        .empty {
            background: white;
            border: 1px solid #efe8e2;
            border-radius: 18px;
            padding: 54px 24px;
            text-align: center;
            color: #777;
            box-shadow: 0 8px 24px rgba(79, 45, 37, .04);
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
            width: min(1200px, 92%);
            margin: 75px auto 25px;
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

            .top-section { padding: 34px; }
            .hero-note { flex-basis: 240px; }
        }

        @media (max-width: 800px) {
            .top-section {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 22px;
            }

            .hero-note {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
                padding: 18px 22px;
            }

            .hero-note strong { margin: 0; }
            .hero-note span { max-width: 280px; }
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
                gap: 20px;
                padding: 26px 22px;
            }

            .page-title {
                font-size: 30px;
            }

            .hero-note {
                width: 100%;
                flex-basis: auto;
                padding: 16px;
            }

            .hero-note { align-items: flex-start; flex-direction: column; gap: 5px; }
            .hero-note span { max-width: none; }

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

            .product-image-wrap { aspect-ratio: 1.25 / 1; }
            .categories { gap: 7px; }
            .category { padding: 9px 12px; font-size: 13px; }
            .catalog-results-header { align-items: flex-start; flex-direction: column; }

            footer {
                margin-top: 60px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>
    @include('partials.navigation', ['active' => 'produk'])

    <main class="container">
        <section class="top-section" aria-labelledby="catalog-title">
            <div>
                <span class="hero-kicker">Segar pilihan dari Ciwidey</span>
                <h1 class="page-title" id="catalog-title">Stroberi pilihan, untuk setiap momen.</h1>
                <p class="hero-copy">
                    Pilih stroberi segar sesuai grade yang kamu suka, atau temukan selai dan keripik
                    stroberi untuk menemani harimu.
                </p>
            </div>
            <div class="hero-note">
                <strong>Grade A, B &amp; C</strong>
                <span>Tiga pilihan kualitas dan harga. Stroberi segar dijual per kilogram.</span>
            </div>
        </section>

        @include('partials.operational-hours')

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
            >Stroberi Segar</a>
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

        <div class="catalog-results-header">
            <div>
                <h2 class="section-title">Pilih Produk Favoritmu</h2>
                <p class="section-subtitle">Harga dan stok stroberi segar ditampilkan untuk setiap grade.</p>
            </div>
            <p class="result-count">{{ $produk->count() }} produk ditemukan</p>
        </div>

        @if($produk->count() > 0)
            <div class="product-grid">
                @foreach($produk as $item)
                    @php
                        $nama = strtolower($item->nama_produk);
                        $isFresh = (bool) ($item->catalog_grade ?? false);
                        if ($isFresh) {
                            $foto = $item->catalog_image;
                        } elseif ($item->foto_produk) {
                            $foto = $item->foto_produk;
                        } elseif (str_contains($nama, 'keripik')) {
                            $foto = 'keripik.jpg';
                        } elseif (str_contains($nama, 'selai')) {
                            $foto = 'selai.jpeg';
                        } else {
                            $foto = 'strawberry.jpeg';
                        }

                        $isAvailable = (bool) $item->catalog_available;
                        $detailUrl = $isFresh
                            ? route('produk.show', ['id' => $item->id_produk, 'grade' => $item->catalog_grade])
                            : route('produk.show', $item->id_produk);
                    @endphp

                    <article class="product-card">
                        <div class="product-image-wrap">
                            <img
                                src="{{ asset('images/' . $foto) }}"
                                class="product-image"
                                alt="{{ $item->catalog_name }}"
                            >
                            @if ($isFresh)
                                <span class="grade-chip">GRADE {{ $item->catalog_grade }}</span>
                            @endif
                        </div>

                        <div class="product-info">
                            <h3 class="product-name">{{ $item->catalog_name }}</h3>

                            @if ($isFresh)
                                <p class="product-description">Stroberi segar pilihan · Grade {{ $item->catalog_grade }}</p>
                                <div class="product-price">
                                    Rp{{ number_format($item->catalog_price, 0, ',', '.') }}<span>/kg</span>
                                </div>
                                <p class="product-weight">
                                    Stok: {{ \App\Support\ProductCatalog::formatKilogramStock($item->catalog_stock) }} kg
                                </p>
                            @else
                                <div class="product-price">
                                    Rp{{ number_format($item->catalog_price, 0, ',', '.') }}<span>/{{ $item->berat_gram ? 'kemasan' : 'pcs' }}</span>
                                </div>

                                @if($item->berat_gram)
                                    <p class="product-weight">{{ number_format($item->berat_gram, 0, ',', '.') }} gram per kemasan</p>
                                @endif
                            @endif

                            <span class="availability-badge {{ $isAvailable ? 'available' : 'sold' }}">
                                {{ $isAvailable ? 'Tersedia' : 'Habis' }}
                            </span>

                            <div class="product-actions">
                                <a
                                    href="{{ $detailUrl }}"
                                    class="detail-link"
                                >Detail</a>
                                <form
                                    action="{{ route('keranjang.tambah', $item->id_produk) }}"
                                    method="POST"
                                >
                                    @csrf
                                    <input type="hidden" name="jumlah" value="1">
                                    @if ($isFresh)
                                        <input type="hidden" name="grade" value="{{ $item->catalog_grade }}">
                                    @endif
                                    <button type="submit" class="btn-product" {{ $isAvailable ? '' : 'disabled' }}>
                                        {{ $isFresh ? 'Tambah Grade '.$item->catalog_grade : 'Tambah' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
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