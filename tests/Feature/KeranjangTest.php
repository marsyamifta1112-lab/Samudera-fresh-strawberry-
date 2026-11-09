<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KeranjangTest extends TestCase
{
    private int $idProduk;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('produk', function (Blueprint $table) {
            $table->bigIncrements('id_produk');
            $table->string('nama_produk');
            $table->decimal('harga_produk', 12, 2);
            $table->string('foto_produk')->nullable();
            $table->unsignedInteger('stok_produk');
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->bigIncrements('id_pesanan');
            $table->unsignedBigInteger('id_pelanggan');
            $table->dateTime('tanggal_pemesanan');
            $table->decimal('total_harga', 12, 2);
            $table->decimal('ongkir', 12, 2)->nullable()->default(0);
            $table->string('wilayah_ongkir', 120)->nullable();
            $table->string('status_pesanan');
            $table->string('nama_penerima');
            $table->text('alamat_pengiriman');
            $table->string('no_hp');
            $table->text('catatan')->nullable();
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->bigIncrements('id_detail');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedInteger('jumlah');
            $table->decimal('harga', 12, 2);
            $table->decimal('subtotal', 12, 2);
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->bigIncrements('id_pembayaran');
            $table->unsignedBigInteger('id_pesanan');
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->string('metode_pembayaran');
            $table->decimal('jumlah_bayar', 12, 2);
            $table->string('bukti_pembayaran')->nullable();
            $table->string('status_pembayaran');
            $table->string('nomor_struk', 32)->nullable()->unique();
            $table->dateTime('tanggal_verifikasi')->nullable();
        });

        Schema::create('pengiriman', function (Blueprint $table) {
            $table->bigIncrements('id_pengiriman');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_petugas');
            $table->string('nama_penerima')->nullable();
            $table->string('no_telp_penerima')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->string('status_pengiriman')->nullable();
            $table->dateTime('tanggal_pengiriman')->nullable();
        });

        Schema::create('ongkir_wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wilayah', 120);
            $table->unsignedBigInteger('tarif');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
        DB::table('ongkir_wilayah')->insert([
            'id' => 1,
            'nama_wilayah' => 'Ciwidey & Rancabali',
            'tarif' => 10000,
            'aktif' => true,
        ]);

        $this->idProduk = DB::table('produk')->insertGetId([
            'nama_produk' => 'Stroberi Segar',
            'harga_produk' => 25000,
            'foto_produk' => 'stroberi.jpg',
            'stok_produk' => 5,
        ], 'id_produk');
    }

    public function test_add_button_saves_cart_item_to_session(): void
    {
        $session = [
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ];

        $this->withSession($session)
            ->post(route('keranjang.tambah', $this->idProduk))
            ->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionHas('keranjang', function (array $keranjang): bool {
                return $keranjang[$this->idProduk]['jumlah'] === 1;
            });

        $this->withSession($session)
            ->post(route('keranjang.tambah', $this->idProduk))
            ->assertRedirect()
            ->assertSessionHas('keranjang', function (array $keranjang): bool {
                return $keranjang[$this->idProduk]['jumlah'] === 2;
            });

        $this->withSession($session)
            ->get(route('keranjang.index'))
            ->assertOk()
            ->assertSee('Ringkasan')
            ->assertSee('Stroberi Segar');
    }

    public function test_cart_quantity_and_remove_forms_use_their_supported_http_methods(): void
    {
        $session = [
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [
                $this->idProduk => [
                    'id_produk' => $this->idProduk,
                    'nama_produk' => 'Stroberi Segar',
                    'harga_produk' => 25000,
                    'foto_produk' => 'stroberi.jpg',
                    'jumlah' => 1,
                ],
            ],
        ];

        $this->withSession($session)
            ->get(route('keranjang.index'))
            ->assertOk()
            ->assertSee('value="PUT"', false)
            ->assertSee('value="DELETE"', false);

        $this->withSession($session)
            ->put(route('keranjang.update', $this->idProduk), ['jumlah' => 2])
            ->assertRedirect(route('keranjang.index'))
            ->assertSessionHas('keranjang', function (array $cart): bool {
                return $cart[$this->idProduk]['jumlah'] === 2;
            });

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [
                $this->idProduk => [
                    'id_produk' => $this->idProduk,
                    'nama_produk' => 'Stroberi Segar',
                    'harga_produk' => 25000,
                    'foto_produk' => 'stroberi.jpg',
                    'jumlah' => 2,
                ],
            ],
        ])->delete(route('keranjang.hapus', $this->idProduk))
            ->assertRedirect(route('keranjang.index'))
            ->assertSessionHas('keranjang', []);
    }

    public function test_checkout_page_matches_the_order_flow_and_shows_cart_total(): void
    {
        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [
                $this->idProduk => [
                    'id_produk' => $this->idProduk,
                    'nama_produk' => 'Stroberi Segar',
                    'harga_produk' => 25000,
                    'foto_produk' => 'stroberi.jpg',
                    'jumlah' => 2,
                ],
            ],
        ])->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('site-header', false)
            ->assertSee(route('produk.index'))
            ->assertSee('Informasi Pengiriman')
            ->assertSee('Ringkasan Pesanan')
            ->assertSee('Subtotal Produk')
            ->assertSee('Ongkir')
            ->assertSee('Pilih wilayah tujuan')
            ->assertSee('Rp10.000')
            ->assertSee('Rp50.000');
    }

    public function test_checkout_creates_order_from_session_cart(): void
    {
        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
            'keranjang' => [
                $this->idProduk => [
                    'id_produk' => $this->idProduk,
                    'nama_produk' => 'Stroberi Segar',
                    'harga_produk' => 25000,
                    'foto_produk' => 'stroberi.jpg',
                    'jumlah' => 2,
                ],
            ],
        ])->post(route('checkout.store'), [
            'nama_penerima' => 'Pelanggan Uji',
            'alamat_pengiriman' => 'Jalan Ciwidey No. 1',
            'wilayah_ongkir_id' => 1,
            'no_hp' => '081234567890',
        ])
            ->assertRedirect(route('pembayaran.show', 1))
            ->assertSessionHas('success')
            ->assertSessionMissing('keranjang');

        $this->assertDatabaseHas('pesanan', [
            'id_pelanggan' => 7,
            'total_harga' => 60000,
            'ongkir' => 10000,
            'wilayah_ongkir' => 'Ciwidey & Rancabali',
            'status_pesanan' => 'menunggu',
        ]);

        $this->assertDatabaseHas('detail_pesanan', [
            'id_produk' => $this->idProduk,
            'jumlah' => 2,
            'harga' => 25000,
            'subtotal' => 50000,
        ]);

        $this->assertDatabaseHas('produk', [
            'id_produk' => $this->idProduk,
            'stok_produk' => 3,
        ]);

        $this->assertDatabaseHas('pembayaran', [
            'id_pesanan' => 1,
            'metode_pembayaran' => 'BCA',
            'jumlah_bayar' => 60000,
            'status_pembayaran' => 'menunggu',
        ]);

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ])->get(route('pembayaran.show', 1))
            ->assertOk()
            ->assertSee('Rp10.000')
            ->assertSee('Rp60.000')
            ->assertSee('Upload Bukti Pembayaran');

        Storage::fake('local');
        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ])->get(route('pembayaran.upload.form', 1))->assertOk();
    }

    public function test_customer_can_upload_payment_proof_for_their_order(): void
    {
        Storage::fake('local');

        $idPesanan = $this->createOrderWithPayment(7);

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ])->get(route('pembayaran.show', $idPesanan))
            ->assertOk()
            ->assertSee('site-header', false)
            ->assertSee(route('produk.index'))
            ->assertSee('7829 8832 8274')
            ->assertSee('Rp25.000')
            ->assertSee('Subtotal produk')
            ->assertSee('Ongkir')
            ->assertSee('Upload Bukti Pembayaran')
            ->assertSee('name="metode_pembayaran"', false)
            ->assertSee('BRI')
            ->assertSee('BNI')
            ->assertSee('Mandiri');

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ])->get(route('pembayaran.upload.form', $idPesanan))
            ->assertOk()
            ->assertSee('site-header', false)
            ->assertSee(route('produk.index'))
            ->assertSee('Upload Bukti Pembayaran')
            ->assertSee(route('pembayaran.upload', $idPesanan));

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ])->post(route('pembayaran.upload', $idPesanan), [
            'bukti_pembayaran' => UploadedFile::fake()->create('bukti.jpg', 1, 'image/jpeg'),
        ])
            ->assertRedirect(route('pembayaran.show', $idPesanan))
            ->assertSessionHas('success');

        $pembayaran = DB::table('pembayaran')
            ->where('id_pesanan', $idPesanan)
            ->first();

        $this->assertNotNull($pembayaran->tanggal_pembayaran);
        $this->assertSame('menunggu', $pembayaran->status_pembayaran);
        $this->assertNotNull($pembayaran->bukti_pembayaran);
        Storage::disk('local')->assertExists($pembayaran->bukti_pembayaran);
    }

    public function test_customer_can_select_any_supported_transfer_bank_and_save_it_to_payment(): void
    {
        $idPesanan = $this->createOrderWithPayment(7);
        $session = [
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ];

        foreach (['BCA', 'BRI', 'BNI', 'Mandiri'] as $bank) {
            $this->withSession($session)
                ->post(route('pembayaran.method', $idPesanan), [
                    'metode_pembayaran' => $bank,
                ])
                ->assertRedirect(route('pembayaran.show', $idPesanan))
                ->assertSessionHas('success');

            $this->assertDatabaseHas('pembayaran', [
                'id_pesanan' => $idPesanan,
                'metode_pembayaran' => $bank,
            ]);
        }
    }

    public function test_customer_can_open_order_history_and_click_an_order_for_details(): void
    {
        $idPesanan = $this->createOrderWithPayment(7);

        $this->withSession([
            'id_pelanggan' => 7,
            'nama_pelanggan' => 'Pelanggan Uji',
            'role' => 'Pelanggan',
        ])->get(route('pesanan.index'))
            ->assertOk()
            ->assertSee('Riwayat Pesanan')
            ->assertSee('#SP'.str_pad((string) $idPesanan, 6, '0', STR_PAD_LEFT))
            ->assertSee('Pelanggan Uji')
            ->assertSee('Rp25.000')
            ->assertSee(route('pesanan.show', $idPesanan));

        $this->withSession([
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ])->get(route('pesanan.show', $idPesanan))
            ->assertOk()
            ->assertSee('Status Pesanan')
            ->assertSee('Pesanan Diproses')
            ->assertSee('Menunggu Pembayaran')
            ->assertSee('Detail Pesanan')
            ->assertSee('Stroberi Segar')
            ->assertSee('Subtotal Produk')
            ->assertSee('Ongkir')
            ->assertSee('Informasi Pengiriman');
    }

    public function test_order_status_timeline_tracks_payment_order_and_delivery_updates(): void
    {
        $idPesanan = $this->createOrderWithPayment(7);
        $session = [
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ];

        DB::table('pembayaran')
            ->where('id_pesanan', $idPesanan)
            ->update([
                'status_pembayaran' => 'diverifikasi',
                'bukti_pembayaran' => 'bukti-pembayaran/bukti.jpg',
            ]);

        $this->withSession($session)
            ->get(route('pesanan.show', $idPesanan))
            ->assertOk()
            ->assertSee('Pembayaran Dikonfirmasi')
            ->assertSee('class="step complete"', false)
            ->assertSee('class="step current"', false);

        DB::table('pesanan')
            ->where('id_pesanan', $idPesanan)
            ->update(['status_pesanan' => 'diproses']);

        $this->withSession($session)
            ->get(route('pesanan.show', $idPesanan))
            ->assertOk()
            ->assertSee('Status saat ini: <strong>Diproses</strong>', false);

        DB::table('pengiriman')->insert([
            'id_pesanan' => $idPesanan,
            'id_petugas' => 3,
            'nama_penerima' => 'Pelanggan Uji',
            'no_telp_penerima' => '081234567890',
            'alamat_pengiriman' => 'Jalan Ciwidey No. 1',
            'status_pengiriman' => 'dikirim',
        ]);

        $this->withSession($session)
            ->get(route('pesanan.show', $idPesanan))
            ->assertOk()
            ->assertSee('Status saat ini: <strong>Dalam Pengiriman</strong>', false);

        DB::table('pengiriman')
            ->where('id_pesanan', $idPesanan)
            ->update(['status_pengiriman' => 'diterima']);

        $this->withSession($session)
            ->get(route('pesanan.show', $idPesanan))
            ->assertOk()
            ->assertSee('Status saat ini: <strong>Selesai</strong>', false)
            ->assertSee('class="step current"', false);
    }

    public function test_customer_cannot_see_another_customers_orders_in_history_or_details(): void
    {
        $this->createOrderWithPayment(7);
        $session = [
            'id_pelanggan' => 8,
            'role' => 'Pelanggan',
        ];

        $this->withSession($session)
            ->get(route('pesanan.index'))
            ->assertOk()
            ->assertSee('Belum Ada Pesanan')
            ->assertDontSee('#SP000001');

        $this->withSession($session)
            ->get(route('pesanan.show', 1))
            ->assertNotFound();
    }

    public function test_customer_cannot_view_or_upload_proof_for_another_customers_order(): void
    {
        Storage::fake('local');

        $idPesanan = $this->createOrderWithPayment(7);
        $session = [
            'id_pelanggan' => 8,
            'role' => 'Pelanggan',
        ];

        $this->withSession($session)
            ->get(route('pembayaran.show', $idPesanan))
            ->assertNotFound();

        $this->withSession($session)
            ->get(route('pembayaran.upload.form', $idPesanan))
            ->assertNotFound();

        $this->withSession($session)
            ->post(route('pembayaran.method', $idPesanan), [
                'metode_pembayaran' => 'BRI',
            ])
            ->assertNotFound();

        $this->withSession($session)
            ->post(route('pembayaran.upload', $idPesanan), [
                'bukti_pembayaran' => UploadedFile::fake()->create('bukti.jpg', 1, 'image/jpeg'),
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('pembayaran', [
            'id_pesanan' => $idPesanan,
            'bukti_pembayaran' => null,
        ]);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    private function createOrderWithPayment(int $idPelanggan): int
    {
        $idPesanan = DB::table('pesanan')->insertGetId([
            'id_pelanggan' => $idPelanggan,
            'tanggal_pemesanan' => now(),
            'total_harga' => 25000,
            'status_pesanan' => 'menunggu',
            'nama_penerima' => 'Pelanggan Uji',
            'alamat_pengiriman' => 'Jalan Ciwidey No. 1',
            'no_hp' => '081234567890',
        ], 'id_pesanan');

        DB::table('pembayaran')->insert([
            'id_pesanan' => $idPesanan,
            'metode_pembayaran' => 'BCA',
            'jumlah_bayar' => 25000,
            'status_pembayaran' => 'menunggu',
        ]);

        DB::table('detail_pesanan')->insert([
            'id_pesanan' => $idPesanan,
            'id_produk' => $this->idProduk,
            'jumlah' => 1,
            'harga' => 25000,
            'subtotal' => 25000,
        ]);

        return $idPesanan;
    }

    public function test_cart_table_migration_removes_cart_table_from_business_schema(): void
    {
        foreach ([
            'cache',
            'cache_locks',
            'failed_jobs',
            'job_batches',
            'jobs',
            'password_reset_tokens',
            'sessions',
            'users',
        ] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
            });
        }

        $migration = require base_path('database/migrations/2026_10_09_000005_drop_laravel_support_tables.php');
        $migration->up();

        foreach ([
            'cache',
            'cache_locks',
            'failed_jobs',
            'job_batches',
            'jobs',
            'password_reset_tokens',
            'sessions',
            'users',
        ] as $tableName) {
            $this->assertFalse(Schema::hasTable($tableName), "{$tableName} should not exist.");
        }
    }
}
