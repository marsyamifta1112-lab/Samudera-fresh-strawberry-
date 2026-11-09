<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru - Samudera Fresh Strawberry</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #fffdf8; color: #205936; font-family: Arial, sans-serif; }
        header { height: 85px; display: flex; align-items: center; padding: 15px 5%; }
        .brand { display: flex; align-items: center; gap: 10px; color: #245b3b; font-size: 14px; font-weight: bold; text-decoration: none; }
        .logo { width: 58px; height: 55px; object-fit: contain; }
        .brand small { display: block; margin-top: 4px; color: #e93658; }
        main { width: min(90%, 520px); margin: 55px auto; padding: 42px; border-radius: 24px; background: #fff; box-shadow: 0 1px 8px rgba(0,0,0,.08); }
        h1 { margin: 0 0 12px; font-size: 27px; }
        p { color: #555; font-size: 14px; line-height: 1.6; }
        label { display: block; margin: 20px 0 7px; color: #222; font-size: 13px; font-weight: bold; }
        .password-wrapper { position: relative; }
        .password-wrapper input { width: 100%; height: 44px; padding: 10px 48px 10px 12px; border: 1px solid #ddd; border-radius: 5px; }
        input:focus { outline: none; border-color: #e93658; }
        .toggle-password { position: absolute; top: 50%; right: 10px; transform: translateY(-50%); display: grid; place-items: center; width: 32px; height: 32px; padding: 0; border: 0; background: transparent; color: #777; cursor: pointer; }
        .toggle-password:hover { color: #205936; }
        .toggle-password:focus-visible { outline: 2px solid #205936; outline-offset: 2px; border-radius: 4px; }
        .toggle-password svg { width: 21px; height: 21px; fill: none; stroke: currentColor; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        button { width: 100%; height: 44px; margin-top: 24px; border: 0; border-radius: 5px; background: #e93658; color: #fff; font-weight: bold; cursor: pointer; }
        .toggle-password { margin: 0; }
        form > button { margin-top: 24px; }
        .message { margin: 14px 0; color: #e93658; font-size: 13px; line-height: 1.5; }
        .back { display: block; margin-top: 20px; color: #e93658; font-size: 13px; text-align: center; text-decoration: none; }
        @media (max-width: 560px) { main { margin: 28px auto; padding: 28px 22px; } }
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
        <h1>Buat Password Baru</h1>
        <p>Verifikasi kode pemulihan berhasil. Buat password baru minimal 8 karakter.</p>

        @if (session('error'))
            <div class="message" role="alert">{{ session('error') }}</div>
        @endif
        @foreach ($errors->all() as $error)
            <div class="message" role="alert">{{ $error }}</div>
        @endforeach

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <label for="password">Password baru</label>
            <div class="password-wrapper">
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
                <button type="button" class="toggle-password" data-target="password" aria-label="Tampilkan password baru" aria-pressed="false">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>

            <label for="password_confirmation">Konfirmasi password baru</label>
            <div class="password-wrapper">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
                <button type="button" class="toggle-password" data-target="password_confirmation" aria-label="Tampilkan konfirmasi password" aria-pressed="false">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>

            <button type="submit">Simpan Password Baru</button>
        </form>
        <a class="back" href="{{ route('login') }}">Kembali ke Login</a>
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
