<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kurir - Samudera Fresh Strawberry</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial,sans-serif}
        body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#fffaf7;padding:24px}
        .register-card{display:grid;grid-template-columns:.9fr 1.1fr;width:100%;max-width:900px;background:#fff;border:1px solid #eee8e2;border-radius:28px;overflow:hidden}
        .register-image{min-height:550px}
        .register-image img{width:100%;height:100%;object-fit:cover;display:block}
        .register-form{padding:42px;display:flex;flex-direction:column;justify-content:center}
        h1{color:#20583d;font-size:28px;margin-bottom:10px}
        .intro{color:#555;font-size:14px;line-height:1.6;margin-bottom:20px}
        .notice,.error{padding:11px 13px;border-radius:6px;margin-bottom:14px;font-size:13px}
        .notice{background:#fff4df;color:#805b12}.error{background:#fde8ec;color:#a3223d}
        .field{margin-bottom:13px}
        label{display:block;margin-bottom:6px;color:#333;font-size:13px;font-weight:bold}
        input,textarea{width:100%;padding:11px 12px;border:1px solid #dedede;border-radius:6px;font-size:14px}
        textarea{min-height:70px;resize:vertical}
        input:focus,textarea:focus{outline:1px solid #20583d}
        button{width:100%;padding:13px;border:0;border-radius:6px;background:#df3153;color:#fff;font-size:14px;font-weight:bold;cursor:pointer}
        .login-link{text-align:center;margin-top:17px;color:#555;font-size:13px}
        .login-link a{color:#df3153;font-weight:bold;text-decoration:none}
        @media(max-width:700px){.register-card{grid-template-columns:1fr;max-width:480px}.register-image{display:none}.register-form{padding:30px 24px}}
    </style>
</head>
<body>
    <main class="register-card">
        <div class="register-image">
            <img src="{{ asset('images/keripik.jpg') }}" alt="Olahan stroberi Samudera Fresh Strawberry">
        </div>
        <section class="register-form">
            <h1>Daftar Akun Kurir</h1>
            <p class="intro">Kirim permohonan akun Kurir. Akun baru dapat digunakan setelah disetujui oleh Admin.</p>
            <div class="notice">Permohonan akan ditinjau oleh Admin sebelum akun diaktifkan.</div>
            @if ($errors->any())
                <div class="error" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('kurir.register.process') }}">
                @csrf
                <div class="field">
                    <label for="nama_petugas">Nama Lengkap *</label>
                    <input id="nama_petugas" name="nama_petugas" value="{{ old('nama_petugas') }}" maxlength="100" required>
                </div>
                <div class="field">
                    <label for="username">Username *</label>
                    <input id="username" name="username" value="{{ old('username') }}" maxlength="50" autocomplete="username" required>
                </div>
                <div class="field">
                    <label for="password">Password *</label>
                    <input id="password" type="password" name="password" minlength="8" autocomplete="new-password" required>
                </div>
                <div class="field">
                    <label for="password_confirmation">Konfirmasi Password *</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required>
                </div>
                <div class="field">
                    <label for="no_telp">Nomor Telepon</label>
                    <input id="no_telp" name="no_telp" value="{{ old('no_telp') }}" maxlength="15">
                </div>
                <div class="field">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" maxlength="1000">{{ old('alamat') }}</textarea>
                </div>
                <button type="submit">Kirim Permohonan</button>
            </form>
            <p class="login-link">Sudah punya akun? <a href="{{ route('kurir.login') }}">Login di sini</a></p>
        </section>
    </main>
</body>
</html>
