@extends('kurir.layout')
@section('title', 'Konfirmasi Diterima')
@section('content')
    <div class="page-heading"><div><h2>Konfirmasi Diterima</h2></div></div>
    <section class="panel detail-card confirmation">
        <div>
            <div class="confirmation-check" aria-hidden="true">✓</div>
            <h3>Pesanan Diterima</h3>
            <strong>#SP{{ str_pad($pengiriman->id_pesanan, 6, '0', STR_PAD_LEFT) }}</strong>
            <p>Terima kasih telah mengantarkan pesanan Samudera Fresh Strawberry.</p>
            <a class="button" href="{{ route('kurir.dashboard') }}">Kembali ke Beranda</a>
        </div>
    </section>
@endsection
