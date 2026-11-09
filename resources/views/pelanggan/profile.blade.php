<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Pelanggan - Samudera Fresh</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #fffdf8; color: #292d29; font-family: Arial, sans-serif; }
        .profile-page { width: min(100% - 32px, 920px); min-height: calc(100vh - 40px); margin: 20px auto; padding: 18px 28px 26px; background: #fff; border: 1px solid #f0eee8; border-radius: 22px; }
        .profile-heading { display: flex; align-items: center; gap: 14px; margin-bottom: 26px; }
        .avatar { width: 76px; height: 76px; flex: 0 0 76px; display: grid; place-items: center; border-radius: 13px; background: #fff0f3; color: #df3d5b; font-size: 28px; font-weight: 700; }
        .profile-heading h1 { margin: 0; color: #245b3b; font-size: 17px; }
        .profile-heading p { margin: 5px 0 0; color: #777; font-size: 12px; }
        .recovery-link { display: inline-block; margin-top: 10px; color: #d7425d; font-size: 12px; font-weight: 700; text-decoration: none; }
        .recovery-link:hover { text-decoration: underline; }
        .notice { width: min(100%, 575px); margin: 0 auto 12px; padding: 10px 13px; border-radius: 5px; font-size: 12px; line-height: 1.5; }
        .notice-success { background: #eaf4ed; color: #245b3b; }
        .notice-info { background: #eef4f0; color: #345a45; }
        .notice-error { background: #fff0f2; color: #a3223d; }
        .profile-sections { width: min(100%, 575px); margin: 0 auto; }
        .profile-section { margin-bottom: 6px; }
        .profile-section summary { min-height: 40px; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 13px; background: #f7f6f4; color: #3f403d; font-size: 12px; font-weight: 700; cursor: pointer; list-style: none; }
        .profile-section summary::-webkit-details-marker { display: none; }
        .profile-section summary::after { content: '+'; color: #777; font-size: 16px; font-weight: 400; }
        .profile-section[open] summary::after { content: '\2212'; }
        .section-content { padding: 13px 14px 17px; color: #666; font-size: 12px; line-height: 1.6; }
        .section-content p { margin: 5px 0 10px; }
        .data-list { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
        .data-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding-bottom: 8px; border-bottom: 1px solid #efeee9; }
        .data-row:last-child { padding-bottom: 0; border-bottom: 0; }
        .data-row strong { color: #3d423e; font-weight: 600; }
        .empty-state { margin: 0; color: #777; }
        .settings-form { display: grid; gap: 12px; }
        .settings-field label { display: block; margin-bottom: 5px; color: #3d423e; font-size: 11px; font-weight: 700; }
        .settings-field input { width: 100%; min-height: 38px; padding: 9px 10px; border: 1px solid #e2e2df; border-radius: 4px; background: #fff; font-size: 12px; }
        .settings-field input:focus { outline: 2px solid #dcebe0; border-color: #245b3b; }
        .button { min-height: 38px; padding: 9px 15px; border: 0; border-radius: 4px; background: #df3d5b; color: #fff; font-size: 12px; font-weight: 700; cursor: pointer; }
        .button:hover { background: #c8324e; }
        .continue-link { display: inline-block; margin-left: 12px; color: #d7425d; font-size: 12px; text-decoration: none; }
        .continue-link:hover { text-decoration: underline; }
        .logout-button { border: 0; padding: 0; background: transparent; color: #df3d5b; font-size: 12px; cursor: pointer; }
        @media (max-width: 600px) {
            .profile-page { width: 100%; min-height: 100vh; margin: 0; padding: 18px 16px 28px; border: 0; border-radius: 0; }
            .avatar { width: 60px; height: 60px; flex-basis: 60px; font-size: 23px; }
            .profile-heading { margin-bottom: 22px; }
            .profile-section summary { min-height: 42px; }
            .data-row { flex-direction: column; gap: 2px; }
        }
    </style>
</head>
<body>
    <main class="profile-page">
        <header class="profile-heading">
            <div class="avatar" aria-hidden="true">{{ strtoupper(substr($pelanggan->nama_pelanggan ?? 'P', 0, 1)) }}</div>
            <div>
                <h1>{{ $pelanggan->nama_pelanggan }}</h1>
                <p>{{ $pelanggan->username }}</p>
                <a class="recovery-link" href="{{ route('pelanggan.recovery-code') }}">Terbitkan Kode Pemulihan</a>
            </div>
        </header>

        @if (session('success'))
            <div class="notice notice-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="notice notice-info" role="status">{{ session('info') }}</div>
        @endif
        @if (session('error'))
            <div class="notice notice-error" role="alert">{{ session('error') }}</div>
        @endif

        <div class="profile-sections">
            <details class="profile-section">
                <summary>Pesanan Saya</summary>
                <div class="section-content">
                    @forelse ($pesanan as $order)
                        <div class="data-row">
                            <strong>Pesanan #{{ $order->id_pesanan }}</strong>
                            <span>{{ $order->status_pesanan ?? 'Diproses' }}@if ($order->tanggal_pemesanan) · {{ \Illuminate\Support\Carbon::parse($order->tanggal_pemesanan)->format('d/m/Y') }}@endif</span>
                        </div>
                    @empty
                        <p class="empty-state">Belum ada pesanan.</p>
                    @endforelse
                </div>
            </details>

            <details class="profile-section">
                <summary>Alamat Saya</summary>
                <div class="section-content">
                    @php($alamatTerakhir = $pesanan->first()->alamat_pengiriman ?? null)
                    <p class="empty-state">{{ $alamatTerakhir ?: 'Alamat pengiriman akan muncul setelah kamu membuat pesanan.' }}</p>
                </div>
            </details>

            <details class="profile-section">
                <summary>Metode Pembayaran</summary>
                <div class="section-content">
                    @forelse ($pembayaran as $payment)
                        <div class="data-row">
                            <strong>Pesanan #{{ $payment->id_pesanan }}</strong>
                            <span>{{ $payment->metode_pembayaran ?: 'Belum dipilih' }}</span>
                        </div>
                    @empty
                        <p class="empty-state">Belum ada metode pembayaran tersimpan.</p>
                    @endforelse
                </div>
            </details>

            <details class="profile-section">
                <summary>Pengaturan Akun</summary>
                <div class="section-content">
                    <form class="settings-form" action="{{ route('pelanggan.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="settings-field">
                            <label for="nama_pelanggan">Nama</label>
                            <input id="nama_pelanggan" name="nama_pelanggan" value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}" required maxlength="100">
                        </div>
                        <div class="settings-field">
                            <label for="username">Username</label>
                            <input id="username" name="username" value="{{ old('username', $pelanggan->username) }}" autocomplete="username" required maxlength="50">
                        </div>
                        <div class="settings-field">
                            <label for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $pelanggan->email) }}" autocomplete="email" maxlength="100">
                        </div>
                        @if ($pelanggan->email && ! $pelanggan->email_verified_at)
                            <p class="empty-state">Email belum diverifikasi.</p>
                            <button class="button" type="submit" form="email-verification-form">Kirim Ulang Verifikasi Email</button>
                        @elseif ($pelanggan->email_verified_at)
                            <p class="empty-state">Email terverifikasi.</p>
                        @endif
                        <div class="settings-field">
                            <label for="current_password">Password untuk mengonfirmasi perubahan password</label>
                            <input id="current_password" type="password" name="current_password" autocomplete="current-password">
                        </div>
                        <div class="settings-field">
                            <label for="password">Password baru</label>
                            <input id="password" type="password" name="password" autocomplete="new-password" minlength="8">
                        </div>
                        <div class="settings-field">
                            <label for="password_confirmation">Konfirmasi password baru</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8">
                        </div>
                        <button class="button" type="submit">Simpan Pengaturan Akun</button>
                    </form>
                    @if ($pelanggan->email && ! $pelanggan->email_verified_at)
                        <form id="email-verification-form" action="{{ route('pelanggan.email.verify.send') }}" method="POST">
                            @csrf
                        </form>
                    @endif
                </div>
            </details>

            <details class="profile-section">
                <summary>Keluar</summary>
                <div class="section-content">
                    <form action="{{ route('pelanggan.logout') }}" method="POST">
                        @csrf
                        <button class="logout-button" type="submit">Keluar dari akun</button>
                    </form>
                </div>
            </details>
        </div>
    </main>
</body>
</html>