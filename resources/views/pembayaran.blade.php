<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Transfer | Samudera Fresh Strawberry</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #fffdf8; color: #292929; font-family: Arial, Helvetica, sans-serif; }
        header {
            min-height: 84px;
            padding: 12px 4%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            background: #fffdf8;
            border-bottom: 1px solid #eee;
        }
        .brand { display: flex; align-items: center; gap: 10px; color: #245b3b; font-size: 14px; font-weight: bold; text-decoration: none; }
        .brand img { width: 60px; height: 55px; object-fit: contain; }
        .brand small { display: block; margin-top: 4px; color: #e93658; }
        nav { display: flex; align-items: center; gap: 28px; }
        nav a { color: #444; font-size: 13px; text-decoration: none; }
        nav a:hover { color: #e93658; }
        main { width: 92%; max-width: 1000px; margin: 24px auto 60px; }
        h1 { margin: 18px 0 25px; text-align: center; font-size: 30px; }
        .panel {
            padding: 28px 34px;
            border: 1px solid #eee;
            border-radius: 32px;
            background: #fff;
        }
        .panel h2 { margin: 0 0 20px; color: #245b3b; font-size: 21px; }
        .order-id { margin: 0 0 18px; text-align: center; color: #777; font-size: 14px; }
        .alert { margin: 18px 0; padding: 13px 16px; border-radius: 9px; background: #e5f2e9; color: #245b3b; }
        .alert.error { background: #fde3e8; color: #a91f32; }
        .method-row { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .method {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 14px 18px;
            border: 1px solid #eee;
            border-radius: 8px;
            background: #f8f8f6;
            color: #333;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }
        .method:has(input:checked) {
            border-color: #e93658;
            background: #fdebf0;
            color: #c92547;
        }
        .method input { accent-color: #e93658; }
        .bank-submit { margin: 16px 0 0; }
        .account-label { display: block; margin: 28px 0 9px; font-size: 14px; font-weight: bold; }
        .account {
            padding: 19px;
            border-radius: 10px;
            background: #e5f2e9;
            color: #245b3b;
            font-size: 16px;
            font-weight: bold;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 22px 0 4px;
            align-items: center;
        }
        .amount { color: #e93658; font-size: 23px; font-weight: bold; }
        .button {
            display: inline-block;
            margin-top: 24px;
            padding: 14px 24px;
            border: 0;
            border-radius: 7px;
            background: #e93658;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
        .button:hover { background: #c92547; }
        .muted { color: #777; font-size: 13px; line-height: 1.6; }
        @media (max-width: 650px) {
            header { align-items: flex-start; flex-direction: column; }
            nav { flex-wrap: wrap; gap: 15px; }
            .panel { padding: 23px 19px; }
            .method-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .amount-row { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    @include('partials.navigation', ['active' => 'keranjang'])

    <main>
        <h1>Pembayaran Transfer</h1>
        <p class="order-id">Pesanan #{{ $pesanan->id_pesanan }}</p>

        @if (session('success'))
            <div class="alert" role="status">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert error" role="alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="panel">
            <h2>Bank Pengirim</h2>
            <p class="muted">Pilih bank yang kamu gunakan untuk transfer. Transfer ditujukan ke rekening BCA Samudera Fresh Strawberry di bawah; pelanggan dari bank lain juga bisa transfer ke rekening tersebut.</p>
            @if ($pesanan->ongkir !== null)
            <form action="{{ route('pembayaran.method', $pesanan->id_pesanan) }}" method="POST">
                @csrf
                <div class="method-row">
                    @foreach (['BCA', 'BRI', 'BNI', 'Mandiri'] as $bank)
                        <label class="method">
                            <input
                                type="radio"
                                name="metode_pembayaran"
                                value="{{ $bank }}"
                                {{ $pembayaran->metode_pembayaran === $bank ? 'checked' : '' }}
                                {{ $pembayaran->bukti_pembayaran ? 'disabled' : 'required' }}
                            >
                            {{ $bank }}
                        </label>
                    @endforeach
                </div>
                @if (!$pembayaran->bukti_pembayaran)
                    <button type="submit" class="button bank-submit">Simpan Bank Pengirim</button>
                @endif
            </form>
                @else
                    <div class="alert error">Admin akan menentukan ongkir berdasarkan alamat pengiriman. Silakan kembali setelah total pembayaran diperbarui.</div>
                @endif

            <label class="account-label">Nomor Rekening</label>
            <div class="account">7829 8832 8274 (a.n. Samudera Fresh Strawberry)</div>

            <div class="amount-row">
                <div>
                    <div>Subtotal produk</div>
                    <div>Rp{{ number_format($pesanan->subtotal_produk, 0, ',', '.') }}</div>
                    <div>Wilayah tujuan</div>
                    <div>{{ $pesanan->wilayah_ongkir ?: 'Tidak tercatat' }}</div>
                    <div>Ongkir</div>
                    <div>{{ $pesanan->ongkir === null ? 'Menunggu penetapan Admin' : 'Rp'.number_format($pesanan->ongkir, 0, ',', '.') }}</div>
                    <div>Total pembayaran</div>
                    <div class="amount">{{ $pesanan->ongkir === null ? 'Menunggu ongkir' : 'Rp'.number_format($pesanan->total_harga, 0, ',', '.') }}</div>
                </div>
                <div class="muted">
                    Status:
                    <strong>{{ ucfirst($pembayaran->status_pembayaran) }}</strong>
                </div>
            </div>

            @if ($pembayaran->status_pembayaran === 'diverifikasi' && $pembayaran->nomor_struk)
                <div class="alert">
                    Pembayaran sudah diverifikasi. Struk pembayaran tersedia untuk dicetak.
                </div>
                <a class="button" href="{{ route('pembayaran.receipt', $pesanan->id_pesanan) }}">Cetak Struk</a>
            @elseif ($pembayaran->bukti_pembayaran)
                <div class="alert">
                    Bukti transfer sudah diterima dan sedang menunggu verifikasi.
                </div>
            @elseif ($pesanan->ongkir === null)
                <p class="muted">Unggah bukti pembayaran setelah ongkir ditetapkan Admin.</p>
            @else
                <p class="muted">Transfer sesuai nominal di atas, lalu unggah bukti pembayaran.</p>
            @endif

            @if ($pembayaran->status_pembayaran !== 'diverifikasi' && $pesanan->ongkir !== null)
                <a class="button" href="{{ route('pembayaran.upload.form', $pesanan->id_pesanan) }}">
                    {{ $pembayaran->bukti_pembayaran ? 'Lihat / Unggah Ulang Bukti' : 'Upload Bukti Pembayaran' }}
                </a>
            @endif
            <a class="button" href="{{ route('pesanan.show', $pesanan->id_pesanan) }}">
                Lihat Status Pesanan
            </a>
        </section>
    </main>
</body>
</html>
