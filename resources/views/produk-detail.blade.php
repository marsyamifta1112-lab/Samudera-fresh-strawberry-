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
            background: #fff9f5;
            color: #292524;
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
        main { width: min(1120px, 92%); margin: 34px auto 64px; }
        .detail-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 12px;
        }
        .detail-page-title {
            margin: 0 0 22px;
            color: #245b3b;
            font-size: clamp(24px, 3vw, 32px);
            letter-spacing: -.7px;
        }
        .detail-eyebrow {
            color: #bb4858;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }
        .alert {
            margin-bottom: 16px;
            padding: 12px 15px;
            border-radius: 12px;
            background: #e5f2e9;
            color: #245b3b;
            font-size: 14px;
        }
        .alert.error { background: #fde3e8; color: #b42342; }
        .detail-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(0, .92fr);
            align-items: stretch;
            gap: 24px;
        }
        .image-panel, .info-panel {
            overflow: hidden;
            border: 1px solid #efe6df;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 12px 32px rgba(79, 45, 37, .07);
        }
        .image-panel {
            position: relative;
            display: flex;
            align-items: center;
            padding: 14px;
            background: linear-gradient(145deg, #fff2eb, #fffaf6);
        }
        .product-image {
            display: block;
            width: 100%;
            height: clamp(340px, 43vw, 500px);
            border-radius: 17px;
            background: #fff6f4;
            object-fit: cover;
        }
        .product-image.grade-c-image { object-fit: contain; }
        .origin {
            position: absolute;
            left: 28px;
            bottom: 28px;
            display: inline-block;
            padding: 8px 13px;
            border: 1px solid rgba(36, 91, 59, .12);
            border-radius: 999px;
            background: rgba(255, 255, 255, .92);
            color: #245b3b;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: .5px;
        }
        .info-panel { padding: clamp(24px, 3vw, 38px); }
        .product-name { margin: 0 0 21px; color: #245b3b; font-size: clamp(23px, 2.5vw, 30px); line-height: 1.2; }
        .price-stock {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
            padding-bottom: 18px;
            border-bottom: 1px solid #f0e9e3;
        }
        .price { color: #c92547; font-size: 24px; font-weight: bold; letter-spacing: -.4px; }
        .stock { color: #56665a; font-size: 13px; font-weight: 600; }
        .availability-badge {
            display: inline-block;
            margin: 0 0 20px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
        }
        .availability-badge.available { background: #e7f3e8; color: #245b3b; }
        .availability-badge.sold { background: #fde8eb; color: #b42342; }
        .grade-select {
            width: 100%;
            min-height: 48px;
            margin: 8px 0 19px;
            padding: 10px 13px;
            border: 1px solid #e6ddd6;
            border-radius: 11px;
            background: #fff;
            font: inherit;
        }
        .grade-select:focus {
            border-color: #d78a94;
            outline: 3px solid rgba(233, 54, 88, .1);
        }
        .description-title, .quantity-title { font-size: 15px; font-weight: bold; }
        .description-title { margin-bottom: 9px; color: #245b3b; }
        .description {
            min-height: 78px;
            margin: 0 0 22px;
            padding: 15px 16px;
            border-left: 3px solid #e8a4ab;
            border-radius: 0 12px 12px 0;
            background: #fff8f4;
            color: #625a54;
            font-size: 14px;
            line-height: 1.75;
        }
        .weight { margin: -10px 0 18px; color: #777; font-size: 13px; }
        .quantity-title { margin-bottom: 9px; }
        .purchase-row {
            display: grid;
            grid-template-columns: 128px minmax(0, 1fr);
            gap: 10px;
        }
        .quantity-control {
            min-height: 43px;
            display: grid;
            grid-template-columns: 36px 1fr 36px;
            align-items: center;
            border: 1px solid #f5dce1;
            border-radius: 10px;
            background: #fff3f5;
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
            border-radius: 10px;
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
            padding: 8px 12px;
            border-radius: 999px;
            background: #f0f5ef;
            color: #245b3b;
            font-size: 12px;
            font-weight: bold;
            transition: background .2s ease;
        }
        .back-link:hover { background: #e5efe4; }
        @media (max-width: 760px) {
            main { margin-top: 26px; }
            .detail-layout { grid-template-columns: 1fr; gap: 16px; }
            .product-image { height: min(78vw, 420px); min-height: 290px; }
            .image-panel { padding: 10px; }
            .origin { left: 22px; bottom: 22px; }
            .info-panel { padding: 24px; }
        }
        @media (max-width: 480px) {
            .detail-heading { align-items: flex-start; flex-direction: column; gap: 8px; }
            .detail-page-title { margin-bottom: 18px; }
            .detail-layout { grid-template-columns: 1fr; }
            .price-stock { align-items: flex-start; flex-direction: column; gap: 7px; }
            .purchase-row { grid-template-columns: 112px minmax(0, 1fr); gap: 8px; }
        }
    </style>
</head>
<body>
    @php
        $namaProduk = strtolower($produk->nama_produk);
        $requestedGrade = old('grade', request()->query('grade'));
        $initialGrade = $isStrawberryFresh
            ? ($grades->firstWhere('grade', $requestedGrade) ?? $grades->first())
            : null;

        if ($initialGrade) {
            $foto = \App\Support\ProductCatalog::gradeImage($initialGrade->grade);
        } elseif ($produk->foto_produk) {
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
        <div class="detail-heading">
            <a href="{{ route('produk.index') }}" class="back-link">← Kembali ke produk</a>
            <span class="detail-eyebrow">{{ $isStrawberryFresh ? 'Stroberi segar dari Ciwidey' : 'Produk pilihan dari Ciwidey' }}</span>
        </div>
        <h1 class="detail-page-title">Detail produk</h1>

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
                    class="product-image {{ ($initialGrade->grade ?? null) === 'C' ? 'grade-c-image' : '' }}"
                    alt="{{ $produk->nama_produk }}{{ $initialGrade ? ' · Grade '.$initialGrade->grade : '' }}"
                >
                <span class="origin">PRODUK CIWIDEY</span>
            </section>

            <section class="info-panel">
                <h2 class="product-name">
                    {{ $produk->nama_produk }}
                    @if ($initialGrade)
                        · Grade {{ $initialGrade->grade }}
                    @endif
                </h2>

                @if ($isStrawberryFresh)
                    <div class="price-stock">
                        <span class="price" id="grade-price">Rp{{ number_format($initialGrade->harga_per_kg ?? 0, 0, ',', '.') }}/kg</span>
                        <span class="stock" id="grade-stock">Stok: {{ \App\Support\ProductCatalog::formatKilogramStock($initialGrade->stok_kg ?? 0) }} kg</span>
                    </div>
                @else
                    <div class="price-stock">
                        <span class="price">Rp{{ number_format($produk->harga_produk, 0, ',', '.') }}/{{ $produk->berat_gram ? 'kemasan' : 'pcs' }}</span>
                        <span class="stock">Stok: {{ $produk->stok_produk }}</span>
                    </div>
                @endif
                <span class="availability-badge {{ $dapatDipesan ? 'available' : 'sold' }}">
                    {{ $dapatDipesan ? 'Tersedia' : 'Habis' }}
                </span>

                <div class="description-title">Deskripsi</div>
                <p class="description" id="{{ $isStrawberryFresh ? 'grade-description' : 'product-description' }}">
                    {{ $initialGrade
                        ? \App\Support\ProductCatalog::gradeDescription($initialGrade->grade)
                        : ($produk->deskripsi ?: 'Produk olahan strawberry pilihan dari Ciwidey.') }}
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
                    @if ($isStrawberryFresh)
                        @if ($grades->isNotEmpty())
                            <label class="quantity-title" for="grade-select">Pilih Grade</label>
                            <select class="grade-select" id="grade-select" name="grade" required>
                                @foreach ($grades as $grade)
                                    <option
                                        value="{{ $grade->grade }}"
                                        data-price="{{ $grade->harga_per_kg }}"
                                        data-stock="{{ $grade->stok_kg }}"
                                        data-image="{{ \App\Support\ProductCatalog::gradeImage($grade->grade) }}"
                                        data-description="{{ \App\Support\ProductCatalog::gradeDescription($grade->grade) }}"
                                        @selected(old('grade', $initialGrade->grade ?? null) === $grade->grade)
                                    >Grade {{ $grade->grade }} · Rp{{ number_format($grade->harga_per_kg, 0, ',', '.') }}/kg · Stok {{ \App\Support\ProductCatalog::formatKilogramStock($grade->stok_kg) }} kg</option>
                                @endforeach
                            </select>
                        @else
                            <p class="alert error">Grade stroberi belum disiapkan Admin.</p>
                        @endif
                    @endif
                    <div class="quantity-title">{{ $isStrawberryFresh ? 'Jumlah (kg)' : 'Jumlah kemasan' }}</div>
                    <div class="purchase-row">
                        <div class="quantity-control">
                            <button
                                type="button"
                                id="quantity-minus"
                                aria-label="Kurangi jumlah"
                                {{ ! $dapatDipesan || $stokTersedia < 1 ? 'disabled' : '' }}
                            >−</button>
                            <input
                                type="number"
                                id="quantity"
                                name="jumlah"
                                value="1"
                                min="1"
                                max="{{ $isStrawberryFresh ? max(1, (int) ($initialGrade->stok_kg ?? 0)) : max(1, $stokTersedia) }}"
                                inputmode="numeric"
                                aria-label="Jumlah produk"
                                {{ ! $dapatDipesan ? 'disabled' : '' }}
                                required
                            >
                            <button
                                type="button"
                                id="quantity-plus"
                                aria-label="Tambah jumlah"
                                {{ ! $dapatDipesan || $stokTersedia < 1 ? 'disabled' : '' }}
                            >+</button>
                        </div>

                        <button
                            type="submit"
                            class="add-button"
                            {{ ! $dapatDipesan || ($isStrawberryFresh && (!$initialGrade || (float) $initialGrade->stok_kg < 1)) || (!$isStrawberryFresh && $stokTersedia < 1) ? 'disabled' : '' }}
                        >{{ $dapatDipesan ? 'Tambah ke Keranjang' : 'Produk Habis' }}</button>
                    </div>
                </form>
            </section>
        </div>

    </main>

    <script>
        const quantityInput = document.getElementById('quantity');
        const minusButton = document.getElementById('quantity-minus');
        const plusButton = document.getElementById('quantity-plus');
        const canAddProduct = @json($dapatDipesan);
        const gradeSelect = document.getElementById('grade-select');
        const gradePrice = document.getElementById('grade-price');
        const gradeStock = document.getElementById('grade-stock');
        const gradeDescription = document.getElementById('grade-description');
        const addButton = document.querySelector('.add-button');
        const productImage = document.querySelector('.product-image');

        function syncQuantityButtons() {
            const quantity = Number(quantityInput.value || quantityInput.min);
            const minimum = Number(quantityInput.min);
            const maximum = Number(quantityInput.max);

            const selectedStock = gradeSelect
                ? Number(gradeSelect.selectedOptions[0].dataset.stock)
                : maximum;
            const canBuyQuantity = canAddProduct && selectedStock >= quantity;
            minusButton.disabled = !canBuyQuantity || quantity <= minimum;
            plusButton.disabled = !canBuyQuantity || quantity >= maximum;
            addButton.disabled = !canBuyQuantity;
        }

        if (gradeSelect) {
            gradeSelect.addEventListener('change', function () {
                const option = gradeSelect.selectedOptions[0];
                quantityInput.max = Math.max(1, Math.floor(Number(option.dataset.stock)));
                quantityInput.value = Math.min(Number(quantityInput.value), Number(quantityInput.max));
                gradePrice.textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(Number(option.dataset.price)) + '/kg';
                gradeStock.textContent = 'Stok: ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 3 }).format(Number(option.dataset.stock)) + ' kg';
                productImage.src = "{{ asset('images') }}/" + option.dataset.image;
                gradeDescription.textContent = option.dataset.description;
                syncQuantityButtons();
            });
            gradeSelect.dispatchEvent(new Event('change'));
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