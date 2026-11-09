<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Samudera Fresh Strawberry</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fffdf8;
            color: #292929;
            font-family: Arial, sans-serif;
        }

        .login-card {
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            width: 100%;
            max-width: 900px;
            min-height: 430px;
            overflow: hidden;
            background: white;
            border: 1px solid #f0eadf;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(45, 54, 43, 0.06);
            margin: 28px;
        }

        .image-panel {
            min-height: 430px;
            padding: 14px;
            background: #fff;
        }

        .image-panel img {
            display: block;
            width: 100%;
            height: 100%;
            min-height: 400px;
            object-fit: cover;
            border-radius: 14px;
        }

        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 38px 11%;
        }

        .form-content {
            width: 100%;
            max-width: 400px;
        }

        h1 {
            margin-bottom: 22px;
            color: #285c3d;
            font-size: 24px;
            line-height: 1.3;
        }

        .field {
            margin-bottom: 17px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            font-size: 14px;
            font-weight: 600;
        }

        input {
            width: 100%;
            height: 42px;
            padding: 0 12px;
            border: 1px solid #dedede;
            border-radius: 4px;
            font-size: 12px;
            outline: none;
        }

        input:focus {
            border-color: #285c3d;
        }

        .login-button {
            width: 100%;
            min-height: 42px;
            border: none;
            border-radius: 4px;
            background: #e83b59;
            color: white;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-button:hover {
            background: #cc2948;
        }

        .brand {
            margin-top: 26px;
            color: #285c3d;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            line-height: 1.6;
        }

        .account-note {
            margin-top: 10px;
            color: #626b63;
            font-size: 13px;
            line-height: 1.5;
        }

        .error {
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 6px;
            background: #fde8ec;
            color: #a3223d;
            font-size: 13px;
        }

        .error p + p {
            margin-top: 5px;
        }

        @media (max-width: 700px) {
            .login-card {
                grid-template-columns: 1fr;
                max-width: 440px;
                min-height: 0;
                margin: 18px;
                border-radius: 20px;
            }

            .image-panel {
                min-height: 185px;
                height: 185px;
                padding: 12px 12px 0;
            }

            .image-panel img {
                min-height: 0;
                height: 100%;
            }

            .form-panel {
                padding: 28px 26px 30px;
            }

            h1 {
                font-size: 22px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <main class="login-card">
        <div class="image-panel">
            <img
                src="{{ asset('images/strawberry.jpeg') }}"
                alt="Stroberi segar dari Ciwidey"
            >
        </div>

        <div class="form-panel">
            <div class="form-content">
                <h1>Masuk ke Akun Admin</h1>

                    @if (session('error'))
                        <div class="error" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="error" role="alert">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.login.process') }}" method="POST">
                        @csrf

                        <div class="field">
                            <label for="username">Username</label>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="{{ old('username') }}"
                                placeholder="Masukkan username"
                                autocomplete="username"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="password">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Masukkan password"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <button class="login-button" type="submit">Login</button>
                    </form>

                    <div class="brand">
                        SAMUDERA FRESH<br>
                        STRAWBERRY
                    </div>
                    <p class="account-note">Akun Admin hanya dapat dibuat oleh Admin yang berwenang melalui menu Petugas.</p>
            </div>
        </div>
    </main>
</body>
</html>