<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Onboarding - Samudera Fresh Strawberry</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #fffdf8;
        }

        /* ================= HEADER ================= */

        header {
            height: 85px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 4%;
            background: #fffdf8;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo {
            width: 80px;
            height: 65px;
            object-fit: contain;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .brand-text h3 {
            color: #245b3b;
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .brand-text span {
            color: #e93658;
            font-size: 11px;
            font-weight: bold;
        }

        /* ================= MENU ================= */

        nav {
            display: flex;
            align-items: center;
            gap: 38px;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-size: 13px;
        }

        nav a.active {
            color: #e93658;
            font-weight: bold;
            border-bottom: 3px solid #e93658;
            padding-bottom: 10px;
        }

        .cart {
            background: #ffe7ec;
            padding: 10px 14px;
            border-radius: 5px;
            font-size: 16px;
        }

        /* ================= ONBOARDING ================= */

        .onboarding {
            width: 92%;
            max-width: 1200px;
            height: 550px;
            margin: 25px auto;

            display: flex;
            overflow: hidden;

            border-radius: 45px;
        }

        .onboarding-left {
            width: 57%;
            background: #205936;
            padding: 55px 65px;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .small-title {
            color: white;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .onboarding-left h1 {
            color: white;
            font-size: 36px;
            line-height: 1.2;
            margin-bottom: 45px;
        }

        .onboarding-left p {
            color: white;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 30px;
            max-width: 550px;
        }

        /* ================= TOMBOL MULAI ================= */

        .btn {
            display: inline-block;
            width: 105px;
            padding: 12px 20px;

            background: #e93658;
            color: white;

            text-decoration: none;
            text-align: center;

            border-radius: 4px;
            font-size: 12px;
        }

        .btn:hover {
            background: #d92f50;
        }

        /* ================= FOTO ================= */

        .onboarding-image {
            width: 43%;
            height: 100%;
        }

        .onboarding-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ================= FOOTER ================= */

        footer {
            color: #555;
            font-size: 10px;
            margin-left: 4%;
            margin-top: 15px;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 768px) {

            header {
                height: auto;
                padding: 15px 5%;
            }

            nav {
                gap: 15px;
            }

            .onboarding {
                height: auto;
                flex-direction: column;
            }

            .onboarding-left,
            .onboarding-image {
                width: 100%;
            }

            .onboarding-left {
                padding: 45px 35px;
            }

            .onboarding-image {
                height: 350px;
            }
        }
    </style>
</head>

<body>

    <!-- ================= HEADER ================= -->

    <header>

        <a href="/onboarding" class="brand">

            <img
                src="{{ asset('images/logo samudera.jpeg') }}"
                class="logo"
                alt="Samudera Fresh Strawberry"
            >

            <div class="brand-text">
                <h3>SAMUDERA FRESH</h3>
                <span>STRAWBERRY</span>
            </div>

        </a>

        <nav>

            <a href="{{ url('/onboarding') }}" class="active">
                Beranda
            </a>

            <a href="{{ route('produk.index') }}">
                Produk
            </a>

            <a href="{{ route('pesanan.index') }}">
                Pesanan
            </a>

            <a href="{{ route('pelanggan.profile') }}">
                Profil
            </a>

            <a href="{{ route('keranjang.index') }}" class="cart" aria-label="Buka keranjang">🛒</a>

        </nav>

    </header>

    <!-- ================= ONBOARDING ================= -->

    <main class="onboarding">

        <section class="onboarding-left">

            <div class="small-title">
                Onboarding
            </div>

            <h1>
                Produk Olahan<br>
                Strawberry dari Ciwidey
            </h1>

            <p>
                Rasakan kesegaran dan kelezatan produk stroberi
                pilihan langsung dari petani Ciwidey.
            </p>

            <!-- SEKARANG SUDAH MENUJU PRODUK -->
            <a href="/produk" class="btn">
                Mulai
            </a>

        </section>


        <section class="onboarding-image">

            <img
                src="{{ asset('images/strawberry.jpeg') }}"
                alt="Strawberry Ciwidey"
            >

        </section>

    </main>


    <!-- ================= FOOTER ================= -->

    <footer>
        © Ciwidey • Produk olahan stroberi
    </footer>

</body>
</html>