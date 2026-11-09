<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran | Samudera Fresh Strawberry</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #fffdf8; color: #292929; font-family: Arial, Helvetica, sans-serif; }
        header {
            min-height: 84px;
            padding: 12px 4%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            background: #fffdf8;
            border-bottom: 1px solid #eee;
        }
        .brand { display: flex; align-items: center; gap: 10px; color: #245b3b; font-size: 14px; font-weight: bold; text-decoration: none; }
        .brand img { width: 60px; height: 55px; object-fit: contain; }
        .brand small { display: block; margin-top: 4px; color: #e93658; }
        nav { display: flex; align-items: center; gap: 28px; }
        nav a { color: #444; font-size: 13px; text-decoration: none; }
        nav a:hover { color: #e93658; }
        main { width: 92%; max-width: 1000px; margin: 28px auto 60px; }
        h1 { margin: 14px 0 24px; text-align: center; font-size: 29px; }
        .panel {
            max-width: 820px;
            margin: 0 auto;
            padding: 34px;
            border: 1px solid #eee;
            border-radius: 32px;
            background: #fff;
        }
        .panel h2 { margin: 0 0 28px; color: #245b3b; text-align: center; font-size: 22px; }
        .order { margin: -16px 0 23px; color: #777; text-align: center; font-size: 13px; }
        .alert { margin: 18px 0; padding: 13px 16px; border-radius: 9px; background: #e5f2e9; color: #245b3b; }
        .alert.error { background: #fde3e8; color: #a91f32; }
        .upload-zone {
            display: flex;
            align-items: center;
            gap: 22px;
            padding: 23px;
            border-radius: 14px;
            background: #fdebf0;
        }
        .upload-icon { color: #c92547; font-size: 31px; line-height: 1; }
        .upload-copy { display: flex; flex: 1; align-items: flex-start; flex-direction: column; gap: 12px; }
        .upload-copy label { font-size: 14px; font-weight: bold; }
        input[type="file"] { max-width: 100%; color: #555; }
        .help { margin: 10px 0 0; color: #777; font-size: 12px; line-height: 1.5; }
        .actions { display: flex; justify-content: center; gap: 12px; margin-top: 25px; }
        .button {
            display: inline-block;
            padding: 13px 27px;
            border: 0;
            border-radius: 7px;
            background: #e93658;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }
        .button:hover { background: #c92547; }
        .button.secondary { background: #e5f2e9; color: #245b3b; }
        @media (max-width: 650px) {
            header { align-items: flex-start; flex-direction: column; }
            nav { flex-wrap: wrap; gap: 15px; }
            .panel { padding: 24px 18px; }
            .upload-zone { align-items: flex-start; flex-direction: column; gap: 14px; }
        }
    </style>
</head>
<body>
    @include('partials.navigation', ['active' => 'keranjang'])

    <main>
        <h1>Upload Bukti Pembayaran</h1>
        <section class="panel">
            <h2>Upload Bukti Pembayaran</h2>
            <p class="order">Pesanan #{{ $pesanan->id_pesanan }} &bull; Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>

            @if ($errors->any())
                <div class="alert error" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if ($pembayaran->bukti_pembayaran)
                <div class="alert">
                    Bukti sebelumnya sudah diunggah. Mengirim bukti baru akan menggantinya.
                </div>
            @endif

            <form
                action="{{ route('pembayaran.upload', $pesanan->id_pesanan) }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                <div class="upload-zone">
                    <span class="upload-icon" aria-hidden="true">▨</span>
                    <div class="upload-copy">
                        <label for="bukti_pembayaran">Upload bukti pembayaran (JPG/PNG/WEBP)</label>
                        <input
                            id="bukti_pembayaran"
                            type="file"
                            name="bukti_pembayaran"
                            accept="image/jpeg,image/png,image/webp"
                            required
                        >
                        <p class="help">Format JPG, PNG, atau WEBP. Ukuran file maksimal 5 MB.</p>
                    </div>
                </div>
                <div class="actions">
                    <a class="button secondary" href="{{ route('pembayaran.show', $pesanan->id_pesanan) }}">Kembali</a>
                    <button type="submit" class="button">Kirim</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
