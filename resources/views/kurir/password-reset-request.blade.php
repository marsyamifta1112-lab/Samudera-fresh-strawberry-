<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajukan Reset Password Kurir - Samudera Fresh</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;background:#fffdf8;color:#205936;font-family:Arial,sans-serif}
        header{height:85px;display:flex;align-items:center;padding:15px 5%}
        .brand{display:flex;align-items:center;gap:10px;color:#245b3b;font-size:14px;font-weight:bold;text-decoration:none}
        .logo{width:58px;height:55px;object-fit:contain}
        .brand small{display:block;margin-top:4px;color:#e93658}
        main{width:min(90%,480px);margin:55px auto;padding:38px;border-radius:22px;background:#fff;box-shadow:0 4px 18px rgba(32,89,54,.09)}
        h1{margin:0 0 10px;font-size:25px}
        p{color:#555;font-size:13px;line-height:1.6}
        .hint{margin:18px 0;padding:12px 14px;border-radius:8px;background:#f4f8f4;color:#45604d;font-size:12px;line-height:1.6}
        label{display:block;margin:20px 0 7px;color:#222;font-size:13px;font-weight:bold}
        input{width:100%;height:44px;padding:10px 12px;border:1px solid #ddd;border-radius:6px;font:inherit}
        input:focus{outline:2px solid #f9d6dd;border-color:#e93658}
        button{width:100%;height:44px;margin-top:20px;border:0;border-radius:6px;background:#e93658;color:white;font-weight:bold;cursor:pointer}
        button:hover{background:#d92f50}
        .message{margin:14px 0;color:#245b3b;font-size:13px;line-height:1.5}
        .error{color:#c92345}
        .back{display:block;margin-top:18px;color:#e93658;text-align:center;text-decoration:none;font-size:13px}
        @media(max-width:560px){main{margin:28px auto;padding:28px 22px}}
    </style>
</head>
<body>
    <header>
        <a href="{{ route('login') }}" class="brand">
            <img src="{{ asset('images/logo samudera.jpeg') }}" class="logo" alt="Samudera Fresh Strawberry">
            <span>SAMUDERA FRESH<small>STRAWBERRY</small></span>
        </a>
    </header>
    <main>
        <h1>Ajukan Reset Password</h1>
        <p>Formulir ini khusus untuk akun Kurir yang tidak dapat masuk.</p>
        <div class="hint">Masukkan username. Admin akan memverifikasi permintaan sebelum sistem menerbitkan password sementara. Form ini tidak mengirim email.</div>

        @if (session('success'))
            <div class="message" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="message error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('kurir.password-reset.submit') }}">
            @csrf
            <label for="username">Username Kurir</label>
            <input id="username" name="username" value="{{ old('username') }}" maxlength="50" autocomplete="username" required autofocus>
            <button type="submit">Kirim Permintaan</button>
        </form>
        <a class="back" href="{{ route('login') }}">Kembali ke Login</a>
    </main>
</body>
</html>
