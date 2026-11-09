<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - Samudera Fresh Strawberry</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #fffdf8;
            color: #292929;
            font-family: Arial, Helvetica, sans-serif;
        }
        a { color: inherit; text-decoration: none; }
        .navbar {
            min-height: 76px;
            padding: 12px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border-bottom: 1px solid #eee;
            background: #fffdf8;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #245b3b;
            font-size: 13px;
            font-weight: bold;
        }
        .brand img { width: 46px; height: 46px; object-fit: contain; }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 27px;
            color: #333;
            font-size: 14px;
        }
        .nav-links .active { color: #e93658; font-weight: bold; }
        .cart-link {
            padding: 9px 12px;
            border-radius: 7px;
            background: #ffe7ec;
            font-size: 18px;
        }
        main { width: 91%; max-width: 1050px; margin: 34px auto 60px; }
        h1 { margin: 0 0 20px; text-align: center; font-size: 24px; }
        .alert {
            margin-bottom: 16px;
            padding: 12px 15px;
            border-radius: 8px;
            background: #e5f2e9;
            color: #245b3b;
            font-size: 14px;
        }
        .alert.error { background: #fde3e8; color: #b42342; }
        .detail-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 18px;
        }
        .image-panel, .info-panel {
            overflow: hidden;
            border: 1px solid #f0e9e8;
            border-radius: 26px;
            background: #fff;
        }
        .image-panel { padding: 14px; }
        .product-image {
            display: block;
            width: 100%;
            height: 360px;
            border-radius: 17px;
            background: #fff6f4;
            object-fit: cover;
        }
        .origin {
            display: inline-block;
            margin: 13px 0 2px 7px;
            padding: 7px 13px;
            border-radius: 20px;
            background: #e5f2e9;
            color: #245b3b;
            font-size: 13px;
            font-weight: bold;
        }
        .info-panel { padding: 28px; }
        .product-name { margin: 0 0 11px; color: #245b3b; font-size: 25px; }
        .rating {
            margin-bottom: 17px;
            color: #e9a93a;
            font-size: 14px;
            letter-spacing: 2px;
        }
        .price-stock {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 23px;
        }
        .price { color: #e93658; font-size: 20px; font-weight: bold; }
        .stock { color: #777; font-size: 14px; }
        .description-title, .quantity-title { font-size: 15px; font-weight: bold; }
        .description-title { margin-bottom: 7px; }
        .description {
            min-height: 65px;
            margin: 0 0 22px;
            color: #555;
            font-size: 15px;
            line-height: 1.7;
        }
        .weight { margin: -12px 0 17px; color: #777; font-size: 14px; }
        .quantity-title { margin-bottom: 9px; }
        .purchase-row {
            display: grid;
            grid-template-columns: 118px minmax(0, 1fr);
            gap: 12px;
        }
        .quantity-control {
            min-height: 43px;
            display: grid;
            grid-template-columns: 36px 1fr 36px;
            align-items: center;
            border-radius: 7px;
            background: #fde9ed;
        }
        .quantity-control button {
            height: 100%;
            border: 0;
            background: transparent;
            color: #e93658;
            font-size: 18px;
            cursor: pointer;
        }
        .quantity-control button:disabled { color: #c7aeb2; cursor: not-allowed; }
        .quantity-control input {
            width: 100%;
            min-width: 0;
            border: 0;
            outline: 0;
            background: transparent;
            color: #333;
            font: inherit;
            font-size: 13px;
            text-align: center;
            appearance: textfield;
        }
        .quantity-control input::-webkit-inner-spin-button,
        .quantity-control input::-webkit-outer-spin-button {
            margin: 0;
            appearance: none;
        }
        .add-button {
            min-height: 43px;
            border: 0;
            border-radius: 7px;
            background: #e93658;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        .add-button:hover { background: #c92547; }
        .add-button:disabled { background: #b9b9b9; cursor: not-allowed; }
        .back-link {
            display: inline-block;
            margin-top: 22px;
            color: #245b3b;
            font-size: 13px;
            font-weight: bold;
        }
        @media (max-width: 700px) {
            .navbar { align-items: flex-start; flex-direction: column; }
            .nav-links { width: 100%; justify-content: space-between; gap: 12px; }
            .detail-layout { grid-template-columns: 1fr; }
            .product-image { height: 280px; }
            .info-panel { padding: 22px; }
            .purchase-row { grid-template-columns: 110px minmax(0, 1fr); gap: 8px; }
        }
    </style>
</head>
<body>
    @php
        $namaProduk = strtolower($produk->nama_produk);

        if ($produk->foto_produk) {
            $foto = $produk->foto_produk;
        } elseif (str_contains($namaProduk, 'keripik')) {
            $foto = 'keripik.jpg';
        } elseif (str_contains($namaProduk, 'selai')) {
            $foto = 'selai.jpeg';
        } else {
            $foto = $produk->foto_produk ?: 'strawberry.jpeg';
        }
    @endphp

    @include('partials.navigation', ['active' => 'produk'])

    <main>
        <h1>Detail Produk – {{ $produk->nama_produk }}</h1>

        @if(session('success'))
            <div class="alert" role="status">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert error" role="alert">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert error" role="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="detail-layout">
            <section class="image-panel" aria-label="Foto produk">
                <img
                    src="{{ asset('images/' . $foto) }}"
                    class="product-image"
                    alt="{{ $produk->nama_produk }}"
                >
                <span class="origin">PRODUK CIWIDEY</span>
            </section>

            <section class="info-panel">
                <h2 class="product-name">{{ $produk->nama_produk }}</h2>
                <div class="rating" aria-label="Rating produk">★★★★★</div>

                <div class="price-stock">
                    <span class="price">Rp{{ number_format($produk->harga_produk, 0, ',', '.') }}</span>
                    <span class="stock">Stok: {{ $produk->stok_produk }}</span>
                </div>

                <div class="description-title">Deskripsi</div>
                <p class="description">
                    {{ $produk->deskripsi ?: 'Produk olahan strawberry pilihan dari Ciwidey.' }}
                </p>

                @if($produk->berat_gram)
                    <p class="weight">Berat: {{ number_format($produk->berat_gram, 0, ',', '.') }} gram</p>
                @endif

                <form
                    action="{{ route('keranjang.tambah', $produk->id_produk) }}"
                    method="POST"
                    id="add-to-cart-form"
                >
                    @csrf
                    <div class="quantity-title">Jumlah</div>
                    <div class="purchase-row">
                        <div class="quantity-control">
                            <button
                                type="button"
                                id="quantity-minus"
                                aria-label="Kurangi jumlah"
                                {{ $stokTersedia < 1 ? 'disabled' : '' }}
                            >−</button>
                            <input
                                type="number"
                                id="quantity"
                                name="jumlah"
                                value="1"
                                min="1"
                                max="{{ max(1, $stokTersedia) }}"
                                inputmode="numeric"
                                aria-label="Jumlah produk"
                                required
                            >
                            <button
                                type="button"
                                id="quantity-plus"
                                aria-label="Tambah jumlah"
                                {{ $stokTersedia < 1 ? 'disabled' : '' }}
                            >+</button>
                        </div>

                        <button
                            type="submit"
                            class="add-button"
                            {{ $stokTersedia < 1 ? 'disabled' : '' }}
                        >Tambah ke Keranjang</button>
                    </div>
                </form>
            </section>
        </div>

        <a href="{{ route('produk.index') }}" class="back-link">← Kembali ke Produk</a>
    </main>

    <script>
        const quantityInput = document.getElementById('quantity');
        const minusButton = document.getElementById('quantity-minus');
        const plusButton = document.getElementById('quantity-plus');

        function syncQuantityButtons() {
            const quantity = Number(quantityInput.value || quantityInput.min);
            const minimum = Number(quantityInput.min);
            const maximum = Number(quantityInput.max);

            minusButton.disabled = quantity <= minimum;
            plusButton.disabled = quantity >= maximum;
        }

        minusButton.addEventListener('click', function () {
            quantityInput.value = Math.max(
                Number(quantityInput.min),
                Number(quantityInput.value || quantityInput.min) - 1
            );
            syncQuantityButtons();
        });

        plusButton.addEventListener('click', function () {
            quantityInput.value = Math.min(
                Number(quantityInput.max),
                Number(quantityInput.value || quantityInput.min) + 1
            );
            syncQuantityButtons();
        });

        quantityInput.addEventListener('input', syncQuantityButtons);
        syncQuantityButtons();
    </script>
</body>
</html>