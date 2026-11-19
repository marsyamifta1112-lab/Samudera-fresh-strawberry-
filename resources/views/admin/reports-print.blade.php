<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Transaksi Penjualan</title>
    <style>
        body { margin: 24px; color: #242923; font-family: Arial, sans-serif; }
        .print-actions { margin-bottom: 18px; }
        button { padding: 9px 14px; cursor: pointer; }
        h1 { margin: 0 0 6px; font-size: 20px; }
        .period { margin: 0 0 18px; color: #555; font-size: 13px; }
        .table-wrap { width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 7px; border: 1px solid #bbb; text-align: left; vertical-align: top; font-size: 10px; }
        th { background: #eee; }
        @media print {
            @page { size: landscape; margin: 12mm; }
            body { margin: 0; }
            .print-actions { display: none; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>
    <h1>Laporan Transaksi Penjualan</h1>
    <p class="period">Periode {{ \Illuminate\Support\Carbon::parse($from)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($to)->format('d/m/Y') }} · {{ $transaksi->count() }} pesanan</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Rincian Produk</th>
                    <th>Subtotal Produk</th>
                    <th>Ongkir</th>
                    <th>Total Pembayaran</th>
                    <th>Status Pesanan</th>
                    <th>Metode Pembayaran</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksi as $row)
                    <tr>
                        <td>#{{ $row->id_pesanan }}</td>
                        <td>{{ $row->tanggal_pemesanan ? \Illuminate\Support\Carbon::parse($row->tanggal_pemesanan)->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $row->nama_pelanggan ?: 'Pelanggan tidak tersedia' }}</td>
                        <td>{{ $row->ringkasan_produk ?: '-' }}</td>
                        <td>Rp{{ number_format($row->subtotal_produk ?? $row->total_harga ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $row->ongkir === null ? 'Menunggu Admin' : 'Rp'.number_format($row->ongkir, 0, ',', '.') }}</td>
                        <td>{{ $row->ongkir === null ? 'Menunggu ongkir' : 'Rp'.number_format($row->total_harga ?? 0, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($row->status_pesanan ?? '-') }}</td>
                        <td>{{ $row->metode_pembayaran ?: '-' }}</td>
                        <td>{{ ucfirst($row->status_pembayaran ?? 'Belum dibayar') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10">Tidak ada transaksi pada rentang tanggal ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
