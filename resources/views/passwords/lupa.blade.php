<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Samudera Fresh Strawberry</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fffdf8;
            margin: 0;
            padding: 24px 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .card { background: #fff; padding: 34px 36px; border-radius: 22px; box-shadow: 0 4px 18px rgba(28,54,37,.08); width: min(100%,532px); }
        h1 { color: #245b3b; margin: 0 0 10px; font-size: 27px; }
        p { color: #666; font-size: 14px; line-height: 1.5; margin-bottom: 20px; }
        .info-box { background: #f0f6f2; padding: 12px 14px; border-radius: 6px; font-size: 12px; color: #506254; margin-bottom: 20px; line-height: 1.55; }
        .alert-error { background: #ffe6e6; color: #d9534f; padding: 10px 15px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        label { font-weight: 600; display: block; margin-bottom: 6px; color: #333; font-size: 14px; }
        .form-control { width: 100%; padding: 11px 14px; border: 1px solid #e2e2df; border-radius: 6px; font-size: 14px; min-height: 46px; }
        .form-control:focus { border-color: #245b3b; outline: none; }
        small { color: #888; font-size: 12px; margin-top: 4px; display: block; }
        .btn-verifikasi { width: 100%; background: #e93658; color: #fff; border: 0; padding: 12px; border-radius: 5px; font-weight: 700; font-size: 15px; cursor: pointer; }
        .btn-verifikasi:hover { background: #cf2f4c; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #d7425d; text-decoration: none; font-size: 14px; font-weight: 500; }
        .back-link a:hover { text-decoration: underline; }
        @media (max-width: 520px) { .card { padding: 27px 22px; border-radius: 16px; } h1 { font-size: 24px; } }
    </style>
</head>
<body>
    <main class="card">
        <h1>Lupa Password?</h1>
        <p>Masukkan username dan kode pemulihan yang sudah diterbitkan dari Profil.</p>
        <div class="info-box">Kode berlaku selama 30 hari dan hanya dapat digunakan satu kali. Jika belum memiliki kode, login ke akun terlebih dahulu lalu terbitkan kode dari Profil.</div>

        @if (session('error'))
            <div class="alert-error" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert-error" role="alert">{{ $errors->first() }}</div>
        @endif
        @if (session('info'))
            <div class="info-box" role="status">{{ session('info') }}</div>
        @endif

        <form action="{{ route('password.verify') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" class="form-control" placeholder="Masukkan username akunmu" autocomplete="username" maxlength="50" required value="{{ old('username') }}">
            </div>
            <div class="form-group">
                <label for="recovery_code">Kode Pemulihan</label>
                <input id="recovery_code" type="text" name="recovery_code" class="form-control" placeholder="Contoh: STB-ABCD-EFGH-JKLM" autocomplete="off" autocapitalize="characters" spellcheck="false" maxlength="30" required>
                <small>Masukkan kode yang kamu simpan saat menerbitkannya dari Profil.</small>
            </div>
            <button type="submit" class="btn-verifikasi">Verifikasi Kode</button>
        </form>

        <div class="back-link"><a href="{{ route('login') }}">Kembali ke Login</a></div>
    </main>
</body>
</html>
