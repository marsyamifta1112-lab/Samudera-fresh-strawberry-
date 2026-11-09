@extends('admin.layout')
@section('title', 'Tarif Ongkir')
@section('content')
    <div class="page-heading">
        <div>
            <h2>Tarif Ongkir per Wilayah</h2>
            <p>Tarif akan dipakai untuk pesanan baru. Ongkir pesanan yang sudah dibuat tidak berubah otomatis.</p>
        </div>
    </div>
    <section class="panel">
        <form method="POST" action="{{ route('admin.shipping-rates.update') }}">
            @csrf
            @method('PUT')
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Wilayah tujuan</th><th>Tarif ongkir (Rp)</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($wilayahOngkir as $wilayah)
                            <tr>
                                <td>{{ $wilayah->nama_wilayah }}</td>
                                <td>
                                    <input
                                        name="tarif[{{ $wilayah->id }}]"
                                        type="text"
                                        inputmode="numeric"
                                        pattern="[0-9.]*"
                                        maxlength="11"
                                        value="{{ number_format($wilayah->tarif, 0, ',', '.') }}"
                                        aria-label="Tarif {{ $wilayah->nama_wilayah }}"
                                        required
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="button">Simpan Tarif Wilayah</button>
        </form>
        <p class="muted">Tarif awal merupakan perkiraan. Sesuaikan dengan jangkauan pengiriman dan biaya operasional Samudera Fresh.</p>
    </section>
    <style>
        .table-wrap input{width:180px;max-width:100%;border:1px solid var(--line);border-radius:4px;padding:9px 10px}
        .panel form>.button{margin-top:16px}
        .muted{margin-top:12px;font-size:11px}
    </style>
@endsection
