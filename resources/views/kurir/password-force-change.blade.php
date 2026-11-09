<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ganti Password Sementara - Samudera Fresh</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;background:#fffdf8;color:#205936;font-family:Arial,sans-serif}
        main{width:min(90%,480px);margin:10vh auto;padding:38px;border-radius:22px;background:#fff;box-shadow:0 4px 18px rgba(32,89,54,.09)}
        h1{margin:0 0 10px;font-size:25px}
        p{color:#555;font-size:13px;line-height:1.6}
        label{display:block;margin:20px 0 7px;color:#222;font-size:13px;font-weight:bold}
        .password-wrapper{position:relative}
        .password-wrapper input{width:100%;height:44px;padding:10px 48px 10px 12px;border:1px solid #ddd;border-radius:6px;font:inherit}
        .toggle-password{position:absolute;top:50%;right:10px;transform:translateY(-50%);display:grid;place-items:center;width:32px;height:32px;margin:0;padding:0;border:0;background:transparent;color:#777;cursor:pointer}
        .toggle-password:hover{color:#205936}
        .toggle-password:focus-visible{outline:2px solid #205936;outline-offset:2px;border-radius:4px}
        .toggle-password svg{width:21px;height:21px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
        button{width:100%;height:44px;margin-top:22px;border:0;border-radius:6px;background:#e93658;color:#fff;font-weight:bold;cursor:pointer}
        .error{margin:12px 0;color:#c92345;font-size:13px}
        @media(max-width:560px){main{padding:28px 22px}}
    </style>
</head>
<body>
    <main>
        <h1>Buat Password Baru</h1>
        <p>Password sementara hanya untuk satu kali masuk. Buat password pribadi baru sebelum melanjutkan ke halaman Kurir.</p>
        @foreach ($errors->all() as $error)
            <div class="error" role="alert">{{ $error }}</div>
        @endforeach
        <form method="POST" action="{{ route('kurir.password.update-temporary') }}">
            @csrf
            <label for="password">Password baru</label>
            <div class="password-wrapper">
                <input id="password" type="password" name="password" minlength="8" autocomplete="new-password" required>
                <button type="button" class="toggle-password" data-target="password" aria-label="Tampilkan password baru" aria-pressed="false">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            <label for="password_confirmation">Konfirmasi password baru</label>
            <div class="password-wrapper">
                <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required>
                <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Tampilkan konfirmasi password" aria-pressed="false">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            <button type="submit">Simpan Password Baru</button>
        </form>
    </main>
    <script>
        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.target);
                const showPassword = input.type === 'password';

                input.type = showPassword ? 'text' : 'password';
                button.setAttribute('aria-pressed', String(showPassword));
                button.setAttribute(
                    'aria-label',
                    showPassword ? 'Sembunyikan password' : 'Tampilkan password'
                );

                button.innerHTML = showPassword
                    ? '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18"></path><path d="M10.6 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.9"></path><path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"></path></svg>'
                    : '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            });
        });
    </script>
</body>
</html>
