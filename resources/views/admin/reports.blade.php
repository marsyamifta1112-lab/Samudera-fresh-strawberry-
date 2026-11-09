@extends('admin.layout')
@section('title', 'Laporan Penjualan')
@section('content')
    <div class="page-heading">
        <div><h2>Laporan Penjualan</h2></div>
        <a class="button button-outline button-small" href="{{ route('admin.reports.export', ['dari' => $from, 'sampai' => $to]) }}">Unduh Excel (.xlsx)</a>
    </div>
    <section class="panel report-card">
        <div class="toolbar">
            <form method="GET" action="{{ route('admin.reports') }}">
                <label>Dari <input type="date" name="dari" value="{{ $from }}"></label>
                <label>Sampai <input type="date" name="sampai" value="{{ $to }}"></label>
                <button class="button button-small" type="submit">Terapkan</button>
            </form>
        </div>
        <div class="detail-grid">
            <div><div class="detail-label">{{ \Illuminate\Support\Carbon::parse($from)->format('d F Y') }} – {{ \Illuminate\Support\Carbon::parse($to)->format('d F Y') }}</div><div class="detail-label">Total Penjualan</div><div class="report-summary">Rp {{ number_format($ringkasan['total_penjualan'], 0, ',', '.') }}</div></div>
            <div><h3>Grafik Penjualan Bulanan</h3>
                @php($maxBulanan = max(1, (float) $grafikBulanan->max('total')))
                <div class="report-chart">
                    @foreach ($grafikBulanan as $index => $bulan)
                        <div class="report-bar-group" title="Rp {{ number_format($bulan->total, 0, ',', '.') }}">
                            <div class="report-bar {{ $index % 2 === 0 ? 'pink' : '' }}" style="height:{{ max(3, ($bulan->total / $maxBulanan) * 100) }}%"></div>
                            <span>{{ $bulan->label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <section class="panel">
        <h3>Data Penjualan</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Subtotal Produk</th>
                        <th>Ongkir</th>
                        <th>Total Pembayaran</th>
                        <th>Status Pesanan</th>
                        <th>Metode Pembayaran</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan as $row)
                        <tr>
                            <td>#{{ $row->id_pesanan }}</td>
                            <td>{{ $row->tanggal_pemesanan ? \Illuminate\Support\Carbon::parse($row->tanggal_pemesanan)->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $row->nama_pelanggan ?: 'Pelanggan tidak tersedia' }}</td>
                            <td>Rp{{ number_format($row->subtotal_produk ?? $row->total_harga ?? 0, 0, ',', '.') }}</td>
                            <td>{{ $row->ongkir === null ? 'Menunggu Admin' : 'Rp'.number_format($row->ongkir, 0, ',', '.') }}</td>
                            <td>{{ $row->ongkir === null ? 'Menunggu ongkir' : 'Rp'.number_format($row->total_harga ?? 0, 0, ',', '.') }}</td>
                            <td><span class="status status-{{ $row->status_pesanan }}">{{ ucfirst($row->status_pesanan ?? '-') }}</span></td>
                            <td>{{ $row->metode_pembayaran ?: '-' }}</td>
                            <td>{{ ucfirst($row->status_pembayaran ?? 'Belum dibayar') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="empty">Tidak ada penjualan pada rentang tanggal ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $laporan->links() }}</div>
    </section>
@endsection
