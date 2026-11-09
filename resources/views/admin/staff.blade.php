@extends('admin.layout')
@section('title', 'Petugas')
@section('content')
    <div class="page-heading">
        <div>
            <h2>Petugas</h2>
            <p>Daftar akun petugas yang terdaftar pada sistem.</p>
        </div>
        <a class="button" href="#tambah-petugas">+ Tambah Petugas</a>
    </div>
    @if (session('courier_temporary_password'))
        <section class="panel" role="status">
            <h3>Password sementara kurir</h3>
            <p class="muted">{{ session('success') }}</p>
            <p><strong>{{ session('courier_temporary_password') }}</strong></p>
            <p class="muted">Catat dan berikan langsung kepada kurir. Password ini hanya ditampilkan sekali; kurir wajib menggantinya saat login.</p>
        </section>
    @endif
    @if (session('error'))
        <div class="panel" role="alert">{{ session('error') }}</div>
    @endif
    @if ($adminRecoveryRequests->isNotEmpty())
        <section class="panel">
            <h3>Permintaan Pemulihan Password Admin</h3>
            <p class="muted">Hanya Admin lain yang dapat menyetujui permintaan. Persetujuan memberi pemohon waktu 30 menit untuk membuat password baru. Setelah menyetujui, logout agar pemohon dapat melanjutkan dari browser yang sama.</p>
            @foreach ($adminRecoveryRequests as $recoveryRequest)
                <div class="actions">
                    <span>
                        {{ $recoveryRequest->nama_petugas ?: $recoveryRequest->username }}
                        ({{ $recoveryRequest->username }}) -
                        {{ \Illuminate\Support\Carbon::parse($recoveryRequest->requested_at)->format('d/m/Y H:i') }}
                    </span>
                    <form method="POST" action="{{ route('admin.password-recovery.approve', $recoveryRequest->id) }}">
                        @csrf
                        @method('PUT')
                        <button class="button button-small" type="submit">Setujui Pemulihan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.password-recovery.reject', $recoveryRequest->id) }}">
                        @csrf
                        @method('PUT')
                        <button class="button button-outline button-small" type="submit">Tolak</button>
                    </form>
                </div>
            @endforeach
        </section>
    @endif
    <section class="panel profile-panel" id="tambah-petugas">
        <h3>Tambah Petugas Baru</h3>
        <form method="POST" action="{{ route('admin.staff.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label for="nama_petugas">Nama Petugas *</label>
                    <input id="nama_petugas" name="nama_petugas" value="{{ old('nama_petugas') }}" required maxlength="100">
                </div>
                <div class="field">
                    <label for="username">Username *</label>
                    <input id="username" name="username" value="{{ old('username') }}" required maxlength="50" autocomplete="username">
                </div>
                <div class="field">
                    <label for="password">Password *</label>
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password">
                </div>
                <div class="field">
                    <label for="password_confirmation">Konfirmasi Password *</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
                </div>
                <div class="field">
                    <label for="no_telp">Nomor Telepon</label>
                    <input id="no_telp" name="no_telp" value="{{ old('no_telp') }}" maxlength="15">
                </div>
                <div class="field">
                    <label for="role">Role *</label>
                    <select id="role" name="role" required>
                        <option value="">Pilih role</option>
                        <option value="Admin" @selected(old('role') === 'Admin')>Admin</option>
                        <option value="Kurir" @selected(old('role') === 'Kurir')>Kurir</option>
                    </select>
                </div>
                <div class="field">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" maxlength="1000">{{ old('alamat') }}</textarea>
                </div>
            </div>
            <button class="button" type="submit">Simpan Petugas</button>
        </form>
    </section>
    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Nomor Telepon</th>
                        <th>Alamat</th>
                        <th>Role</th>
                        <th>Status Akun</th>
                        <th>Permintaan Reset</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($petugas as $person)
                        <tr>
                            <td>{{ $person->id_petugas }}</td>
                            <td>{{ $person->nama_petugas ?: '-' }}</td>
                            <td>{{ $person->username ?: '-' }}</td>
                            <td>{{ $person->no_telp ?: '-' }}</td>
                            <td>{{ $person->alamat ?: '-' }}</td>
                            <td><span class="status">{{ $person->role ?: '-' }}</span></td>
                            <td><span class="status">{{ $person->status_akun }}</span></td>
                            <td>
                                @if ($person->role === 'Kurir' && $person->password_reset_status)
                                    <span class="status">{{ $person->password_reset_status }}</span>
                                    @if ($person->password_reset_requested_at)
                                        <br><small>{{ \Illuminate\Support\Carbon::parse($person->password_reset_requested_at)->format('d/m/Y H:i') }}</small>
                                    @endif
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    @if ($person->role === 'Kurir' && $person->status_akun === 'Menunggu Persetujuan')
                                        <form method="POST" action="{{ route('admin.staff.approve', $person->id_petugas) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="button button-small" type="submit">Setujui Akun</button>
                                        </form>
                                    @endif
                                    @if ($person->role === 'Kurir' && $person->password_reset_status === 'Menunggu Verifikasi')
                                        <form method="POST" action="{{ route('admin.staff.password-reset.approve', $person->id_petugas) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="button button-small" type="submit">Verifikasi &amp; Setujui Reset</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.staff.password-reset.reject', $person->id_petugas) }}">
                                            @csrf
                                            @method('PUT')
                                            <button class="button button-outline button-small" type="submit">Tolak</button>
                                        </form>
                                    @endif
                                    @if ($person->role !== 'Kurir' || ($person->status_akun !== 'Menunggu Persetujuan' && $person->password_reset_status !== 'Menunggu Verifikasi'))
                                        @if ($person->role !== 'Admin')
                                            <span class="muted">-</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="empty">Belum ada data petugas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $petugas->links() }}</div>
    </section>
@endsection
