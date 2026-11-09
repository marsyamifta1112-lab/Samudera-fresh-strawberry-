<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan #{{ $nomorPesanan }} | Samudera Fresh Strawberry</title>

    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #fffdf8; color: #292929; font-family: Arial, Helvetica, sans-serif; }
        main { width: 92%; max-width: 1000px; margin: 24px auto 60px; }
        h1 { margin: 12px 0 26px; text-align: center; font-size: 29px; }
        .panel { margin-bottom: 20px; padding: 27px 32px; border: 1px solid #e8e5df; border-radius: 30px; background: #fff; }
        .panel h2 { margin: 0 0 22px; color: #245b3b; font-size: 20px; }
        .order-heading { margin: 0 0 24px; color: #245b3b; font-size: 17px; font-weight: bold; }
        .timeline { max-width: 600px; margin: 0 auto; }
        .step { position: relative; display: grid; grid-template-columns: 30px minmax(0, 1fr); gap: 16px; min-height: 58px; }
        .step:not(:last-child)::before { position: absolute; top: 15px; bottom: -3px; left: 10px; width: 5px; border-radius: 5px; background: #f9dce3; content: ""; }
        .marker { z-index: 1; width: 15px; height: 15px; margin: 1px 0 0 5px; border: 2px solid #d4c9ca; border-radius: 50%; background: #fff; }
        .step.complete .marker, .step.current .marker { border-color: #e93658; background: #e93658; }
        .step.complete:not(:last-child)::before { background: #f4a9b9; }
        .step-label { padding-bottom: 23px; color: #898989; font-size: 14px; }
        .step.complete .step-label, .step.current .step-label { color: #333; font-weight: bold; }
        .cancelled { margin: 18px 0 0; color: #c6283d; font-weight: bold; }
        .payment-note { margin: -9px 0 21px; padding: 12px 15px; border-radius: 9px; background: #fff2dc; color: #865300; font-size: 13px; }
        .detail-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 0; border-bottom: 1px solid #eee; }
        .product-info { display: flex; align-items: center; gap: 14px; min-width: 0; }
        .product-image { width: 60px; height: 60px; border-radius: 9px; background: #fff5f5; object-fit: cover; }
        .product-name { font-weight: bold; }
        .muted { margin-top: 5px; color: #777; font-size: 13px; }
        .subtotal { white-space: nowrap; font-weight: bold; }
        .total { display: flex; justify-content: space-between; gap: 16px; padding-top: 20px; font-size: 17px; font-weight: bold; }
        .delivery { display: grid; grid-template-columns: 150px 1fr; gap: 12px; font-size: 14px; }
        .delivery dt { color: #777; }
        .delivery dd { margin: 0; overflow-wrap: anywhere; }
        .actions { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; margin-top: 22px; }
        .button { display: inline-block; padding: 12px 19px; border-radius: 7px; background: #e93658; color: #fff; font-size: 14px; font-weight: bold; text-decoration: none; }
        .button.secondary { background: #e5f2e9; color: #245b3b; }
        .return-form { width: 100%; margin-top: 14px; padding: 16px; border-radius: 10px; background: #fffaf7; }
        .return-form label { display: block; margin: 10px 0 5px; font-size: 13px; font-weight: bold; }
        .return-form input, .return-form select, .return-form textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font: inherit; }
        .return-form textarea { min-height: 75px; resize: vertical; }
        .return-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 12px; }
        .return-card { margin-top: 14px; padding: 14px; border: 1px solid #eee; border-radius: 10px; font-size: 14px; }
        .return-status { display: inline-block; margin: 5px 8px 5px 0; padding: 5px 9px; border-radius: 5px; background: #e5f2e9; color: #245b3b; font-size: 12px; }
        .form-alert { margin-bottom: 15px; padding: 12px 15px; border-radius: 8px; background: #e5f2e9; color: #245b3b; }
        .form-alert.error { background: #fde8ec; color: #a3223d; }

        @media (max-width: 620px) {
            .panel { padding: 22px 18px; border-radius: 22px; }
            .delivery { grid-template-columns: 1fr; gap: 5px; }
            .detail-row { align-items: flex-start; }
            .return-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('partials.navigation', ['active' => 'pesanan'])

    <main>
        <h1>Status Pesanan</h1>

        @if (session('success'))
            <div class="form-alert" role="status">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="form-alert error" role="alert">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="form-alert error" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="panel">
            <h2 class="order-heading">No. Pesanan #{{ $nomorPesanan }}</h2>

            @if (($pembayaran->status_pembayaran ?? null) === 'ditolak')
                <p class="payment-note">Bukti pembayaran ditolak. Silakan periksa kembali pembayaran dan hubungi toko jika membutuhkan bantuan.</p>
            @elseif (($pembayaran->status_pembayaran ?? null) === 'menunggu' && $pembayaran->bukti_pembayaran)
                <p class="payment-note">Bukti pembayaran diterima dan sedang menunggu konfirmasi.</p>
            @elseif (($pembayaran->status_pembayaran ?? null) === 'menunggu')
                <p class="payment-note">Pesanan menunggu pembayaran. Selesaikan pembayaran agar pesanan dapat diproses.</p>
            @endif

            @if ($dibatalkan)
                <p class="cancelled">Pesanan ini telah dibatalkan.</p>
            @else
                <div class="timeline" aria-label="Progres pesanan">
                    @foreach (['Pesanan Diproses', 'Pembayaran Dikonfirmasi', 'Diproses', 'Dalam Pengiriman', 'Pesanan Selesai'] as $index => $label)
                        <div @class([
                            'step',
                            'complete' => $index < $langkahAktif,
                            'current' => $index === $langkahAktif,
                        ])>
                            <span class="marker" aria-hidden="true"></span>
                            <span class="step-label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <p class="muted">Status saat ini: <strong>{{ $statusPesanan }}</strong></p>
        </section>

        <section class="panel">
            <h2>Detail Pesanan</h2>

            @forelse ($detailPesanan as $item)
                <div class="detail-row">
                    <div class="product-info">
                        @if ($item->foto_produk)
                            <img
                                class="product-image"
                                src="{{ asset('images/' . $item->foto_produk) }}"
                                alt="{{ $item->nama_produk ?? 'Produk' }}"
                            >
                        @endif

                        <div>
                            <div class="product-name">{{ $item->nama_produk ?? 'Produk tidak tersedia' }}</div>
                            <div class="muted">{{ $item->jumlah }} x Rp{{ number_format($item->harga, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <span class="subtotal">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>

                @php
                    $returnRequests = $returnsByDetail->get($item->id_detail, collect());
                    $quantityReturned = (int) $returnRequests->where('status_return', '<>', 'Ditolak')->sum('jumlah');
                    $quantityAvailable = max(0, (int) $item->jumlah - $quantityReturned);
                @endphp

                @if ($bolehReturn && $quantityAvailable > 0)
                    <form class="return-form" method="POST" enctype="multipart/form-data" action="{{ route('pesanan.return.store', $pesanan->id_pesanan) }}">
                        @csrf

                        <h3>Ajukan Return Produk Ini</h3>
                        <p class="muted">Jumlah yang masih dapat diajukan return: {{ $quantityAvailable }}</p>

                        <input type="hidden" name="id_detail" value="{{ $item->id_detail }}">

                        <div class="return-grid">
                            <div>
                                <label for="jumlah-{{ $item->id_detail }}">Jumlah *</label>
                                <input id="jumlah-{{ $item->id_detail }}" type="number" name="jumlah" min="1" max="{{ $quantityAvailable }}" required>
                            </div>

                            <div>
                                <label for="foto-{{ $item->id_detail }}">Foto Bukti *</label>
                                <input id="foto-{{ $item->id_detail }}" type="file" name="foto_bukti" accept="image/jpeg,image/png,image/webp" required>
                            </div>

                            <div>
                                <label for="bank-{{ $item->id_detail }}">Bank untuk Pengembalian *</label>
                                <select id="bank-{{ $item->id_detail }}" name="bank_pengembalian" required>
                                    <option value="">Pilih bank</option>
                                    @foreach (['BCA', 'BRI', 'BNI', 'Mandiri'] as $bank)
                                        <option value="{{ $bank }}">{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="pemilik-{{ $item->id_detail }}">Nama Pemilik Rekening *</label>
                                <input id="pemilik-{{ $item->id_detail }}" name="nama_pemilik_rekening" maxlength="100" required>
                            </div>

                            <div>
                                <label for="rekening-{{ $item->id_detail }}">Nomor Rekening *</label>
                                <input id="rekening-{{ $item->id_detail }}" name="nomor_rekening" inputmode="numeric" pattern="[0-9]+" maxlength="34" required>
                            </div>
                        </div>

                        <label for="alasan-{{ $item->id_detail }}">Alasan Return *</label>
                        <textarea id="alasan-{{ $item->id_detail }}" name="alasan" minlength="10" maxlength="2000" required></textarea>

                        <button class="button" type="submit">Kirim Pengajuan Return</button>
                    </form>
                @endif
            @empty
                <p class="muted">Rincian produk pesanan tidak tersedia.</p>
            @endforelse

            <div class="total">
                <span>Subtotal Produk</span>
                <span>Rp{{ number_format($pesanan->subtotal_produk, 0, ',', '.') }}</span>
            </div>
            <div class="total">
                <span>Wilayah Tujuan</span>
                <span>{{ $pesanan->wilayah_ongkir ?? 'Tidak tercatat' }}</span>
            </div>
            <div class="total">
                <span>Ongkir</span>
                <span>{{ $pesanan->ongkir === null ? 'Menunggu penetapan Admin' : 'Rp'.number_format($pesanan->ongkir, 0, ',', '.') }}</span>
            </div>
            <div class="total">
                <span>Total Pembayaran</span>
                <span>{{ $pesanan->ongkir === null ? 'Menunggu ongkir' : 'Rp'.number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </section>

        @if ($returnsByDetail->isNotEmpty())
            <section class="panel">
                <h2>Status Return</h2>

                @foreach ($returnsByDetail as $requests)
                    @foreach ($requests as $return)
                        <article class="return-card">
                            <strong>{{ $detailPesanan->firstWhere('id_detail', $return->id_detail)->nama_produk ?? 'Produk' }}</strong>

                            <span class="return-status">{{ $return->status_return }}</span>
                            <span class="return-status">Pengembalian dana: {{ $return->status_pengembalian_dana }}</span>

                            <p class="muted">
                                Jumlah: {{ $return->jumlah }} &bull;
                                Nilai pengembalian: Rp{{ number_format($return->jumlah_pengembalian, 0, ',', '.') }}
                            </p>

                            @if ($return->catatan_admin)
                                <p class="muted">Catatan Admin: {{ $return->catatan_admin }}</p>
                            @endif

                            <a href="{{ route('pesanan.return.proof', [$pesanan->id_pesanan, $return->id_return]) }}">Lihat foto bukti</a>
                        </article>
                    @endforeach
                @endforeach
            </section>
        @endif

        <section class="panel">
            <h2>Informasi Pengiriman</h2>

            <dl class="delivery">
                <dt>Nama penerima</dt>
                <dd>{{ $pesanan->nama_penerima ?: '-' }}</dd>

                <dt>Nomor HP</dt>
                <dd>{{ $pesanan->no_hp ?: '-' }}</dd>

                <dt>Alamat</dt>
                <dd>{{ $pesanan->alamat_pengiriman ?: '-' }}</dd>

                @if ($pesanan->catatan)
                    <dt>Catatan</dt>
                    <dd>{{ $pesanan->catatan }}</dd>
                @endif

                @if ($pengiriman)
                    <dt>Status pengiriman</dt>
                    <dd>{{ ucfirst($pengiriman->status_pengiriman) }}</dd>
                @endif
            </dl>
        </section>

        <div class="actions">
            <a class="button secondary" href="{{ route('pesanan.index') }}">← Riwayat Pesanan</a>

            @if ($pembayaran && $pembayaran->status_pembayaran !== 'diverifikasi' && !$dibatalkan)
                <a class="button" href="{{ route('pembayaran.show', $pesanan->id_pesanan) }}">Lihat Pembayaran</a>
            @endif
        </div>
    </main>
</body>
</html>