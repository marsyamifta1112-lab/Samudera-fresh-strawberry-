@extends('kurir.layout')
@section('title', 'Detail Pengiriman')
@section('content')
    <div class="page-heading"><div><h2>Detail Pengiriman (Kurir)</h2></div></div>
    <section class="panel detail-card">
        <h3 class="detail-title">Detail Pengiriman #SP{{ str_pad($pengiriman->id_pesanan, 6, '0', STR_PAD_LEFT) }}</h3>
        <div class="detail-address">
            <strong>{{ $pengiriman->nama_penerima ?: 'Penerima pesanan' }} · {{ $pengiriman->no_telp_penerima ?: 'Nomor telepon tidak tersedia' }}</strong><br>
            {{ $pengiriman->alamat_pengiriman ?: 'Alamat tidak tersedia' }}
            @if ($pengiriman->catatan)<br><strong>Catatan:</strong> {{ $pengiriman->catatan }}@endif
        </div>
        <div class="progress" aria-label="Status pengiriman">
            @foreach (['menunggu' => 'Diambil', 'dikirim' => 'Dalam Pengiriman', 'diterima' => 'Tiba di Tujuan'] as $key => $label)
                @php
                    $statusOrder = ['menunggu' => 0, 'diproses' => 0, 'dikirim' => 1, 'diterima' => 2];
                    $stepIndex = array_search($key, array_keys(['menunggu' => 0, 'dikirim' => 1, 'diterima' => 2]), true);
                    $isDone = $statusOrder[$pengiriman->status_pengiriman] >= $stepIndex;
                @endphp
                @if (! $loop->first)<span class="progress-line"></span>@endif
                <span class="progress-step {{ $isDone ? 'done' : '' }}"><span class="progress-dot"></span>{{ $label }}</span>
            @endforeach
        </div>
        <div class="detail-actions">
            @php
                $nextStatus = ['menunggu' => 'diproses', 'diproses' => 'dikirim', 'dikirim' => 'diterima'][$pengiriman->status_pengiriman] ?? null;
            @endphp
            @if ($nextStatus)
                <form method="POST" action="{{ route('kurir.shipments.update', $pengiriman->id_pengiriman) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status_pengiriman" value="{{ $nextStatus }}">
                    <button class="button" type="submit">{{ $nextStatus === 'diterima' ? 'Konfirmasi Diterima' : 'Update Status' }}</button>
                </form>
            @else
                <a class="button button-outline" href="{{ route('kurir.confirmation', $pengiriman->id_pengiriman) }}">Lihat Konfirmasi</a>
            @endif
        </div>
    </section>
@endsection
