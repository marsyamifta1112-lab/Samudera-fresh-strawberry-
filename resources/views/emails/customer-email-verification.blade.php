<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Email</title>
</head>
<body style="margin:0;padding:24px;background:#fffdf8;color:#292d29;font-family:Arial,sans-serif">
    <main style="max-width:560px;margin:0 auto;padding:28px;background:#fff;border:1px solid #eee9de;border-radius:12px">
        <h1 style="margin:0 0 16px;color:#245b3b;font-size:22px">Verifikasi email</h1>
        <p>Halo {{ $customerName }},</p>
        <p>Konfirmasi bahwa alamat email ini milik akun Samudera Fresh kamu.</p>
        <p><a href="{{ $verificationUrl }}" style="display:inline-block;padding:12px 18px;background:#df3d5b;color:#fff;text-decoration:none;border-radius:4px">Verifikasi Email</a></p>
        <p style="font-size:13px;color:#666">Tautan berlaku selama 60 menit dan hanya dapat digunakan dari email ini. Jika kamu tidak meminta verifikasi, abaikan pesan ini.</p>
    </main>
</body>
</html>
