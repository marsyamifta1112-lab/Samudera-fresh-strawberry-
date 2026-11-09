@extends('admin.layout')
@section('title', 'Verifikasi Pembayaran')
@section('content')
    <div class="page-heading"><div><h2>Verifikasi Pembayaran</h2></div></div>
    <section class="panel detail-card payment-card">
        <h3>Verifikasi Pembayaran</h3>
        <div class="detail-grid">
            <div><div class="detail-label">Subtotal Produk</div><div class="detail-value">Rp{{ number_format($pesanan->subtotal_produk, 0, ',', '.') }}</div></div>
            <div><div class="detail-label">Wilayah Tujuan</div><div class="detail-value">{{ $pesanan->wilayah_ongkir ?: 'Tidak tercatat' }}</div></div>
            <div><div class="detail-label">Ongkir</div><div class="detail-value">{{ $pesanan->ongkir === null ? 'Menunggu Admin' : 'Rp'.number_format($pesanan->ongkir, 0, ',', '.') }}</div></div>
            <div><div class="detail-label">Total</div><div class="payment-total">{{ $pesanan->ongkir === null ? 'Menunggu ongkir' : 'Rp'.number_format($pesanan->total_harga, 0, ',', '.') }}</div></div>
            <div><div class="detail-label">Metode Transfer</div><div class="detail-value">{{ $pesanan->metode_pembayaran ?? '-' }} · {{ $pesanan->nama_pelanggan }}</div></div>
            <div><div class="detail-label">Status</div><span class="status status-{{ $pesanan->status_pembayaran }}">{{ ucfirst($pesanan->status_pembayaran) }}</span></div>
            <div><div class="detail-label">Bukti Transfer</div>
                @if ($pesanan->bukti_pembayaran)
                    <a target="_blank" rel="noopener" href="{{ route('admin.payments.proof', $pesanan->id_pesanan) }}">
                        <img class="payment-proof" src="{{ route('admin.payments.proof', $pesanan->id_pesanan) }}" alt="Bukti transfer pesanan #{{ $pesanan->id_pesanan }}">
                    </a>
                @else
                    <span class="muted">Belum diunggah</span>
                @endif
            </div>
        </div>
        @if ($pesanan->status_pembayaran === 'menunggu' && $pesanan->bukti_pembayaran)
            <div class="payment-actions">
                <form method="POST" action="{{ route('admin.payments.review', $pesanan->id_pesanan) }}">@csrf @method('PUT')<input type="hidden" name="status_pembayaran" value="ditolak"><button class="button button-outline" type="submit">Tolak</button></form>
                <form method="POST" action="{{ route('admin.payments.review', $pesanan->id_pesanan) }}">@csrf @method('PUT')<input type="hidden" name="status_pembayaran" value="diverifikasi"><button class="button" type="submit">Terima</button></form>
            </div>
        @endif
        @if ($pesanan->status_pembayaran === 'diverifikasi' && $pesanan->nomor_struk)
            <div class="payment-actions">
                <a class="button" href="{{ route('admin.payments.receipt', $pesanan->id_pesanan) }}">Cetak Struk</a>
            </div>
        @endif
    </section>
    <a class="button button-outline" href="{{ route('admin.orders.show', $pesanan->id_pesanan) }}">Kembali ke Detail Pesanan</a>
    <style>.payment-proof{display:block;width:min(100%,320px);max-height:210px;object-fit:contain;object-position:left center;border:1px solid var(--line);border-radius:4px;background:#fff}</style>
@endsection
