@extends('admin.layout')
@section('title', 'Kelola Pengiriman')
@section('content')
    <div class="page-heading"><div><h2>Kelola Pengiriman</h2></div></div>
    <section class="shipment-list">
        @forelse ($pesanan as $order)
            <article class="shipment-row">
                @if ($order->id_pengiriman)
                    <span class="status status-{{ $order->status_pengiriman }}">{{ strtoupper($order->status_pengiriman) }}</span>
                @else
                    <span class="status">BELUM DITUGASKAN</span>
                @endif
                <div class="shipment-route">
                    <strong>{{ $order->nama_penerima }}</strong>
                    <span class="muted"> · {{ $order->alamat_pengiriman }}</span>
                    @if ($order->nama_kurir)<small class="muted"> · {{ $order->nama_kurir }}</small>@endif
                </div>
                <div class="actions">
                    <form class="inline-form" method="POST" action="{{ route('admin.shipments.assign', $order->id_pesanan) }}">@csrf @method('PUT')
                        <select name="id_petugas" aria-label="Pilih kurir" required><option value="">Pilih kurir</option>@foreach ($kurir as $person)<option value="{{ $person->id_petugas }}" @selected($order->id_petugas === $person->id_petugas)>{{ $person->nama_petugas }}</option>@endforeach</select>
                        <button class="button button-small" type="submit">Tugaskan</button>
                    </form>
                    @if ($order->id_pengiriman)
                        <form class="inline-form" method="POST" action="{{ route('admin.shipments.update', $order->id_pengiriman) }}">@csrf @method('PUT')
                            <select name="status_pengiriman" aria-label="Status pengiriman">@foreach (['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'diterima' => 'Diterima'] as $key => $label)<option value="{{ $key }}" @selected($order->status_pengiriman === $key)>{{ $label }}</option>@endforeach</select>
                            <button class="button button-outline button-small" type="submit">Simpan</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="panel empty">Belum ada pesanan terverifikasi untuk dikirim.</div>
        @endforelse
    </section>
    <div class="pagination">{{ $pesanan->links() }}</div>
@endsection
