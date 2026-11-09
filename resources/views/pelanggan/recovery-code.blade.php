<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terbitkan Kode Pemulihan - Samudera Fresh</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 20px; background: #fffdf8; color: #292d29; font-family: Arial, sans-serif; }
        main { width: min(100%, 560px); padding: 38px; border: 1px solid #f0eee8; border-radius: 22px; background: #fff; box-shadow: 0 4px 18px rgba(32,89,54,.09); }
        h1 { margin: 0 0 12px; color: #245b3b; font-size: 25px; }
        p { color: #666; font-size: 14px; line-height: 1.6; }
        .notice { margin: 18px 0; padding: 13px 15px; border-radius: 8px; background: #f0f6f2; color: #506254; font-size: 13px; line-height: 1.6; }
        .message { margin: 16px 0; padding: 12px 14px; border-radius: 8px; font-size: 13px; line-height: 1.5; }
        .success { background: #eaf4ed; color: #245b3b; }
        .error { background: #fff0f2; color: #a3223d; }
        .recovery-code { margin: 16px 0; padding: 16px; border: 1px dashed #245b3b; border-radius: 8px; background: #fbfdfb; color: #245b3b; font-size: 20px; font-weight: 700; letter-spacing: 1px; text-align: center; overflow-wrap: anywhere; }
        button { width: 100%; min-height: 46px; margin-top: 8px; padding: 12px; border: 0; border-radius: 6px; background: #e93658; color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; }
        button:hover { background: #cf2f4c; }
        .back { display: block; margin-top: 20px; color: #d7425d; font-size: 13px; text-align: center; text-decoration: none; }
        .back:hover { text-decoration: underline; }
        @media (max-width: 560px) { main { padding: 28px 22px; } }
    </style>
</head>
<body>
    <main>
        <h1>Terbitkan Kode Pemulihan</h1>
        <p>{{ $pelanggan->nama_pelanggan }} ({{ $pelanggan->username }})</p>
        <div class="notice">Kode berlaku selama 30 hari dan hanya ditampilkan satu kali. Simpan kode ini di tempat aman. Jika diterbitkan ulang, kode sebelumnya tidak dapat digunakan lagi.</div>

        @if (session('success'))
            <div class="message success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="message error" role="alert">{{ session('error') }}</div>
        @endif
        @if (session('recovery_code'))
            <div class="recovery-code" aria-label="Kode pemulihan">{{ session('recovery_code') }}</div>
            <form action="{{ route('pelanggan.recovery-code.continue') }}" method="POST">
                @csrf
                <button type="submit">Selesai, lanjut ke Lupa Password</button>
            </form>
        @endif

        @unless (session('recovery_code'))
            <form action="{{ route('pelanggan.recovery-code.issue') }}" method="POST">
                @csrf
                <button type="submit">Terbitkan Kode Pemulihan</button>
            </form>
        @endunless
        <a class="back" href="{{ route('pelanggan.profile') }}">Kembali ke Profil</a>
    </main>
</body>
</html>
