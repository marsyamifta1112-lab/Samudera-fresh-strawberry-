@extends('admin.layout')
@section('title', 'Detail Pesanan (Admin)')
@section('content')
    <div class="page-heading"><div><h2>Detail Pesanan (Admin)</h2></div></div>
    <section class="panel detail-card">
        <div class="detail-grid">
            <div><div class="detail-label">Pelanggan</div><div class="detail-value">{{ $pesanan->nama_pelanggan }}</div></div>
            <div><div class="detail-label">Tanggal Pesanan</div><div class="detail-value">{{ $pesanan->tanggal_pemesanan ? \Illuminate\Support\Carbon::parse($pesanan->tanggal_pemesanan)->format('d/m/Y') : '-' }}</div></div>
            <div style="grid-column:1/-1"><div class="detail-label">Alamat</div><div class="detail-value">{{ $pesanan->alamat_pengiriman ?? 'Alamat tidak tersedia' }}</div></div>
            <div><div class="detail-label">Produk</div><div class="detail-products">
                @forelse ($detailPesanan as $item)
                    <div class="detail-product">
                        @if ($item->foto_produk)<img class="product-thumb" src="{{ asset('images/'.$item->foto_produk) }}" alt="">@else<img class="product-thumb" src="{{ asset('images/strawberry.jpeg') }}" alt="">@endif
                        <span>{{ $item->nama_produk ?? 'Produk dihapus' }} · {{ $item->jumlah }} pcs · Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <span class="muted">Detail produk tidak tersedia.</span>
                @endforelse
            </div></div>
            <div><div class="detail-label">Subtotal Produk</div><div class="detail-value">Rp{{ number_format($pesanan->subtotal_produk, 0, ',', '.') }}</div></div>
            <div><div class="detail-label">Wilayah Tujuan</div><div class="detail-value">{{ $pesanan->wilayah_ongkir ?: 'Tidak tercatat' }}</div></div>
            <div><div class="detail-label">Ongkir</div><div class="detail-value">{{ $pesanan->ongkir === null ? 'Belum ditetapkan' : 'Rp'.number_format($pesanan->ongkir, 0, ',', '.') }}</div></div>
            <div><div class="detail-label">Total Pembayaran</div><div class="detail-total">{{ $pesanan->ongkir === null ? 'Menunggu ongkir' : 'Rp'.number_format($pesanan->total_harga, 0, ',', '.') }}</div></div>
        </div>
        @if (session('success'))<p class="success" role="status">{{ session('success') }}</p>@endif
        @if (session('error'))<p class="error" role="alert">{{ session('error') }}</p>@endif
        @if ($errors->any())<p class="error" role="alert">{{ $errors->first() }}</p>@endif
        @if (! $pesanan->status_pembayaran || (! $pesanan->bukti_pembayaran && $pesanan->status_pembayaran !== 'diverifikasi'))
            <form method="POST" action="{{ route('admin.orders.ongkir.update', $pesanan->id_pesanan) }}" class="shipping-form">
                @csrf
                @method('PUT')
                <label for="ongkir">Tetapkan ongkir (Rp)</label>
                <input id="ongkir" name="ongkir" type="text" inputmode="numeric" pattern="[0-9.]*" maxlength="11" placeholder="Contoh: 10.000" value="{{ old('ongkir', $pesanan->ongkir === null ? '' : number_format($pesanan->ongkir, 0, ',', '.')) }}" required>
                <p class="muted">Ongkir maksimal sebesar subtotal produk dalam pesanan.</p>
                <button class="button" type="submit">Simpan Ongkir</button>
            </form>
        @else
            <p class="muted">Ongkir dikunci karena bukti pembayaran sudah diunggah atau pembayaran sudah diverifikasi.</p>
        @endif
        <div class="form-submit">
            @if ($pesanan->status_pembayaran)
                <a class="button" href="{{ route('admin.payments.show', $pesanan->id_pesanan) }}">Verifikasi Pembayaran</a>
            @else
                <span class="status">Pembayaran belum dibuat</span>
            @endif
        </div>
    </section>
    <div class="actions"><a class="button button-outline" href="{{ route('admin.orders') }}">Kembali ke Daftar Pesanan</a></div>
    <style>.form-submit{display:flex;justify-content:flex-end;margin-top:18px}.shipping-form{display:grid;grid-template-columns:1fr auto;gap:10px;align-items:end;margin-top:20px}.shipping-form label{grid-column:1/-1}.shipping-form input{min-height:42px;padding:8px 10px;border:1px solid var(--line);border-radius:4px}.shipping-form .button{margin:0}.success{color:#205936}.error{color:#b4233c}@media(max-width:600px){.shipping-form{grid-template-columns:1fr}}</style>
@endsection
