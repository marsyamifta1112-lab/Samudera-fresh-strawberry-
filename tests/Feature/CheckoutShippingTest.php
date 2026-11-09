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
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id_pembayaran');
            $table->integer('id_pesanan');
            $table->string('metode_pembayaran');
            $table->decimal('jumlah_bayar', 12, 2);
            $table->string('status_pembayaran');
        });
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
            ->assertSee('Ongkir dihitung otomatis dari kecamatan/kota pada alamat');

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
        ])->assertRedirect(route('pembayaran.show', 1));

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'wilayah_ongkir' => 'Pasirjambu & Soreang',
            'ongkir' => 12000,
            'total_harga' => 24000,
        ]);
        $this->assertDatabaseHas('pembayaran', [
            'id_pesanan' => 1,
            'jumlah_bayar' => 24000,
        ]);
    }
}
