<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password - Samudera Fresh Strawberry</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: #fffdf8;
            color: #333;
        }

        .reset-container {
            width: 100%;
            max-width: 625px;
            padding: 46px 48px;
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 8px 28px rgba(32, 89, 54, 0.09);
        }

        h1 {
            margin-bottom: 14px;
            color: #205936;
            font-size: 32px;
            line-height: 1.25;
        }

        .description {
            margin-bottom: 25px;
            color: #555;
            font-size: 15px;
            line-height: 1.7;
        }

        .notice {
            margin-bottom: 25px;
            padding: 16px;
            border-radius: 10px;
            background: #f2f7f2;
            color: #536958;
            font-size: 13px;
            line-height: 1.7;
        }

        .message-error {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 8px;
            background: #fff0f0;
            color: #b4233c;
            font-size: 13px;
            line-height: 1.6;
        }

        .message-success {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 8px;
            background: #edf8ef;
            color: #205936;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 21px;
        }

        label {
            display: block;
            margin-bottom: 9px;
            color: #333;
            font-size: 14px;
            font-weight: bold;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            width: 100%;
            height: 56px;
            padding: 0 52px 0 14px;
            border: 1px solid #e2e2e2;
            border-radius: 9px;
            background: #fff;
            color: #333;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .password-wrapper input:focus {
            border-color: #205936;
            box-shadow: 0 0 0 3px rgba(32, 89, 54, 0.08);
        }

        .password-wrapper input::placeholder {
            color: #999;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 13px;
            transform: translateY(-50%);
            display: flex;
            justify-content: center;
            align-items: center;
            width: 28px;
            height: 32px;
            padding: 0;
            border: none;
            background: transparent;
            color: #777;
            cursor: pointer;
        }

        .toggle-password:hover {
            color: #205936;
        }

        .toggle-password svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .submit-button {
            width: 100%;
            min-height: 56px;
            margin-top: 5px;
            border: none;
            border-radius: 9px;
            background: #e93658;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }

        .submit-button:hover {
            background: #d92f50;
        }

        .back-link {
            display: block;
            margin-top: 23px;
            color: #d95770;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            body {
                padding: 20px 14px;
            }

            .reset-container {
                padding: 30px 23px;
                border-radius: 22px;
            }

            h1 {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>

    <main class="reset-container">

        <h1>Buat Password Baru</h1>

        <p class="description">
            Permintaan pemulihan sudah disetujui Admin lain.
            Buat password baru untuk akun Admin kamu.
        </p>

        <div class="notice">
            Persetujuan ini hanya berlaku selama 30 menit dan hanya dapat digunakan sekali.
            Buat password minimal 8 karakter, lalu konfirmasikan password yang sama.
        </div>

        @if (session('error'))
            <div class="message-error" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="message-error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="message-success" role="status">
                {{ session('success') }}
            </div>
        @endif

        <form
            action="{{ route('admin.password.update') }}"
            method="POST"
        >
            @csrf

            <div class="form-group">
                <label for="password">Password Baru</label>

                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password baru"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="password"
                        aria-label="Tampilkan password baru"
                        aria-pressed="false"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">
                    Konfirmasi Password Baru
                </label>

                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Masukkan ulang password baru"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="password_confirmation"
                        aria-label="Tampilkan konfirmasi password"
                        aria-pressed="false"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="submit-button">
                Simpan Password Baru
            </button>
        </form>

        <a
            href="{{ route('admin.password.request') }}"
            class="back-link"
        >
            Kembali ke verifikasi kode
        </a>

    </main>

    <script>
        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(
                    button.getAttribute('data-target')
                );

                const showPassword = input.type === 'password';

                input.type = showPassword ? 'text' : 'password';

                button.setAttribute(
                    'aria-pressed',
                    String(showPassword)
                );

                button.setAttribute(
                    'aria-label',
                    showPassword
                        ? 'Sembunyikan password'
                        : 'Tampilkan password'
                );

                if (showPassword) {
                    button.innerHTML = `
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 3l18 18"></path>
                            <path d="M10.6 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.9"></path>
                            <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"></path>
                        </svg>
                    `;
                } else {
                    button.innerHTML = `
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    `;
                }
            });
        });
    </script>

</body>
</html>