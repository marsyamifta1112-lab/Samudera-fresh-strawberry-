@extends('admin.layout')
@section('title', 'Profil Admin')
@section('content')
    <div class="page-heading"><div><h2>Profil Admin</h2><p>Perbarui informasi akun petugas admin.</p></div></div>
    <section class="panel profile-panel">
        <h3>Profil Tersimpan</h3>
        <div class="detail-grid">
            <div><div class="detail-label">Nama</div><div class="detail-value">{{ $petugas->nama_petugas }}</div></div>
            <div><div class="detail-label">Username</div><div class="detail-value">{{ $petugas->username }}</div></div>
            <div><div class="detail-label">Nomor Telepon</div><div class="detail-value">{{ $petugas->no_telp ?: '-' }}</div></div>
            <div><div class="detail-label">Alamat</div><div class="detail-value">{{ $petugas->alamat ?: '-' }}</div></div>
        </div>
    </section>
    <form class="panel profile-panel" method="POST" action="{{ route('admin.profile.update') }}">@csrf @method('PUT')
        <div class="field"><label for="nama_petugas">Nama *</label><input id="nama_petugas" name="nama_petugas" value="{{ old('nama_petugas', $petugas->nama_petugas) }}" required maxlength="100"></div>
        <div class="field"><label for="username">Username *</label><input id="username" name="username" value="{{ old('username', $petugas->username) }}" required maxlength="50"></div>
        <div class="field"><label for="no_telp">Nomor Telepon</label><input id="no_telp" name="no_telp" value="{{ old('no_telp', $petugas->no_telp) }}" maxlength="15"></div>
        <div class="field"><label for="alamat">Alamat</label><textarea id="alamat" name="alamat" maxlength="1000">{{ old('alamat', $petugas->alamat) }}</textarea></div>
        <div class="field"><label for="password">Password Baru</label><input type="password" id="password" name="password" autocomplete="new-password" minlength="8"><small class="muted">Kosongkan jika tidak ingin mengganti password.</small></div>
        <div class="field"><label for="password_confirmation">Konfirmasi Password Baru</label><input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"></div>
        <button class="button" type="submit">Simpan Profil</button>
    </form>
@endsection
