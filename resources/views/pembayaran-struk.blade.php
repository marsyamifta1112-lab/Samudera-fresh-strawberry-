<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $pembayaran->nomor_struk }} - Samudera Fresh Strawberry</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            background: #f4f4f0;
            color: #222;
            font-family: Arial, Helvetica, sans-serif;
        }

        .receipt {
            width: 90mm;
            max-width: 100%;
            margin: 0 auto;
            padding: 6mm 4mm;
            background: #fff;
            font-size: 8pt;
            line-height: 1.45;
        }

        .center {
            text-align: center;
        }

        .brand-logo {
            display: block;
            width: 22mm;
            height: 18mm;
            margin: 0 auto 2mm;
            object-fit: contain;
        }

        h1 {
            margin: 0;
            color: #205936;
            font-size: 8pt;
        }

        .subtitle {
            margin: 2px 0 12px;
            color: #555;
        }

        .store-address {
            margin-top: 2mm;
            overflow-wrap: anywhere;
        }

        .rule {
            margin: 10px 0;
            border: 0;
            border-top: 1px dashed #555;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .address {
            margin-top: 3px;
            overflow-wrap: anywhere;
        }

        .address-label {
            font-weight: bold;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
        }

        .items td {
            padding: 4px 0;
            vertical-align: top;
        }

        .items .quantity {
            width: 10mm;
            text-align: center;
        }

        .items .amount {
            text-align: right;
            white-space: nowrap;
        }

        .item-name {
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .muted {
            color: #666;
        }

        .total {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: 8pt;
            font-weight: bold;
        }

        .actions {
            width: 90mm;
            max-width: 100%;
            margin: 16px auto 0;
            text-align: center;
        }

        .button {
            display: inline-block;
            padding: 11px 18px;
            border: 0;
            border-radius: 5px;
            background: #e93658;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        /* Pengaturan ukuran kertas untuk PDF dan printer struk */
        @page {
            size: 90mm 200mm;
            margin: 0;
        }

        @media print {
            html,
            body {
                width: 90mm;
                margin: 0;
                padding: 0;
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .receipt {
                width: 90mm;
                max-width: none;
                margin: 0;
                padding: 4mm;
                background: #fff;
                font-size: 8pt;
                line-height: 1.4;
                overflow-wrap: anywhere;
            }

            .brand-logo {
                max-width: 22mm;
            }

            .actions {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <main class="receipt">
        <header class="center">
            <img
                class="brand-logo"
                src="{{ asset('images/logo samudera.jpeg') }}"
                alt="Logo Samudera Fresh Strawberry"
            >

            <h1>SAMUDERA FRESH</h1>
            <div class="subtitle">STRAWBERRY</div>
            <div>BUKTI PEMBAYARAN</div>
            <div class="store-address">Kp. Mekarbaru, RW 03 RT 02, Alamendah, Rancabali, Kabupaten Bandung</div>
        </header>

        <hr class="rule">

        <div class="meta">
            <span>No. Struk</span>
            <strong>{{ $pembayaran->nomor_struk }}</strong>
        </div>

        <div class="meta">
            <span>Tanggal verifikasi</span>
            <span>{{ \Illuminate\Support\Carbon::parse($pembayaran->tanggal_verifikasi)->format('d/m/Y H:i') }}</span>
        </div>

        <div class="meta">
            <span>No. Pesanan</span>
            <span>#{{ $pesanan->id_pesanan }}</span>
        </div>

        <div class="meta">
            <span>Pelanggan</span>
            <span>{{ $pelanggan->nama_pelanggan ?? '-' }}</span>
        </div>

        <div class="meta">
            <span>Penerima</span>
            <span>{{ $pesanan->nama_penerima ?: '-' }}</span>
        </div>

        <div class="meta">
            <span>Kontak</span>
            <span>{{ $pesanan->no_hp ?: '-' }}</span>
        </div>

        <div class="address">
            <span class="address-label">Alamat Pengiriman:</span>
            <span>{{ $alamatPengiriman ?: 'Tidak tersedia' }}</span>
        </div>

        <hr class="rule">

        <table class="items">
            <tbody>
                @forelse ($detailPesanan as $item)
                    <tr>
                        <td>
                            <div class="item-name">
                                {{ $item->nama_produk ?? 'Produk tidak tersedia' }}
                            </div>

                            <div class="muted">
                                Rp{{ number_format($item->harga, 0, ',', '.') }} / produk
                            </div>
                        </td>

                        <td class="quantity">
                            {{ $item->jumlah }}
                        </td>

                        <td class="amount">
                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="muted">
                            Rincian produk tidak tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <hr class="rule">

        <div class="meta">
            <span>Wilayah tujuan</span>
            <span>{{ $pesanan->wilayah_ongkir ?: '-' }}</span>
        </div>
        <div class="meta">
            <span>Subtotal produk</span>
            <span>Rp{{ number_format($pesanan->subtotal_produk, 0, ',', '.') }}</span>
        </div>
        <div class="meta">
            <span>Ongkir</span>
            <span>Rp{{ number_format($pesanan->ongkir ?? 0, 0, ',', '.') }}</span>
        </div>
        <div class="meta">
            <span>Metode transfer</span>
            <span>{{ $pembayaran->metode_pembayaran ?: '-' }}</span>
        </div>

        <div class="meta">
            <span>Status</span>
            <strong>TERVERIFIKASI</strong>
        </div>

        <div class="total">
            <span>TOTAL DIBAYAR</span>
            <span>Rp{{ number_format($pembayaran->jumlah_bayar ?? $pesanan->total_harga, 0, ',', '.') }}</span>
        </div>

        <hr class="rule">

        <p class="center">
            Terima kasih telah berbelanja<br>
            di Samudera Fresh Strawberry
        </p>
    </main>

    <div class="actions">
        <button
            class="button"
            type="button"
            onclick="window.print()"
        >
            Cetak Struk
        </button>

        <a class="button" href="{{ $backUrl }}">
            Kembali
        </a>
    </div>
</body>
</html>