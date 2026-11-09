@extends('admin.layout')
@section('title', $produk ? 'Edit Produk' : 'Tambah Produk')
@section('content')
    <div class="page-heading"><div><h2>{{ $produk ? 'Edit Produk' : 'Tambah Produk' }}</h2></div></div>
    <form id="product-form" class="panel detail-card" method="POST" enctype="multipart/form-data" action="{{ $produk ? route('admin.products.update', $produk->id_produk) : route('admin.products.store') }}">
        @csrf @if ($produk) @method('PUT') @endif
        <h3>{{ $produk ? 'Edit Produk' : 'Tambah Produk' }}</h3>
        <div class="form-grid">
            <div class="field"><label for="nama_produk">Nama Produk</label><input id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" required maxlength="100"></div>
            <div class="field"><label for="kategori">Kategori</label>
                @php
                    $namaAwal = strtolower(old('nama_produk', $produk->nama_produk ?? ''));
                    $kategoriAwal = str_contains($namaAwal, 'keripik') ? 'Keripik' : (str_contains($namaAwal, 'selai') ? 'Selai' : (str_contains($namaAwal, 'segar') ? 'Segar' : 'Olahan'));
                @endphp
                <input id="kategori" value="{{ $kategoriAwal }}" readonly aria-label="Kategori otomatis dari nama produk">
            </div>
            <div class="field"><label for="harga_produk">Harga</label><input type="number" id="harga_produk" name="harga_produk" value="{{ old('harga_produk', $produk->harga_produk ?? '') }}" min="0" step="0.01" required></div>
            <div class="field"><label for="stok_produk">Stok</label><input type="number" id="stok_produk" name="stok_produk" value="{{ old('stok_produk', $produk->stok_produk ?? 0) }}" min="0" required></div>
            <div class="field photo-field"><label for="foto_produk">Foto Produk</label><input type="file" id="foto_produk" name="foto_produk" accept="image/jpeg,image/png,image/webp">
                <img class="product-preview" id="preview_produk" src="{{ $produk && $produk->foto_produk ? asset('images/'.$produk->foto_produk) : asset('images/strawberry.jpeg') }}" alt="Pratinjau foto produk">
            </div>
            <div class="field"><label for="deskripsi">Deskripsi</label><textarea id="deskripsi" name="deskripsi" maxlength="5000">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea></div>
        </div>
        <div class="form-submit">
            @if ($produk)
                <button class="button button-outline" type="submit" form="delete-product-form">Hapus Produk</button>
            @endif
            <button class="button" type="submit">{{ $produk ? 'Simpan Perubahan' : 'Simpan' }}</button>
        </div>
    </form>
    @if ($produk)
        <form id="delete-product-form" method="POST" action="{{ route('admin.products.destroy', $produk->id_produk) }}" onsubmit="return confirm('Hapus produk ini? Produk yang sudah ada di riwayat pesanan tidak dapat dihapus.')">@csrf @method('DELETE')</form>
    @endif
    <style>
        .product-preview{display:block;width:min(100%,390px);height:110px;margin-top:8px;object-fit:cover;border-radius:4px;background:#f5eee4}
        .form-submit{display:flex;justify-content:flex-end;align-items:center;gap:8px;margin-top:8px}.photo-field input{font-size:9px}
    </style>
    <script>
        const nameInput = document.getElementById('nama_produk');
        const categoryInput = document.getElementById('kategori');
        const photoInput = document.getElementById('foto_produk');
        const preview = document.getElementById('preview_produk');

        nameInput.addEventListener('input', () => {
            const name = nameInput.value.toLowerCase();
            categoryInput.value = name.includes('keripik') ? 'Keripik' : name.includes('selai') ? 'Selai' : name.includes('segar') ? 'Segar' : 'Olahan';
        });
        photoInput.addEventListener('change', () => {
            if (photoInput.files[0]) preview.src = URL.createObjectURL(photoInput.files[0]);
        });
    </script>
@endsection
