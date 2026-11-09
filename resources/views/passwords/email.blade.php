<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Samudera Fresh Strawberry</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background: #fffdf8;
            color: #205936;
            font-family: Arial, sans-serif;
        }

        header {
            height: 85px;
            display: flex;
            align-items: center;
            padding: 15px 5%;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #245b3b;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
        }

        .logo {
            width: 58px;
            height: 55px;
            object-fit: contain;
        }

        .brand small {
            display: block;
            margin-top: 4px;
            color: #e93658;
        }

        main {
            width: min(90%, 520px);
            margin: 55px auto;
            padding: 40px;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 4px 18px rgba(32, 89, 54, .09);
        }

        h1 {
            margin: 0 0 10px;
            color: #205936;
            font-size: 27px;
        }

        .intro {
            margin: 0;
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }

        .hint {
            margin: 14px 0 24px;
            padding: 12px 14px;
            border-radius: 8px;
            background: #f4f8f4;
            color: #45604d;
            font-size: 12px;
            line-height: 1.6;
        }

        .field-help {
            display: block;
            margin-top: 8px;
            color: #666;
            font-size: 12px;
            line-height: 1.5;
        }

        label {
            display: block;
            margin: 18px 0 7px;
            color: #222;
            font-size: 13px;
            font-weight: bold;
        }

        input {
            width: 100%;
            height: 46px;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            color: #222;
            font: inherit;
        }

        input:focus {
            outline: 2px solid rgba(233, 54, 88, .18);
            border-color: #e93658;
        }

        button {
            width: 100%;
            height: 46px;
            margin-top: 24px;
            border: 0;
            border-radius: 6px;
            background: #e93658;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #d92f50;
        }

        .message {
            margin: 14px 0;
            color: #205936;
            font-size: 13px;
            line-height: 1.5;
        }

        .message.error {
            color: #e93658;
        }

        .back {
            display: block;
            margin-top: 20px;
            color: #e93658;
            font-size: 13px;
            text-align: center;
            text-decoration: none;
        }

        .back:hover {
            text-decoration: underline;
        }

        @media (max-width: 560px) {
            main {
                margin: 28px auto;
                padding: 28px 22px;
            }
        }
    </style>
</head>

<body>
    <header>
        <a href="{{ route('login') }}" class="brand">
            <img
                src="{{ asset('images/logo samudera.jpeg') }}"
                class="logo"
                alt="Samudera Fresh Strawberry"
            >
            <span>
                SAMUDERA FRESH
                <small>STRAWBERRY</small>
            </span>
        </a>
    </header>

    <main>
        <h1>Lupa Password?</h1>

        <p class="intro">
            Masukkan username dan kode pemulihan untuk membuat password baru.
        </p>

        <div class="hint">
            Kode pemulihan ditampilkan satu kali saat diterbitkan.
            Jika masih bisa masuk ke akun, kamu dapat membuat kode baru
            melalui menu Profil. Jika password dan kode sama-sama lupa,
            reset mandiri tidak dapat dilanjutkan karena kepemilikan akun
            perlu diverifikasi.
        </div>

        @if (session('success'))
            <div class="message" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="message error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @error('username')
            <div class="message error" role="alert">
                {{ $message }}
            </div>
        @enderror

        @error('recovery_code')
            <div class="message error" role="alert">
                {{ $message }}
            </div>
        @enderror

        <form method="POST" action="{{ route('password.verify') }}">
            @csrf

            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                value="{{ old('username', $username ?? request('username', '')) }}"
                autocomplete="username"
                maxlength="255"
                required
                autofocus
            >

            <label for="recovery_code">Kode Pemulihan</label>
            <input
                type="text"
                id="recovery_code"
                name="recovery_code"
                placeholder="Contoh: STB-ABCD-EFGH-JKLM"
                aria-describedby="recovery-code-help"
                autocomplete="off"
                autocapitalize="characters"
                spellcheck="false"
                maxlength="30"
                required
            >

            <span id="recovery-code-help" class="field-help">
                Masukkan kode persis seperti yang kamu simpan.
            </span>

            <button type="submit">Verifikasi</button>
        </form>

        <a class="back" href="{{ route('login') }}">
            Kembali ke Login
        </a>
    </main>
</body>
</html>