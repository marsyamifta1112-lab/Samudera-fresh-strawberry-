<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemulihan Password Admin - Samudera Fresh Strawberry</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            font-family: Arial, sans-serif;
            background: #fffdf8;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 625px;
            padding: 44px 48px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 30px rgba(32, 89, 54, 0.09);
        }
        h1 { margin: 0 0 12px; color: #205936; font-size: 30px; }
        p { color: #5d665f; line-height: 1.6; }
        .info, .message {
            margin: 20px 0;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.6;
        }
        .info { background: #f0f7f2; color: #536b5a; }
        .message { background: #fff0f0; color: #a32020; }
        .success { background: #edf8ef; color: #205936; }
        label { display: block; margin: 20px 0 8px; font-weight: 700; }
        input {
            width: 100%;
            min-height: 52px;
            padding: 13px;
            border: 1px solid #dedede;
            border-radius: 9px;
            font: inherit;
        }
        button {
            width: 100%;
            min-height: 52px;
            margin-top: 22px;
            padding: 13px;
            border: 0;
            border-radius: 9px;
            background: #e93658;
            color: white;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        .back { display: block; margin-top: 22px; color: #c94b66; text-align: center; }
        @media (max-width: 600px) { .container { padding: 30px 24px; } }
    </style>
</head>
<body>
    <main class="container">
        <h1>Pemulihan Password Admin</h1>
        <p>Masukkan username akun Admin. Permintaan hanya dapat disetujui oleh Admin lain yang masih dapat masuk.</p>

        <div class="info">
            @if ($pending)
                Permintaan untuk akun <strong>{{ $requestedUsername }}</strong> sedang menunggu verifikasi Admin lain. Setelah disetujui dan Admin lain logout, buka kembali halaman ini di browser yang sama untuk membuat password baru.
            @else
                Setelah diajukan, Admin lain dapat menyetujui atau menolak permintaan melalui menu Petugas. Permintaan tetap tersimpan di browser ini meskipun Admin lain login dan logout. Jika kedua Admin tidak dapat masuk, hubungi developer untuk pemulihan.
            @endif
        </div>

        @if (session('error'))
            <div class="message" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="message" role="alert">{{ $errors->first() }}</div>
        @endif
        @if (session('success'))
            <div class="message success" role="status">{{ session('success') }}</div>
        @endif

        @unless ($pending)
            <form action="{{ route('admin.password.verify') }}" method="POST">
                @csrf
                <label for="username">Username Admin</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="{{ old('username') }}"
                    placeholder="Masukkan username Admin"
                    autocomplete="username"
                    maxlength="50"
                    required
                >
                <button type="submit">Ajukan Pemulihan</button>
            </form>
        @endunless

        <a href="{{ route('login') }}" class="back">Kembali ke Login</a>
    </main>
</body>
</html>
