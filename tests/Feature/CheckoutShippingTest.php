<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CheckoutShippingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('produk', function (Blueprint $table) {
            $table->increments('id_produk');
            $table->string('nama_produk');
            $table->string('foto_produk')->nullable();
            $table->decimal('harga_produk', 12, 2);
            $table->integer('stok_produk');
            $table->unsignedInteger('berat_gram')->nullable();
        });

        Schema::create('produk_grade', function (Blueprint $table) {
            $table->increments('id_produk_grade');
            $table->integer('id_produk');
            $table->char('grade', 1);
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('stok_kg', 12, 3)->default(0);
        });

        Schema::create('ongkir_wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wilayah', 120);
            $table->unsignedBigInteger('tarif');
            $table->boolean('aktif')->default(true);
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->integer('id_pelanggan');
            $table->dateTime('tanggal_pemesanan')->nullable();
            $table->date('tanggal_pemrosesan')->nullable();
            $table->date('jadwal_pengiriman')->nullable();
            $table->decimal('total_harga', 12, 2);
            $table->decimal('ongkir', 12, 2)->nullable();
            $table->string('wilayah_ongkir', 120)->nullable();
            $table->string('status_pesanan');
            $table->string('nama_penerima');
            $table->text('alamat_pengiriman');
            $table->string('no_hp', 20);
            $table->text('catatan')->nullable();
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->integer('id_pesanan');
            $table->integer('id_produk');
            $table->integer('jumlah');
            $table->decimal('harga', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->char('grade', 1)->nullable();
            $table->string('satuan', 12)->nullable();
            $table->unsignedInteger('berat_gram')->nullable();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id_pembayaran');
            $table->integer('id_pesanan');
            $table->string('metode_pembayaran');
            $table->decimal('jumlah_bayar', 12, 2);
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->string('status_pembayaran');
            $table->string('nomor_struk')->nullable();
            $table->dateTime('tanggal_verifikasi')->nullable();
        });

        Schema::create('pengaturan_operasional', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->time('jam_buka');
            $table->time('jam_tutup');
            $table->json('hari_operasional');
            $table->json('tanggal_libur');
            $table->dateTime('updated_at')->nullable();
        });
        DB::table('pengaturan_operasional')->insert([
            'id' => 1,
            'jam_buka' => '08:00:00',
            'jam_tutup' => '17:00:00',
            'hari_operasional' => json_encode([1, 2, 3, 4, 5, 6]),
            'tanggal_libur' => json_encode([]),
        ]);
    }

    public function test_checkout_detects_shipping_zone_from_address_and_caps_cost_at_product_subtotal(): void
    {
        DB::table('produk')->insert([
            'id_produk' => 1,
            'nama_produk' => 'Stroberi',
            'harga_produk' => 12000,
            'stok_produk' => 5,
        ]);
        DB::table('ongkir_wilayah')->insert([
            'nama_wilayah' => 'Pasirjambu & Soreang',
            'tarif' => 15000,
            'aktif' => true,
        ]);

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [
                1 => ['id_produk' => 1, 'jumlah' => 1],
            ],
        ])->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Wilayah Terdeteksi')
            ->assertSee('Jam Operasional')
            ->assertSee('Jadwal barang berangkat dari toko')
            ->assertSee('Perkiraan tiba di rumah')
            ->assertSee('Ongkir dihitung otomatis dari kecamatan/kota pada alamat')
            ->assertSee('value="Transfer"', false)
            ->assertSee('value="QRIS"', false);

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [
                1 => ['id_produk' => 1, 'jumlah' => 1],
            ],
        ])->post(route('checkout.store'), [
            'nama_penerima' => 'Penerima Uji',
            'alamat_pengiriman' => 'Jl. Contoh, Kecamatan Soreang, Kabupaten Bandung',
            'no_hp' => '08123456789',
            'metode_pembayaran' => 'Transfer',
        ])->assertRedirect(route('pembayaran.show', 1));

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'wilayah_ongkir' => 'Pasirjambu & Soreang',
            'ongkir' => 12000,
            'total_harga' => 24000,
        ]);
        $this->assertNotNull(DB::table('pesanan')->where('id_pesanan', 1)->value('jadwal_pengiriman'));
        $this->assertDatabaseHas('pembayaran', [
            'id_pesanan' => 1,
            'jumlah_bayar' => 24000,
            'metode_pembayaran' => 'Transfer',
        ]);
    }

    public function test_checkout_requires_a_supported_payment_method_and_saves_qris(): void
    {
        DB::table('produk')->insert([
            'id_produk' => 1,
            'nama_produk' => 'Stroberi',
            'harga_produk' => 12000,
            'stok_produk' => 5,
        ]);
        DB::table('ongkir_wilayah')->insert([
            'nama_wilayah' => 'Pasirjambu & Soreang',
            'tarif' => 10000,
            'aktif' => true,
        ]);

        $session = [
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [1 => ['id_produk' => 1, 'jumlah' => 1]],
        ];
        $checkout = [
            'nama_penerima' => 'Penerima Uji',
            'alamat_pengiriman' => 'Jl. Contoh, Kecamatan Soreang, Kabupaten Bandung',
            'no_hp' => '08123456789',
        ];

        $this->withSession($session)
            ->post(route('checkout.store'), $checkout)
            ->assertSessionHasErrors('metode_pembayaran');
        $this->withSession($session)
            ->post(route('checkout.store'), $checkout + ['metode_pembayaran' => 'Tunai'])
            ->assertSessionHasErrors('metode_pembayaran');
        $this->assertDatabaseCount('pesanan', 0);

        $this->withSession($session)
            ->post(route('checkout.store'), $checkout + ['metode_pembayaran' => 'QRIS'])
            ->assertRedirect(route('pembayaran.show', 1));

        $this->assertDatabaseHas('pembayaran', [
            'id_pesanan' => 1,
            'metode_pembayaran' => 'QRIS',
        ]);
        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ])->get(route('pembayaran.show', 1))
            ->assertOk()
            ->assertSee('Bayar dengan QRIS')
            ->assertSee('qris-merchant.png')
            ->assertSee('Perkiraan tiba di rumah:')
            ->assertSee('Jadwal berangkat dari toko:')
            ->assertDontSee('Bank Pengirim')
            ->assertDontSee('QRIS DEMO')
            ->assertSee('Upload Bukti Pembayaran');
        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ])->post(route('pembayaran.method', 1), ['metode_pembayaran' => 'BCA'])
            ->assertRedirect(route('pembayaran.show', 1))
            ->assertSessionHasErrors([
                'nama_pemilik_rekening_pengirim',
                'nomor_rekening_pengirim',
            ]);
        $this->assertDatabaseHas('pembayaran', [
            'id_pesanan' => 1,
            'metode_pembayaran' => 'QRIS',
        ]);
    }

    public function test_fresh_strawberry_checkout_uses_selected_grade_price_and_stock_snapshot(): void
    {
        DB::table('produk')->insert([
            'id_produk' => 2,
            'nama_produk' => 'Stroberi Segar',
            'foto_produk' => 'strawberry.jpeg',
            'harga_produk' => 0,
            'stok_produk' => 0,
        ]);
        DB::table('produk_grade')->insert([
            ['id_produk' => 2, 'grade' => 'A', 'harga_per_kg' => 80000, 'stok_kg' => 4],
            ['id_produk' => 2, 'grade' => 'B', 'harga_per_kg' => 70000, 'stok_kg' => 3],
            ['id_produk' => 2, 'grade' => 'C', 'harga_per_kg' => 50000, 'stok_kg' => 2],
        ]);
        DB::table('ongkir_wilayah')->insert([
            'nama_wilayah' => 'Bandung Raya',
            'tarif' => 15000,
            'aktif' => true,
        ]);

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [
                'grade-2-A' => ['id_produk' => 2, 'jumlah' => 2, 'grade' => 'A'],
            ],
        ])->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Grade A')
            ->assertSee('Rp80.000/kg');

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [
                'grade-2-A' => ['id_produk' => 2, 'jumlah' => 2, 'grade' => 'A'],
            ],
        ])->post(route('checkout.store'), [
            'nama_penerima' => 'Penerima Uji',
            'alamat_pengiriman' => 'Jl. Contoh, Kota Bandung',
            'no_hp' => '08123456789',
            'metode_pembayaran' => 'Transfer',
        ])->assertRedirect(route('pembayaran.show', 1));

        $this->assertDatabaseHas('detail_pesanan', [
            'id_produk' => 2,
            'jumlah' => 2,
            'harga' => 80000,
            'subtotal' => 160000,
            'grade' => 'A',
            'satuan' => 'kg',
        ]);
        $this->assertDatabaseHas('produk_grade', [
            'id_produk' => 2,
            'grade' => 'A',
            'stok_kg' => 2,
        ]);
        $this->assertDatabaseHas('produk_grade', [
            'id_produk' => 2,
            'grade' => 'B',
            'stok_kg' => 3,
        ]);

        DB::table('produk_grade')->where('id_produk', 2)->where('grade', 'A')->update(['harga_per_kg' => 90000]);
        $this->assertDatabaseHas('detail_pesanan', [
            'id_produk' => 2,
            'harga' => 80000,
            'subtotal' => 160000,
        ]);
    }

    public function test_processed_strawberry_checkout_keeps_database_price_and_package_weight(): void
    {
        DB::table('produk')->insert([
            'id_produk' => 3,
            'nama_produk' => 'Selai Strawberry',
            'foto_produk' => 'selai.jpeg',
            'harga_produk' => 15000,
            'stok_produk' => 5,
            'berat_gram' => 100,
        ]);
        DB::table('ongkir_wilayah')->insert([
            'nama_wilayah' => 'Bandung Raya',
            'tarif' => 15000,
            'aktif' => true,
        ]);

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [
                3 => ['id_produk' => 3, 'jumlah' => 2],
            ],
        ])->post(route('checkout.store'), [
            'nama_penerima' => 'Penerima Uji',
            'alamat_pengiriman' => 'Jl. Contoh, Kota Bandung',
            'no_hp' => '08123456789',
            'metode_pembayaran' => 'Transfer',
        ])->assertRedirect(route('pembayaran.show', 1));

        $this->assertDatabaseHas('detail_pesanan', [
            'id_produk' => 3,
            'jumlah' => 2,
            'harga' => 15000,
            'subtotal' => 30000,
            'grade' => null,
            'satuan' => 'paket',
            'berat_gram' => 100,
        ]);
        $this->assertDatabaseHas('produk', [
            'id_produk' => 3,
            'harga_produk' => 15000,
            'stok_produk' => 3,
            'berat_gram' => 100,
        ]);
    }
}
