<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode Pemulihan Password</title>
</head>
<body style="margin:0;padding:24px;background:#fffdf8;color:#292d29;font-family:Arial,sans-serif">
    <main style="max-width:560px;margin:0 auto;padding:28px;background:#fff;border:1px solid #eee9de;border-radius:12px">
        <h1 style="margin:0 0 16px;color:#245b3b;font-size:22px">Kode Pemulihan Password</h1>
        <p>Halo {{ $customerName }},</p>
        <p>Gunakan kode ini bersama username akunmu untuk membuat password baru:</p>
        <p style="padding:14px;background:#fff3f5;color:#bf2948;font-size:22px;font-weight:bold;letter-spacing:2px;text-align:center">{{ $recoveryCode }}</p>
        <p style="font-size:13px;color:#666">Kode berlaku 15 menit dan hanya dapat digunakan satu kali. Jika kamu tidak meminta kode ini, abaikan pesan ini dan jangan bagikan kode kepada siapa pun.</p>
    </main>
</body>
</html>
