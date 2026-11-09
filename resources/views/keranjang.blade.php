<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | Samudera Fresh Strawberry</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fffdf8;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .navbar {
            min-height: 88px;
            padding: 15px 6%;
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
            gap: 10px;
            color: #a91f32;
            font-weight: bold;
            font-size: 17px;
            line-height: 1.35;
        }

        .brand img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 27px;
            font-size: 14px;
        }

        .nav-links a:hover,
        .nav-links .active {
            color: #c6283d;
        }

        .cart-icon {
            color: #c6283d;
            font-size: 24px;
        }

        main {
            max-width: 1180px;
            width: 90%;
            margin: 45px auto 60px;
        }

        h1 {
            font-size: 29px;
            margin-bottom: 30px;
            color: #292929;
        }

        .alert {
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
            background: #edf7ed;
            color: #27632a;
        }

        .alert.error {
            background: #fff0f0;
            color: #a91f32;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(250px, 0.9fr);
            gap: 30px;
            align-items: start;
        }

        .product-row {
            display: grid;
            grid-template-columns: 105px minmax(0, 1fr);
            gap: 20px;
            padding: 22px 0;
            border-bottom: 1px solid #eee;
        }

        .product-image {
            width: 105px;
            height: 105px;
            border-radius: 10px;
            object-fit: cover;
            background: #fff5f5;
        }

        .product-name {
            font-size: 17px;
            margin: 5px 0 10px;
        }

        .product-price {
            color: #c6283d;
            font-weight: bold;
            margin-bottom: 17px;
        }

        .product-controls {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .quantity {
            display: flex;
            align-items: center;
            border: 1px solid #e5e5e5;
            border-radius: 6px;
            overflow: hidden;
        }

        .quantity button {
            width: 35px;
            height: 34px;
            border: 0;
            background: #fff5f5;
            color: #a91f32;
            font-size: 20px;
            cursor: pointer;
        }

        .quantity button:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .quantity span {
            min-width: 35px;
            text-align: center;
            font-size: 14px;
        }

        .delete-button {
            border: 0;
            background: transparent;
            color: #c6283d;
            font-size: 20px;
            cursor: pointer;
        }

        .summary {
            border: 1px solid #204b35;
            border-radius: 24px;
            padding: 27px 24px;
            background: #204b35;
            color: #fff;
        }

        .summary h2 {
            font-size: 21px;
            margin-bottom: 24px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
            font-size: 15px;
        }

        .summary-total {
            border-top: 1px solid #ffffff40;
            padding-top: 20px;
            font-size: 19px;
            font-weight: bold;
        }

        .checkout-button {
            display: block;
            width: 100%;
            padding: 14px 10px;
            margin-top: 25px;
            border: 0;
            border-radius: 7px;
            background: #e93658;
            color: #fff;
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .checkout-button:hover {
            background: #c92547;
        }

        .empty-cart {
            padding: 50px 20px;
            text-align: center;
            border: 1px solid #eee;
            border-radius: 12px;
        }

        .cart-items {
            padding: 5px 28px;
            border: 1px solid #eee;
            border-radius: 28px;
            background: #fff;
        }

        .empty-cart p {
            margin: 12px 0 20px;
            color: #777;
        }

        footer {
            padding: 23px 6%;
            border-top: 1px solid #eee;
            color: #777;
            font-size: 13px;
        }

        @media (max-width: 760px) {
            .navbar {
                flex-wrap: wrap;
                padding: 14px 5%;
            }

            .nav-links {
                width: 100%;
                justify-content: space-between;
                gap: 10px;
                font-size: 13px;
            }

            main {
                width: 92%;
                margin-top: 30px;
            }

            .cart-layout {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .product-row {
                grid-template-columns: 80px minmax(0, 1fr);
                gap: 13px;
            }

            .product-image {
                width: 80px;
                height: 80px;
            }

            .product-name {
                font-size: 15px;
            }

            .cart-items {
                padding: 5px 18px;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    @include('partials.navigation', ['active' => 'keranjang'])

    <main>
        <h1>Keranjang Belanja</h1>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        @if (count($keranjang) > 0)
            <div class="cart-layout">
                <section class="cart-items">
                    @foreach ($keranjang as $id => $item)
                        <article class="product-row">
                            <img
                                class="product-image"
                                src="{{ asset('images/' . $item['foto_produk']) }}"
                                alt="{{ $item['nama_produk'] }}"
                                onerror="this.style.display='none'"
                            >

                            <div>
                                <h2 class="product-name">
                                    {{ $item['nama_produk'] }}
                                </h2>

                                <p class="product-price">
                                    Rp{{ number_format($item['harga_produk'], 0, ',', '.') }}
                                </p>

                                <div class="product-controls">
                                    <form
                                        action="{{ route('keranjang.update', $id) }}"
                                        method="POST"
                                        class="quantity"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <button
                                            type="submit"
                                            name="jumlah"
                                            value="{{ $item['jumlah'] - 1 }}"
                                            {{ $item['jumlah'] <= 1 ? 'disabled' : '' }}
                                            aria-label="Kurangi jumlah"
                                        >−</button>

                                        <span>{{ $item['jumlah'] }}</span>

                                        <button
                                            type="submit"
                                            name="jumlah"
                                            value="{{ $item['jumlah'] + 1 }}"
                                            aria-label="Tambah jumlah"
                                        >+</button>
                                    </form>

                                    <form
                                        action="{{ route('keranjang.hapus', $id) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="delete-button"
                                            aria-label="Hapus {{ $item['nama_produk'] }}"
                                            title="Hapus produk"
                                        >🗑</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </section>

                <aside class="summary">
                    <h2>Ringkasan</h2>

                    <div class="summary-line">
                        <span>Total ({{ count($keranjang) }} produk)</span>
                    </div>

                    <div class="summary-line summary-total">
                        <span>Total harga</span>
                        <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <a href="{{ url('/checkout') }}" class="checkout-button">
                        Checkout
                    </a>
                </aside>
            </div>
        @else
            <div class="empty-cart">
                <h2>Keranjang Belanja Kosong</h2>
                <p>Pilih produk strawberry favoritmu untuk mulai berbelanja.</p>

                <a href="{{ route('produk.index') }}" class="checkout-button">
                    Lihat Produk
                </a>
            </div>
        @endif
    </main>

    <footer>
        © Ciwidey • Produk olahan stroberi
    </footer>
</body>
</html>