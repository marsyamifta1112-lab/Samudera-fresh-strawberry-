<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar - Samudera Fresh Strawberry</title>

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

        /* HEADER */
        header {
            height: 85px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 5%;
        }

        .logo {
            width: 80px;
            height: auto;
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

        /* REGISTER BOX */
        .register-box {
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

        /* FOTO */
        .register-image {
            width: 40%;
            padding: 25px;
        }

        .register-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        /* CONTENT */
        .register-content {
            width: 60%;
            padding: 45px 70px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-content h1 {
            font-size: 30px;
            line-height: 1.2;
            margin-bottom: 15px;
        }

        .welcome {
            color: #333;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* FORM */
        label {
            display: block;
            color: #222;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            height: 40px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 14px;
            outline: none;
        }

        input:focus {
            border-color: #e93658;
        }

        /* BUTTON */
        .register-button {
            width: 100%;
            height: 42px;
            border: none;
            border-radius: 4px;
            background: #e93658;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 5px;
        }

        .register-button:hover {
            background: #d92f50;
        }

        /* LINK LOGIN */
        .login-link {
            text-align: center;
            color: #555;
            font-size: 12px;
            margin-top: 18px;
        }

        .login-link a {
            color: #e93658;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* ERROR */
        .error-message {
            color: #e93658;
            font-size: 12px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header>

        <a href="/">
            <img
                src="{{ asset('images/logo samudera.jpeg') }}"
                class="logo"
                alt="Samudera Fresh Strawberry"
            >
        </a>

        <nav>
            <a href="/">Beranda</a>
            <a href="/login">Login</a>
            <a href="/register" class="active">Daftar</a>
        </nav>

    </header>


    <!-- REGISTER -->
    <div class="register-box">

        <!-- FOTO STRAWBERRY -->
        <div class="register-image">

            <img
                src="{{ asset('images/strawberry.jpeg') }}"
                alt="Strawberry Ciwidey"
            >

        </div>


        <!-- FORM REGISTER -->
        <div class="register-content">

            <h1>
                Buat Akun,<br>
                Mulai Belanja
            </h1>

            <p class="welcome">
                <strong>Selamat Datang!</strong><br>
                Buat akun untuk melanjutkan berbelanja
                di Samudera Fresh Strawberry.
            </p>


            {{-- PESAN ERROR --}}
            @if(session('error'))
                <p class="error-message">
                    {{ session('error') }}
                </p>
            @endif


            {{-- FORM REGISTER --}}
            <form method="POST" action="{{ route('register.process') }}">

                @csrf

                <label for="nama_pelanggan">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    id="nama_pelanggan"
                    name="nama_pelanggan"
                    placeholder="Masukkan nama lengkap"
                    value="{{ old('nama_pelanggan') }}"
                    required
                >


                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email"
                    value="{{ old('email') }}"
                    required
                >


                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    value="{{ old('username') }}"
                    required
                >


                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >


                <button
                    type="submit"
                    class="register-button"
                >
                    DAFTAR
                </button>

            </form>


            <!-- LINK KEMBALI KE LOGIN -->
            <div class="login-link">
                Sudah punya akun?
                <a href="{{ route('login') }}">
                    Login di sini
                </a>
            </div>

        </div>

    </div>

</body>
</html>