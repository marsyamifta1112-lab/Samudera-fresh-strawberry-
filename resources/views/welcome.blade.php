<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Samudera Fresh Strawberry</title>

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

        .home {
            min-height: 100vh;
            display: flex;
        }

        .left {
            width: 63%;
            padding: 45px 6%;
            display: flex;
            flex-direction: column;
        }

        .logo {
            width: 155px;
        }

        .content {
            margin-top: 75px;
        }

        .content h1 {
            color: #205936;
            font-size: 42px;
            line-height: 1.2;
            margin-bottom: 25px;
        }

        .content p {
            color: #555;
            font-size: 14px;
            margin-bottom: 35px;
        }

        .btn {
            display: inline-block;
            width: 153px;
            padding: 12px 20px;
            background: #e93658;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 4px;
            font-size: 13px;
        }

        .copyright {
            margin-top: auto;
            color: #555;
            font-size: 10px;
        }

        .right {
            width: 37%;
            height: 100vh;
        }

        .right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <div class="home">

        <div class="left">

            <img
                src="{{ asset('images/logo samudera.jpeg') }}"
                class="logo"
                alt="Samudera Fresh Strawberry"
            >

            <div class="content">

                <h1>
                    Fresh from<br>
                    Ciwidey.
                </h1>

                <p>
                    Segar • Lezat • Alami
                </p>

                <a href="{{ route('login') }}" class="btn">
                    Mulai Belanja
                </a>

            </div>

            <div class="copyright">
                © Ciwidey • Produk olahan stroberi
            </div>

        </div>


        <div class="right">

            <img
                src="{{ asset('images/strawberry.jpeg') }}"
                alt="Strawberry Ciwidey"
            >

        </div>

    </div>

</body>
</html>