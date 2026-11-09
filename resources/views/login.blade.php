<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Samudera Fresh Strawberry</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #fffdf8;
            color: #205936;
        }

        header {
            height: 85px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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

        nav {
            display: flex;
            gap: 35px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-size: 13px;
        }

        nav a.active {
            color: #e93658;
            font-weight: bold;
        }

        .login-box {
            width: 90%;
            max-width: 1150px;
            min-height: 500px;
            margin: 25px auto;
            background: white;
            border-radius: 35px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.08);
        }

        .login-image {
            width: 40%;
            padding: 25px;
        }

        .login-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .login-content {
            width: 60%;
            padding: 65px 70px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-content h1 {
            font-size: 30px;
            line-height: 1.2;
            margin-bottom: 25px;
        }

        .welcome {
            color: #333;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        label {
            display: block;
            color: #222;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            height: 42px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 18px;
            outline: none;
        }

        input:focus {
            border-color: #e93658;
        }

        .password-field {
            position: relative;
        }

        .password-field input {
            padding-right: 48px;
        }

        .password-toggle {
            position: absolute;
            top: 0;
            right: 0;
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border: 0;
            background: transparent;
            color: #666;
            cursor: pointer;
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.8;
        }

        .forgot-links {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            margin: -9px 0 18px;
        }

        .forgot-password {
            display: block;
            width: fit-content;
            color: #e93658;
            font-size: 12px;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            height: 42px;
            border: none;
            border-radius: 4px;
            background: #e93658;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .login-button:hover {
            background: #d92f50;
        }

        .register {
            text-align: center;
            color: #555;
            font-size: 12px;
            line-height: 1.8;
            margin-top: 18px;
        }

        .register a {
            color: #e93658;
            text-decoration: none;
            font-weight: bold;
        }

        .error-message {
            color: #e93658;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .success-message {
            color: #205936;
            font-size: 12px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            header {
                height: auto;
                gap: 15px;
                flex-wrap: wrap;
            }

            nav {
                gap: 18px;
            }

            .login-box {
                flex-direction: column;
                margin: 15px auto;
            }

            .login-image,
            .login-content {
                width: 100%;
            }

            .login-image {
                height: 220px;
                padding: 15px;
            }

            .login-content {
                padding: 25px;
            }
        }
    </style>
</head>

<body>
    <header>
        <a href="/" class="brand">
            <img
                src="{{ asset('images/logo samudera.jpeg') }}"
                class="logo"
                alt="Samudera Fresh Strawberry"
            >
            <span>SAMUDERA FRESH<small>STRAWBERRY</small></span>
        </a>

        <nav>
            <a href="/">Beranda</a>
            <a href="#">Keranjang</a>
            <a href="{{ route('login') }}" class="active">Login</a>
        </nav>
    </header>

    <div class="login-box">
        <div class="login-image">
            <img
                src="{{ asset('images/strawberry.jpeg') }}"
                alt="Strawberry Ciwidey"
            >
        </div>

        <div class="login-content">
            <h1>Fresh Strawberry,<br>Better Mood</h1>

            <p class="welcome">
                <strong>Selamat Datang!</strong><br>
                Masuk ke akun kamu untuk melanjutkan
                menggunakan akun Samudera Fresh Strawberry.
            </p>

            @if(session('error'))
                <p class="error-message">{{ session('error') }}</p>
            @endif

            @if(session('success'))
                <p class="success-message">{{ session('success') }}</p>
            @endif

            @if(session('info'))
                <p class="success-message">{{ session('info') }}</p>
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <p class="error-message">{{ $error }}</p>
                @endforeach
            @endif

            <form method="POST" action="{{ route('login.process') }}">
                @csrf

                <label for="username">Email / Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan email atau username"
                    value="{{ old('username', request('username')) }}"
                    autocomplete="username"
                    required
                >

                <label for="password">Password</label>

                <div class="password-field">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        id="toggle-password"
                        aria-label="Tampilkan password"
                        aria-pressed="false"
                    >
                        <svg
                            id="eye-open"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                            style="display:none"
                        >
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>

                        <svg
                            id="eye-closed"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="m3 3 18 18"></path>
                            <path d="M10.6 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.9"></path>
                            <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"></path>
                            <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"></path>
                        </svg>
                    </button>
                </div>

                <div class="forgot-links">
                    <a
                        class="forgot-password"
                        id="forgot-password-link"
                        href="{{ route('password.request') }}"
                    >
                        Lupa Password?
                    </a>

                    <a
                        class="forgot-password"
                        href="{{ route('admin.password.request') }}"
                    >
                        Admin? Lupa Password
                    </a>

                    <a
                        class="forgot-password"
                        href="{{ route('kurir.password-reset.request') }}"
                    >
                        Kurir? Ajukan Reset Password
                    </a>
                </div>

                <button type="submit" class="login-button">
                    LOG IN
                </button>
            </form>

            <div class="register">
                Belum punya akun?
                <a href="{{ route('register') }}">Daftar di sini</a>
                <br>
                Mendaftar sebagai Kurir?
                <a href="{{ route('kurir.register') }}">Daftar di sini</a>
            </div>
        </div>
    </div>

    <script>
        // Tampilkan atau sembunyikan password.
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('toggle-password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        passwordToggle.addEventListener('click', () => {
            const showPassword = passwordInput.type === 'password';

            passwordInput.type = showPassword ? 'text' : 'password';

            passwordToggle.setAttribute(
                'aria-pressed',
                String(showPassword)
            );

            passwordToggle.setAttribute(
                'aria-label',
                showPassword ? 'Sembunyikan password' : 'Tampilkan password'
            );

            eyeOpen.style.display = showPassword ? '' : 'none';
            eyeClosed.style.display = showPassword ? 'none' : '';
        });

        // Bawa username yang diketik ke halaman pemulihan password.
        const usernameInput = document.getElementById('username');
        const forgotPasswordLink = document.getElementById('forgot-password-link');
        const forgotPasswordUrl = @json(route('password.request'));

        function updateForgotPasswordLink() {
            const username = usernameInput.value.trim();

            forgotPasswordLink.href = username
                ? `${forgotPasswordUrl}?username=${encodeURIComponent(username)}`
                : forgotPasswordUrl;
        }

        usernameInput.addEventListener('input', updateForgotPasswordLink);
        updateForgotPasswordLink();
    </script>
</body>
</html>