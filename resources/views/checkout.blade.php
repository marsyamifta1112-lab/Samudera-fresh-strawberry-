<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Samudera Fresh Strawberry</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fffdf8;
            color: #333;
        }
        header {
            background: #fffdf8;
            padding: 15px 4%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            border-bottom: 1px solid #eee;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #245b3b;
            font-weight: bold;
            text-decoration: none;
        }
        .brand img { width: 60px; height: 55px; object-fit: contain; }
        .brand small { display: block; margin-top: 4px; color: #e93658; }
        nav { display: flex; flex-wrap: wrap; gap: 28px; }
        nav a { color: #444; text-decoration: none; font-size: 13px; }
        nav a:hover { color: #e93658; }
        main { max-width: 1180px; width: 92%; margin: 34px auto 60px; }
        h1 { color: #292929; text-align: center; font-size: 30px; margin: 8px 0 30px; }
        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(260px, 0.8fr);
            gap: 28px;
            align-items: stretch;
        }
        .panel {
            background: white;
            padding: 25px 30px;
            border: 1px solid #eee;
            border-radius: 30px;
            margin-bottom: 20px;
        }
        .panel h2 { margin: 0 0 18px; color: #245b3b; font-size: 20px; }
        .summary-panel { background: #204b35; color: #fff; border: 0; }
        .summary-panel h2 { color: #fff; }
        .summary-panel .item { border-color: #ffffff38; }
        label { display: block; font-weight: bold; margin: 15px 0 7px; }
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 7px;
            font: inherit;
        }
        .payment-options { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
        }
        .payment-option input { width: auto; accent-color: #e93658; }
        textarea { min-height: 90px; resize: vertical; }
        .item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .total {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .btn {
            display: block;
            width: 100%;
            border: 0;
            border-radius: 8px;
            background: #e93658;
            color: white;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-top: 18px;
        }
        .back { color: #245b3b; text-decoration: none; }
        .error { color: #b51f35; font-size: 14px; margin-top: 5px; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 15px; background: #ffe5e8; }
        .shipping-schedule { display:flex; justify-content:space-between; align-items:center; gap:20px; margin:0 0 22px; padding:17px 20px; border:1px solid #f4d8de; border-radius:14px; background:#fff8f8; }
        .shipping-schedule > div { display:grid; gap:5px; }
        .shipping-label { color:#777; font-size:11px; }
        .shipping-schedule strong { color:#245b3b; font-size:15px; }
        .shipping-schedule p,.shipping-schedule small { margin:0; color:#666; font-size:11px; line-height:1.5; }
        .shipping-schedule small { max-width:260px; }
        @media (max-width: 700px) {
            header { align-items: flex-start; flex-direction: column; }
            .layout { grid-template-columns: 1fr; }
            .panel { padding: 21px; }
            .shipping-schedule { align-items:flex-start; flex-direction:column; gap:8px; }
        }
    </style>
</head>
<body>
@include('partials.navigation', ['active' => 'keranjang'])

<main>
    <a class="back" href="{{ route('keranjang.index') }}">← Kembali ke Keranjang</a>
    <h1>Checkout</h1>
    <p>Lengkapi informasi penerima untuk melanjutkan pesanan.</p>

    @include('partials.operational-hours')

    <section class="shipping-schedule" aria-label="Jadwal pengiriman">
        <div>
            <span class="shipping-label">Jadwal barang berangkat dari toko</span>
            <strong>{{ $operationalSchedule['label_pengiriman'] }}</strong>
            <p>{{ $operationalSchedule['pesan_pengiriman'] }}</p>
        </div>
        <small>Perkiraan tiba ditampilkan terpisah berdasarkan wilayah tujuan. Jadwal berangkat dapat disesuaikan setelah pembayaran dikonfirmasi.</small>
    </section>

    @if (session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert">
            Mohon periksa kembali data yang kamu masukkan.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="layout">
            <section class="panel">
                <h2>Informasi Pengiriman</h2>

                <label for="nama_penerima">Nama Penerima</label>
                <input id="nama_penerima" name="nama_penerima"
                    value="{{ old('nama_penerima') }}" maxlength="100" required>

                <label for="alamat_pengiriman">Alamat Lengkap (cantumkan kecamatan/kota)</label>
                <textarea id="alamat_pengiriman" name="alamat_pengiriman"
                    maxlength="1000" required>{{ old('alamat_pengiriman') }}</textarea>

                <label for="no_hp">Nomor HP</label>
                <input id="no_hp" name="no_hp" type="tel"
                    value="{{ old('no_hp') }}" maxlength="20" required>

                <label for="catatan">Catatan (Opsional)</label>
                <textarea id="catatan" name="catatan"
                    maxlength="1000">{{ old('catatan') }}</textarea>

                <label>Metode Pembayaran</label>
                <div class="payment-options">
                    @foreach (['Transfer', 'QRIS'] as $metode)
                        <label class="payment-option">
                            <input
                                type="radio"
                                name="metode_pembayaran"
                                value="{{ $metode }}"
                                {{ old('metode_pembayaran', 'Transfer') === $metode ? 'checked' : '' }}
                                required
                            >
                            {{ $metode }}
                        </label>
                    @endforeach
                </div>
            </section>

            <aside class="panel summary-panel">
                <h2>Ringkasan Pesanan</h2>

                <p>{{ count($keranjang) }} produk</p>
                @foreach ($keranjang as $item)
                    <div class="item">
                        <div>
                            <strong>{{ $item['nama_produk'] }}</strong>
                            <div>Jumlah: {{ $item['jumlah'] }}</div>
                        </div>
                        <div>
                            Rp{{ number_format($item['harga_produk'] * $item['jumlah'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach

                <div class="item">
                    <strong>Subtotal Produk</strong>
                    <strong>Rp{{ number_format($subtotalProduk, 0, ',', '.') }}</strong>
                </div>
                <div class="item">
                    <span>Wilayah Terdeteksi</span>
                    <span id="wilayah-display" aria-live="polite">Belum terdeteksi</span>
                </div>
                <div class="item">
                    <span>Perkiraan tiba di rumah</span>
                    <span id="arrival-estimate-display" aria-live="polite">Isi alamat pengiriman</span>
                </div>
                <div class="item">
                    <span>Ongkir</span>
                    <span id="ongkir-display">Isi alamat pengiriman</span>
                </div>
                <div class="total">
                    <span>Total Pembayaran</span>
                    <span id="total-display">Isi alamat pengiriman</span>
                </div>

                <p id="ongkir-note">Ongkir dihitung otomatis dari kecamatan/kota pada alamat dan tidak melebihi subtotal produk.</p>
                <p class="muted">Estimasi dihitung dalam hari kalender dari wilayah dan jadwal toko; dapat berubah mengikuti kurir.</p>
                <button type="submit" class="btn">Buat Pesanan &amp; Lanjut Bayar</button>
            </aside>
        </div>
    </form>
</main>
<script>
    const alamatPengiriman = document.getElementById('alamat_pengiriman');
    const wilayahDisplay = document.getElementById('wilayah-display');
    const arrivalEstimateDisplay = document.getElementById('arrival-estimate-display');
    const ongkirDisplay = document.getElementById('ongkir-display');
    const totalDisplay = document.getElementById('total-display');
    const subtotalProduk = {{ (int) $subtotalProduk }};
    const aturanWilayah = @json($aturanWilayahOngkir);
    const tarifWilayah = @json($tarifWilayahOngkir);
    const arrivalEstimates = @json($arrivalEstimates);
    const formatRupiah = (amount) => 'Rp' + new Intl.NumberFormat('id-ID').format(amount);

    function updateOrderTotal() {
        const alamat = alamatPengiriman.value.toLocaleLowerCase('id-ID');
        const aturan = aturanWilayah.find((item) =>
            item.keywords.some((keyword) => alamat.includes(keyword))
        ) || aturanWilayah[aturanWilayah.length - 1];

        if (!alamat.trim()) {
            wilayahDisplay.textContent = 'Belum terdeteksi';
            arrivalEstimateDisplay.textContent = 'Isi alamat pengiriman';
            ongkirDisplay.textContent = 'Isi alamat pengiriman';
            totalDisplay.textContent = 'Isi alamat pengiriman';
            return;
        }

        const arrivalEstimate = arrivalEstimates[aturan.name];
        arrivalEstimateDisplay.textContent = arrivalEstimate
            ? arrivalEstimate.label
            : 'Estimasi belum tersedia';
        const tarif = tarifWilayah[aturan.name];
        if (typeof tarif !== 'number') {
            wilayahDisplay.textContent = aturan.name;
            ongkirDisplay.textContent = 'Tarif belum tersedia';
            totalDisplay.textContent = 'Hubungi Admin';
            return;
        }

        const ongkir = Math.min(tarif, subtotalProduk);
        wilayahDisplay.textContent = aturan.name;
        ongkirDisplay.textContent = formatRupiah(ongkir);
        totalDisplay.textContent = formatRupiah(subtotalProduk + ongkir);
    }

    alamatPengiriman.addEventListener('input', updateOrderTotal);
    updateOrderTotal();
</script>
</body>
</html>