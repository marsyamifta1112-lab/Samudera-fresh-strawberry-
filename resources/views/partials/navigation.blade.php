<style>
    .site-header {
        position: sticky;
        top: 0;
        z-index: 20;
        min-height: 84px;
        padding: 12px 5%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        background: rgba(255, 253, 248, .94);
        border-bottom: 1px solid #f0e9e3;
        box-shadow: 0 5px 18px rgba(58, 44, 35, .045);
        backdrop-filter: blur(12px);
    }
    .site-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #245b3b;
        font-size: 14px;
        font-weight: bold;
        text-decoration: none;
    }
    .site-brand img { width: 58px; height: 55px; object-fit: contain; }
    .site-brand small { display: block; margin-top: 4px; color: #e93658; }
    .site-nav { display: flex; align-items: center; gap: 28px; }
    .site-nav a {
        padding: 9px 12px;
        border-radius: 999px;
        color: #333;
        font-size: 13px;
        text-decoration: none;
        transition: background .2s ease, color .2s ease;
    }
    .site-nav a:hover { background: #f8f2ec; color: #245b3b; }
    .site-nav a.is-active { background: #fff0f2; color: #c92547; }
    .site-nav a.is-active { font-weight: bold; }
    .site-nav .site-cart {
        min-width: 44px;
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 13px;
        background: #ffe7ec;
        font-size: 18px;
    }
    .site-nav a:focus-visible,
    .site-brand:focus-visible {
        outline: 3px solid rgba(36, 91, 59, .35);
        outline-offset: 3px;
    }
    @media (max-width: 700px) {
        .site-header { flex-wrap: wrap; padding: 12px 5%; }
        .site-nav { width: 100%; justify-content: space-between; gap: 12px; }
    }
</style>

<header class="site-header">
    <a href="{{ route('produk.index') }}" class="site-brand">
        <img src="{{ asset('images/logo samudera.jpeg') }}" alt="Samudera Fresh Strawberry">
        <span>SAMUDERA FRESH<small>STRAWBERRY</small></span>
    </a>
    <nav class="site-nav" aria-label="Navigasi utama">
        <a href="{{ url('/onboarding') }}">Beranda</a>
        <a href="{{ route('produk.index') }}" class="{{ ($active ?? '') === 'produk' ? 'is-active' : '' }}">Produk</a>
        <a href="{{ route('pesanan.index') }}" class="{{ ($active ?? '') === 'pesanan' ? 'is-active' : '' }}">Pesanan</a>
        <a href="{{ route('pelanggan.profile') }}" class="{{ ($active ?? '') === 'profil' ? 'is-active' : '' }}">Profil</a>
        <a href="{{ route('keranjang.index') }}" class="site-cart {{ ($active ?? '') === 'keranjang' ? 'is-active' : '' }}" aria-label="Buka keranjang">🛒</a>
    </nav>
</header>
