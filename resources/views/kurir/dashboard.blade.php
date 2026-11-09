@extends('kurir.layout')
@section('title', 'Dashboard Kurir')
@section('content')
    <div class="page-heading"><div><h2>Dashboard Kurir</h2></div></div>
    <section class="cards">
        <article class="card"><div class="card-label">Pengiriman Hari Ini</div><div class="card-value">{{ number_format($ringkasan['hari_ini']) }}</div></article>
        <article class="card"><div class="card-label">Sedang Dikirim</div><div class="card-value">{{ number_format($ringkasan['dikirim']) }}</div></article>
        <article class="card"><div class="card-label">Selesai</div><div class="card-value green">{{ number_format($ringkasan['diterima']) }}</div></article>
    </section>
    <section class="panel">
        <h3>Daftar Tujuan</h3>
        <div class="destination-list">
            @forelse ($tujuan as $namaTujuan)
                <span>{{ $namaTujuan }}</span>
            @empty
                <p class="empty">Belum ada tujuan pengiriman aktif.</p>
            @endforelse
        </div>
        <div style="text-align:right;margin-top:18px"><a class="button" href="{{ route('kurir.shipments') }}">Lihat Data Pengiriman</a></div>
    </section>
@endsection
