<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan | Samudera Fresh Strawberry</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #fffdf8; color: #292929; font-family: Arial, Helvetica, sans-serif; }
        main { width: 92%; max-width: 1000px; margin: 26px auto 60px; }
        h1 { margin: 10px 0 25px; text-align: center; font-size: 29px; }
        .alert { margin: 14px 0; padding: 13px 16px; border-radius: 9px; background: #e5f2e9; color: #245b3b; }
        .order-list { display: grid; gap: 16px; }
        .order-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
            min-height: 86px;
            padding: 19px 24px;
            border: 1px solid #e8e5df;
            border-radius: 13px;
            background: #fff;
            text-decoration: none;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }
        .order-card:hover { border-color: #e93658; box-shadow: 0 5px 18px #0000000a; transform: translateY(-1px); }
        .order-number { color: #245b3b; font-size: 16px; font-weight: bold; }
        .order-meta { display: flex; align-items: center; justify-content: flex-end; gap: 8px; color: #777; font-size: 13px; text-align: right; }
        .status { color: #245b3b; }
        .status.warning { color: #b36b00; }
        .status.danger { color: #c6283d; }
        .empty { padding: 42px 20px; border: 1px solid #eee; border-radius: 15px; background: #fff; text-align: center; }
        .empty p { color: #777; }
        .button { display: inline-block; margin-top: 12px; padding: 12px 20px; border-radius: 7px; background: #e93658; color: #fff; font-size: 14px; font-weight: bold; text-decoration: none; }
        @media (max-width: 620px) {
            main { width: 92%; }
            .order-card { align-items: flex-start; flex-direction: column; gap: 10px; padding: 18px; }
            .order-meta { flex-wrap: wrap; justify-content: flex-start; text-align: left; }
        }
    </style>
</head>
<body>
    @include('partials.navigation', ['active' => 'pesanan'])

    <main>
        <h1>Riwayat Pesanan</h1>

        @if (session('success'))
            <div class="alert" role="status">{{ session('success') }}</div>
        @endif

        @if ($pesanan->isEmpty())
            <section class="empty">
                <h2>Belum Ada Pesanan</h2>
                <p>Pesanan yang kamu buat akan muncul di halaman ini.</p>
                <a class="button" href="{{ route('produk.index') }}">Lihat Produk</a>
            </section>
        @else
            <section class="order-list" aria-label="Daftar riwayat pesanan">
                @foreach ($pesanan as $item)
                    @php
                        $statusClass = in_array($item->status_label, ['Dibatalkan', 'Pembayaran Ditolak'], true)
                            ? 'danger'
                            : (in_array($item->status_label, ['Menunggu Pembayaran', 'Pembayaran Dikonfirmasi'], true) ? 'warning' : '');
                    @endphp
                    <a class="order-card" href="{{ route('pesanan.show', $item->id_pesanan) }}">
                        <span class="order-number">#{{ $item->nomor_pesanan }}</span>
                        <span class="order-meta">
                            <span>{{ session('nama_pelanggan', 'Pelanggan') }}</span>
                            <span aria-hidden="true">&bull;</span>
                            <span>{{ $item->ongkir === null ? 'Menunggu ongkir' : 'Rp'.number_format($item->total_harga, 0, ',', '.') }}</span>
                            <span aria-hidden="true">&bull;</span>
                            <span class="status {{ $statusClass }}">{{ $item->status_label }}</span>
                        </span>
                    </a>
                @endforeach
            </section>
        @endif
    </main>
</body>
</html>
