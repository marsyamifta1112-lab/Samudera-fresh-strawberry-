<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kurir - Samudera Fresh Strawberry</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fffaf7;
            padding: 30px;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1240px;
            min-height: 620px;
            background: white;
            border: 1px solid #eee8e2;
            border-radius: 70px;
            overflow: hidden;
        }

        .login-image {
            width: 43%;
            min-height: 620px;
        }

        .login-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .login-form {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
        }

        h1 {
            color: #20583d;
            font-size: 34px;
            margin-bottom: 60px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 18px 16px;
            border: 1px solid #dedede;
            border-radius: 9px;
            outline: none;
            font-size: 14px;
        }

        input:focus {
            border-color: #20583d;
        }

        button {
            width: 100%;
            padding: 17px;
            margin-top: 0;
            border: none;
            border-radius: 8px;
            background: #df3153;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #c92345;
        }

        .error {
            color: #c92345;
            font-size: 13px;
            margin-top: 7px;
        }

        .alert {
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 7px;
            background: #fff0f0;
            color: #b42336;
            font-size: 14px;
        }

        .register-link {
            margin-top: 18px;
            text-align: center;
            color: #555;
            font-size: 13px;
        }

        .register-link a {
            color: #df3153;
            font-weight: bold;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            body {
                padding: 16px;
            }

            .login-container {
                min-height: auto;
                border-radius: 24px;
            }

            .login-image {
                display: none;
            }

            .login-form {
                padding: 35px 24px;
            }

            h1 {
                font-size: 27px;
                margin-bottom: 35px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-image">
            <img src="{{ asset('images/keripik.jpg') }}"
                 alt="Olahan stroberi Samudera Fresh Strawberry">
        </div>

        <div class="login-form">
            <h1>Masuk ke Akun Kurir</h1>

            @if (session('error'))
                <div class="alert">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert" style="background:#e9f2eb;color:#20583d">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('kurir.login.process') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        value="{{ old('username') }}"
                        required
                        autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password">
                </div>

                <button type="submit">Login</button>
            </form>

            <p class="register-link">Belum punya akun? <a href="{{ route('kurir.register') }}">Daftar di sini</a></p>
        </div>
    </div>
</body>
</html>