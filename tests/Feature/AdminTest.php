<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('produk', function (Blueprint $table) {
            $table->increments('id_produk');
            $table->string('nama_produk', 100)->nullable();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_produk', 12, 2)->nullable();
            $table->integer('stok_produk')->nullable();
            $table->string('foto_produk')->nullable();
            $table->unsignedInteger('berat_gram')->nullable();
            $table->boolean('is_available')->default(true);
        });

        Schema::create('produk_grade', function (Blueprint $table) {
            $table->increments('id_produk_grade');
            $table->integer('id_produk');
            $table->char('grade', 1);
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('stok_kg', 12, 3)->default(0);
        });

        Schema::create('pelanggan', function (Blueprint $table) {
            $table->increments('id_pelanggan');
            $table->string('nama_pelanggan');
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('recovery_code_hash')->nullable();
            $table->string('recovery_code_fingerprint', 64)->nullable()->unique();
            $table->dateTime('recovery_code_expires_at')->nullable();
            $table->dateTime('recovery_code_used_at')->nullable();
            $table->unsignedTinyInteger('recovery_code_attempts')->default(0);
            $table->dateTime('recovery_code_locked_until')->nullable();
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->integer('id_pelanggan');
            $table->dateTime('tanggal_pemesanan')->nullable();
            $table->date('tanggal_pemrosesan')->nullable();
            $table->date('jadwal_pengiriman')->nullable();
            $table->decimal('total_harga', 12, 2)->nullable();
            $table->decimal('ongkir', 12, 2)->nullable()->default(0);
            $table->string('wilayah_ongkir', 120)->nullable();
            $table->string('status_pesanan')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('catatan')->nullable();
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

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->integer('id_pesanan');
            $table->integer('id_produk');
            $table->integer('jumlah')->nullable();
            $table->decimal('harga', 12, 2)->nullable();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->char('grade', 1)->nullable();
            $table->string('satuan', 12)->nullable();
            $table->unsignedInteger('berat_gram')->nullable();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id_pembayaran');
            $table->integer('id_pesanan');
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->decimal('jumlah_bayar', 12, 2)->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->string('status_pembayaran')->nullable();
            $table->string('nama_pemilik_rekening_pengirim', 100)->nullable();
            $table->string('nomor_rekening_pengirim', 34)->nullable();
            $table->string('nomor_struk', 32)->nullable()->unique();
            $table->dateTime('tanggal_verifikasi')->nullable();
        });

        Schema::create('petugas', function (Blueprint $table) {
            $table->increments('id_petugas');
            $table->string('nama_petugas')->nullable();
            $table->string('username', 50)->nullable();
            $table->string('password')->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('role')->nullable();
            $table->string('status_akun', 30)->default('Aktif');
            $table->string('recovery_code_hash')->nullable();
            $table->dateTime('recovery_code_expires_at')->nullable();
            $table->dateTime('recovery_code_used_at')->nullable();
            $table->unsignedTinyInteger('recovery_code_attempts')->default(0);
            $table->dateTime('recovery_code_locked_until')->nullable();
            $table->dateTime('password_reset_requested_at')->nullable();
            $table->string('password_reset_status', 30)->nullable();
            $table->boolean('must_change_password')->default(false);
        });

        Schema::create('permintaan_reset_password_admin', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('admin_id');
            $table->unsignedInteger('reviewer_id')->nullable();
            $table->string('token_hash', 64)->nullable();
            $table->string('status', 30);
            $table->dateTime('requested_at');
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('expires_at');
            $table->timestamps();
        });

        Schema::create('ongkir_wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_wilayah', 120);
            $table->unsignedBigInteger('tarif');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
        DB::table('ongkir_wilayah')->insert([
            ['nama_wilayah' => 'Ciwidey & Rancabali', 'tarif' => 10000, 'aktif' => true],
            ['nama_wilayah' => 'Pasirjambu & Soreang', 'tarif' => 15000, 'aktif' => true],
            ['nama_wilayah' => 'Bandung Raya', 'tarif' => 25000, 'aktif' => true],
            ['nama_wilayah' => 'Luar Bandung Raya', 'tarif' => 50000, 'aktif' => true],
        ]);

        Schema::create('pengiriman', function (Blueprint $table) {
            $table->increments('id_pengiriman');
            $table->integer('id_pesanan');
            $table->integer('id_petugas');
            $table->string('nama_penerima')->nullable();
            $table->string('no_telp_penerima')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->string('status_pengiriman')->nullable();
            $table->dateTime('tanggal_pengiriman')->nullable();
        });
    }

    public function test_non_admin_cannot_open_admin_pages(): void
    {
        $this->withSession(['role' => 'Pelanggan'])
            ->get(route('admin.dashboard'))
            ->assertRedirect('/login');
    }

    public function test_existing_admin_login_opens_the_admin_dashboard_in_the_same_app(): void
    {
        DB::table('petugas')->insert([
            'id_petugas' => 1,
            'nama_petugas' => 'Admin Uji',
            'username' => 'admin-uji',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('login.process'))
            ->assertSee('Email / Username')
            ->assertSee('Masukkan email atau username');

        $this->get(route('admin.login'))
            ->assertRedirect(route('login'));

        $this->post(route('login.process'), [
            'username' => 'admin-uji',
            'password' => 'password123',
        ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('id_petugas', 1)
            ->assertSessionHas('role', 'Admin');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Grafik Penjualan');
    }

    public function test_shared_login_routes_an_active_courier_to_the_courier_dashboard(): void
    {
        DB::table('petugas')->insert([
            'id_petugas' => 2,
            'nama_petugas' => 'Kurir Uji',
            'username' => 'kurir-uji',
            'password' => Hash::make('password123'),
            'role' => 'Kurir',
        ]);

        $this->post(route('login.process'), [
            'username' => 'kurir-uji',
            'password' => 'password123',
        ])
            ->assertRedirect(route('kurir.dashboard'))
            ->assertSessionHas('id_petugas', 2)
            ->assertSessionHas('role', 'Kurir');

        $this->get(route('kurir.dashboard'))->assertOk();
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_shared_login_accepts_customer_email_and_customer_logout_clears_the_session(): void
    {
        DB::table('pelanggan')->insert([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Pelanggan Uji',
            'username' => 'pelanggan-uji',
            'email' => 'pelanggan@example.test',
            'password' => Hash::make('password123'),
        ]);

        $this->post(route('login.process'), [
            'username' => 'pelanggan@example.test',
            'password' => 'password123',
        ])
            ->assertRedirect('/onboarding')
            ->assertSessionHas('id_pelanggan', 1)
            ->assertSessionHas('role', 'Pelanggan');

        $this->get('/onboarding')->assertOk();
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->post(route('pelanggan.logout'))
            ->assertRedirect(route('login'))
            ->assertSessionMissing('role')
            ->assertSessionMissing('id_pelanggan');
    }

    public function test_shared_login_rejects_invalid_credentials(): void
    {
        $this->from(route('login'))
            ->post(route('login.process'), [
                'username' => 'not-a-real-account',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Username/email atau password salah.')
            ->assertSessionMissing('role');
    }

    public function test_role_middleware_requires_the_matching_role_identity(): void
    {
        $this->withSession(['role' => 'Admin'])
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $this->withSession(['id_petugas' => 1, 'role' => 'Kurir'])
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_approve_a_courier_registration_request(): void
    {
        DB::table('petugas')->insert([
            'id_petugas' => 2,
            'nama_petugas' => 'Calon Kurir',
            'username' => 'calon-kurir',
            'password' => Hash::make('password123'),
            'role' => 'Kurir',
            'status_akun' => 'Menunggu Persetujuan',
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.staff'))
            ->assertOk()
            ->assertSee('Calon Kurir')
            ->assertSee('Menunggu Persetujuan')
            ->assertSee(route('admin.staff.approve', 2));

        $this->actingAsAdmin()
            ->put(route('admin.staff.approve', 2))
            ->assertRedirect(route('admin.staff'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('petugas', [
            'id_petugas' => 2,
            'status_akun' => 'Aktif',
        ]);
    }

    public function test_admin_can_approve_and_reject_courier_password_reset_requests(): void
    {
        DB::table('petugas')->insert([
            [
                'id_petugas' => 20,
                'nama_petugas' => 'Kurir Menunggu',
                'username' => 'kurir-reset',
                'password' => Hash::make('password-lama'),
                'role' => 'Kurir',
                'password_reset_requested_at' => now(),
                'password_reset_status' => 'Menunggu Verifikasi',
            ],
            [
                'id_petugas' => 21,
                'nama_petugas' => 'Kurir Ditolak',
                'username' => 'kurir-tolak',
                'password' => Hash::make('password-lama'),
                'role' => 'Kurir',
                'password_reset_requested_at' => now(),
                'password_reset_status' => 'Menunggu Verifikasi',
            ],
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.staff'))
            ->assertOk()
            ->assertSee('Menunggu Verifikasi')
            ->assertSee(route('admin.staff.password-reset.approve', 20))
            ->assertSee(route('admin.staff.password-reset.reject', 21));

        $this->actingAsAdmin()
            ->put(route('admin.staff.password-reset.approve', 20))
            ->assertRedirect(route('admin.staff'))
            ->assertSessionHas('courier_temporary_password');

        $courier = DB::table('petugas')->where('id_petugas', 20)->first();
        $temporaryPassword = session('courier_temporary_password');
        $this->assertSame('Disetujui', $courier->password_reset_status);
        $this->assertTrue((bool) $courier->must_change_password);
        $this->assertTrue(Hash::check($temporaryPassword, $courier->password));

        $this->actingAsAdmin()
            ->put(route('admin.staff.password-reset.reject', 21))
            ->assertRedirect(route('admin.staff'));

        $this->assertDatabaseHas('petugas', [
            'id_petugas' => 21,
            'password_reset_status' => 'Ditolak',
        ]);
    }

    public function test_admin_can_create_update_and_delete_a_product(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Kelola Produk')
            ->assertSee(route('admin.products.best-sellers'));

        $this->actingAsAdmin()
            ->post(route('admin.products.store'), [
                'nama_produk' => 'Strawberry Segar',
                'deskripsi' => 'Produk pilihan Ciwidey',
                'harga_produk' => 25000,
                'stok_produk' => 12,
                'berat_gram' => 250,
            ])
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success');

        $idProduk = DB::table('produk')->value('id_produk');
        $this->assertDatabaseHas('produk', [
            'id_produk' => $idProduk,
            'nama_produk' => 'Strawberry Segar',
            'stok_produk' => 12,
            'is_available' => true,
        ]);

        $this->actingAsAdmin()
            ->put(route('admin.products.update', $idProduk), [
                'nama_produk' => 'Strawberry Segar Premium',
                'deskripsi' => 'Produk pilihan Ciwidey',
                'harga_produk' => 30000,
                'stok_produk' => 8,
                'berat_gram' => 250,
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('produk', [
            'id_produk' => $idProduk,
            'nama_produk' => 'Strawberry Segar Premium',
            'harga_produk' => 30000,
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.products.edit', $idProduk))
            ->assertOk()
            ->assertSee('Edit Produk')
            ->assertSee(route('admin.products.destroy', $idProduk));

        $this->actingAsAdmin()
            ->delete(route('admin.products.destroy', $idProduk))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('produk', ['id_produk' => $idProduk]);
    }

    public function test_admin_can_toggle_product_availability_and_customers_cannot_access_toggle(): void
    {
        DB::table('produk')->insert([
            'id_produk' => 50,
            'nama_produk' => 'Produk Toggle',
            'harga_produk' => 25000,
            'stok_produk' => 5,
            'is_available' => true,
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Tersedia')
            ->assertSee(route('admin.products.toggle', 50));

        $this->actingAsAdmin()
            ->post(route('admin.products.toggle', 50))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('produk', ['id_produk' => 50, 'is_available' => false]);

        $this->actingAsAdmin()
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Habis');

        $this->actingAsAdmin()
            ->post(route('admin.products.toggle', 50))
            ->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('produk', ['id_produk' => 50, 'is_available' => true]);

        $this->withSession(['id_pelanggan' => 7, 'role' => 'Pelanggan'])
            ->post(route('admin.products.toggle', 50))
            ->assertRedirect(route('login'));
        $this->assertDatabaseHas('produk', ['id_produk' => 50, 'is_available' => true]);

        $this->actingAsAdmin()
            ->post(route('admin.products.toggle', 999))
            ->assertNotFound();
    }

    public function test_admin_can_update_price_and_stock_for_each_strawberry_grade(): void
    {
        DB::table('produk')->insert([
            'id_produk' => 10,
            'nama_produk' => 'Stroberi Segar',
            'harga_produk' => 0,
            'stok_produk' => 0,
        ]);
        DB::table('produk_grade')->insert([
            ['id_produk_grade' => 1, 'id_produk' => 10, 'grade' => 'A', 'harga_per_kg' => 80000, 'stok_kg' => 2],
            ['id_produk_grade' => 2, 'id_produk' => 10, 'grade' => 'B', 'harga_per_kg' => 70000, 'stok_kg' => 3],
            ['id_produk_grade' => 3, 'id_produk' => 10, 'grade' => 'C', 'harga_per_kg' => 50000, 'stok_kg' => 4],
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.inventory'))
            ->assertOk()
            ->assertSee('Grade A')
            ->assertSee('Grade B')
            ->assertSee('Grade C');

        $this->actingAsAdmin()
            ->put(route('admin.inventory.grades.update', 2), [
                'harga_per_kg' => 72000,
                'stok_kg' => '3.250',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('produk_grade', [
            'id_produk_grade' => 2,
            'harga_per_kg' => 72000,
        ]);
        $this->assertEquals(3.25, DB::table('produk_grade')->where('id_produk_grade', 2)->value('stok_kg'));
    }

    public function test_admin_can_verify_payment_assign_courier_and_complete_delivery(): void
    {
        DB::table('pelanggan')->insert(['id_pelanggan' => 1, 'nama_pelanggan' => 'Pelanggan Uji']);
        DB::table('pesanan')->insert([
            'id_pesanan' => 1,
            'id_pelanggan' => 1,
            'tanggal_pemesanan' => now(),
            'jadwal_pengiriman' => '2026-11-17',
            'total_harga' => 25000,
            'status_pesanan' => 'menunggu',
            'nama_penerima' => 'Penerima Uji',
            'alamat_pengiriman' => 'Alamat Uji',
            'no_hp' => '08123456789',
        ]);
        DB::table('pembayaran')->insert([
            'id_pembayaran' => 1,
            'id_pesanan' => 1,
            'metode_pembayaran' => 'BCA',
            'nama_pemilik_rekening_pengirim' => 'Pelanggan Uji',
            'nomor_rekening_pengirim' => '1234567890',
            'jumlah_bayar' => 25000,
            'bukti_pembayaran' => 'bukti-pembayaran/receipt.jpg',
            'status_pembayaran' => 'menunggu',
        ]);
        DB::table('petugas')->insert([
            'id_petugas' => 2,
            'nama_petugas' => 'Kurir Uji',
            'username' => 'kurir',
            'role' => 'Kurir',
        ]);

        $this->actingAsAdmin()
            ->put(route('admin.payments.review', 1), ['status_pembayaran' => 'diverifikasi'])
            ->assertRedirect();

        $this->assertDatabaseHas('pesanan', ['id_pesanan' => 1, 'status_pesanan' => 'diproses']);
        $this->assertDatabaseHas('pembayaran', ['id_pesanan' => 1, 'status_pembayaran' => 'diverifikasi']);
        $payment = DB::table('pembayaran')->where('id_pesanan', 1)->first();
        $this->assertSame('SFS-'.now()->format('Ymd').'-000001', $payment->nomor_struk);
        $this->assertNotNull($payment->tanggal_verifikasi);

        $this->withSession([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Pelanggan Uji',
            'role' => 'Pelanggan',
        ])
            ->get(route('pembayaran.receipt', 1))
            ->assertOk()
            ->assertSee('Cetak Struk')
            ->assertSee($payment->nomor_struk)
            ->assertSee('Rp25.000')
            ->assertSee('Kp. Mekarbaru, RW 03 RT 02, Alamendah, Rancabali, Kabupaten Bandung')
            ->assertSee('size: 90mm 200mm')
            ->assertSee('font-size: 8pt');

        $this->withSession([
            'id_pelanggan' => 99,
            'nama_pelanggan' => 'Pelanggan Lain',
            'role' => 'Pelanggan',
        ])->get(route('pembayaran.receipt', 1))->assertNotFound();

        $this->actingAsAdmin()
            ->get(route('admin.orders.show', 1))
            ->assertOk()
            ->assertSee('Detail Pesanan (Admin)')
            ->assertSee('Selasa, 17-11-2026')
            ->assertSee('Verifikasi Pembayaran');
        $this->actingAsAdmin()
            ->get(route('admin.payments.show', 1))
            ->assertOk()
            ->assertSee('Verifikasi Pembayaran')
            ->assertSee('Diverifikasi')
            ->assertSee('Pelanggan Uji')
            ->assertSee('1234567890')
            ->assertSee(route('admin.payments.receipt', 1));

        $this->actingAsAdmin()
            ->get(route('admin.payments.receipt', 1))
            ->assertOk()
            ->assertSee('images/logo samudera.jpeg')
            ->assertSee('SAMUDERA FRESH')
            ->assertSee('Alamat Pengiriman:')
            ->assertSee('Alamat Uji')
            ->assertSee('TOTAL DIBAYAR');

        $this->actingAsAdmin()
            ->put(route('admin.shipments.assign', 1), ['id_petugas' => 2])
            ->assertRedirect();

        $idPengiriman = DB::table('pengiriman')->value('id_pengiriman');
        $this->assertDatabaseHas('pengiriman', [
            'id_pesanan' => 1,
            'id_petugas' => 2,
            'alamat_pengiriman' => 'Alamat Uji',
        ]);

        $this->actingAsAdmin()
            ->put(route('admin.shipments.update', $idPengiriman), ['status_pengiriman' => 'diterima'])
            ->assertRedirect();

        $this->assertDatabaseHas('pengiriman', [
            'id_pengiriman' => $idPengiriman,
            'status_pengiriman' => 'diterima',
        ]);
        $this->assertDatabaseHas('pesanan', ['id_pesanan' => 1, 'status_pesanan' => 'selesai']);
    }

    public function test_admin_can_set_shipping_cost_and_sync_order_and_payment_totals(): void
    {
        DB::table('pelanggan')->insert([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Pelanggan Uji',
        ]);
        DB::table('pesanan')->insert([
            'id_pesanan' => 1,
            'id_pelanggan' => 1,
            'tanggal_pemesanan' => now(),
            'total_harga' => 25000,
            'ongkir' => null,
            'status_pesanan' => 'menunggu',
            'nama_penerima' => 'Penerima Uji',
            'alamat_pengiriman' => 'Alamat Uji',
            'no_hp' => '08123456789',
        ]);
        DB::table('detail_pesanan')->insert([
            'id_pesanan' => 1,
            'id_produk' => 1,
            'jumlah' => 2,
            'harga' => 12500,
            'subtotal' => 25000,
        ]);
        DB::table('pembayaran')->insert([
            'id_pesanan' => 1,
            'metode_pembayaran' => 'BCA',
            'jumlah_bayar' => 0,
            'status_pembayaran' => 'menunggu',
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.orders.show', 1))
            ->assertOk()
            ->assertSee('Alamat Uji')
            ->assertSee('Belum ditetapkan')
            ->assertSee(route('admin.orders.ongkir.update', 1));

        $this->actingAsAdmin()
            ->put(route('admin.orders.ongkir.update', 1), ['ongkir' => '12.500'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'ongkir' => 12500,
            'total_harga' => 37500,
        ]);
        $this->assertDatabaseHas('pembayaran', [
            'id_pesanan' => 1,
            'jumlah_bayar' => 37500,
        ]);

        $this->withSession([
            'id_pelanggan' => 1,
            'role' => 'Pelanggan',
        ])->get(route('pembayaran.show', 1))
            ->assertOk()
            ->assertSee('Rp25.000')
            ->assertSee('Rp12.500')
            ->assertSee('Rp37.500')
            ->assertSee('Simpan Data Rekening Pengirim');

        $this->withSession([
            'id_pelanggan' => 1,
            'role' => 'Pelanggan',
        ])->post(route('pembayaran.method', 1), [
            'metode_pembayaran' => 'BCA',
            'nama_pemilik_rekening_pengirim' => 'Pelanggan Uji',
            'nomor_rekening_pengirim' => '1234567890',
        ])->assertRedirect(route('pembayaran.show', 1));

        $this->withSession([
            'id_pelanggan' => 1,
            'role' => 'Pelanggan',
        ])->get(route('pembayaran.show', 1))
            ->assertOk()
            ->assertSee('Upload Bukti Pembayaran');

        DB::table('pembayaran')->where('id_pesanan', 1)->update([
            'bukti_pembayaran' => 'bukti-pembayaran/test.jpg',
        ]);
        $this->actingAsAdmin()
            ->put(route('admin.orders.ongkir.update', 1), ['ongkir' => 15000])
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertDatabaseHas('pesanan', ['id_pesanan' => 1, 'ongkir' => 12500, 'total_harga' => 37500]);
    }

    public function test_admin_shipping_cost_is_capped_at_the_order_product_subtotal(): void
    {
        DB::table('pelanggan')->insert([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Pelanggan Uji',
        ]);
        DB::table('pesanan')->insert([
            'id_pesanan' => 1,
            'id_pelanggan' => 1,
            'tanggal_pemesanan' => now(),
            'total_harga' => 25000,
            'ongkir' => null,
            'status_pesanan' => 'menunggu',
            'nama_penerima' => 'Penerima Uji',
            'alamat_pengiriman' => 'Alamat Uji',
            'no_hp' => '08123456789',
        ]);
        DB::table('detail_pesanan')->insert([
            'id_pesanan' => 1,
            'id_produk' => 1,
            'jumlah' => 2,
            'harga' => 12500,
            'subtotal' => 25000,
        ]);

        $this->actingAsAdmin()
            ->put(route('admin.orders.ongkir.update', 1), ['ongkir' => '40.000'])
            ->assertRedirect()
            ->assertSessionHas('success', 'Ongkir melebihi subtotal produk, sehingga dibatasi sampai subtotal tersebut.');

        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'ongkir' => 25000,
            'total_harga' => 50000,
        ]);
    }

    public function test_admin_can_update_shipping_zone_rates_for_future_checkouts(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.shipping-rates'))
            ->assertOk()
            ->assertSee('Ciwidey & Rancabali')
            ->assertSee('Bandung');

        $this->actingAsAdmin()
            ->put(route('admin.shipping-rates.update'), [
                'tarif' => [
                    1 => '12.500',
                    2 => '20.000',
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('ongkir_wilayah', [
            'id' => 1,
            'tarif' => 12500,
        ]);
        $this->assertDatabaseHas('ongkir_wilayah', [
            'id' => 2,
            'tarif' => 20000,
        ]);
    }

    public function test_admin_can_update_operational_days_hours_and_holidays(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.operational-settings'))
            ->assertOk()
            ->assertSee('Jam Operasional & Hari Libur')
            ->assertSee('Senin');

        $this->actingAsAdmin()
            ->put(route('admin.operational-settings.update'), [
                'jam_buka' => '09:00',
                'jam_tutup' => '18:00',
                'hari_operasional' => ['1', '2', '3', '4', '5'],
                'tanggal_libur' => ['2026-11-16', '2026-11-17'],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $settings = DB::table('pengaturan_operasional')->where('id', 1)->first();
        $this->assertSame('09:00', substr($settings->jam_buka, 0, 5));
        $this->assertSame('18:00', substr($settings->jam_tutup, 0, 5));
        $this->assertSame([1, 2, 3, 4, 5], json_decode($settings->hari_operasional, true));
        $this->assertSame(['2026-11-16', '2026-11-17'], json_decode($settings->tanggal_libur, true));
    }

    public function test_admin_operational_settings_reject_invalid_hours_and_empty_operating_days(): void
    {
        $this->actingAsAdmin()
            ->put(route('admin.operational-settings.update'), [
                'jam_buka' => '17:00',
                'jam_tutup' => '08:00',
                'hari_operasional' => [],
            ])
            ->assertSessionHasErrors(['jam_tutup', 'hari_operasional']);
    }

    public function test_admin_report_filters_payment_method_and_order_status_and_sums_matching_orders(): void
    {
        DB::table('pelanggan')->insert([
            ['id_pelanggan' => 1, 'nama_pelanggan' => 'Pelanggan QRIS Diproses'],
            ['id_pelanggan' => 2, 'nama_pelanggan' => 'Pelanggan Transfer Selesai'],
            ['id_pelanggan' => 3, 'nama_pelanggan' => 'Pelanggan QRIS Selesai'],
            ['id_pelanggan' => 4, 'nama_pelanggan' => 'Pelanggan Menunggu'],
        ]);
        DB::table('pesanan')->insert([
            ['id_pesanan' => 1, 'id_pelanggan' => 1, 'tanggal_pemesanan' => '2026-05-10 10:00:00', 'total_harga' => 25000, 'status_pesanan' => 'diproses'],
            ['id_pesanan' => 2, 'id_pelanggan' => 2, 'tanggal_pemesanan' => '2026-05-11 10:00:00', 'total_harga' => 45000, 'status_pesanan' => 'selesai'],
            ['id_pesanan' => 3, 'id_pelanggan' => 3, 'tanggal_pemesanan' => '2026-05-12 10:00:00', 'total_harga' => 30000, 'status_pesanan' => 'selesai'],
            ['id_pesanan' => 4, 'id_pelanggan' => 4, 'tanggal_pemesanan' => '2026-05-13 10:00:00', 'total_harga' => 20000, 'status_pesanan' => 'menunggu'],
        ]);
        DB::table('pembayaran')->insert([
            ['id_pesanan' => 1, 'metode_pembayaran' => 'QRIS', 'jumlah_bayar' => 25000, 'status_pembayaran' => 'diverifikasi'],
            ['id_pesanan' => 2, 'metode_pembayaran' => 'BCA', 'jumlah_bayar' => 45000, 'status_pembayaran' => 'diverifikasi'],
            ['id_pesanan' => 3, 'metode_pembayaran' => 'QRIS', 'jumlah_bayar' => 30000, 'status_pembayaran' => 'menunggu'],
            ['id_pesanan' => 4, 'metode_pembayaran' => 'BCA', 'jumlah_bayar' => 20000, 'status_pembayaran' => 'menunggu'],
        ]);

        $dates = ['dari' => '2026-05-01', 'sampai' => '2026-05-31', 'periode' => 'bulanan'];

        $this->actingAsAdmin()
            ->get(route('admin.reports', $dates + [
                'metode_pembayaran' => 'QRIS',
                'status_pesanan' => 'selesai',
            ]))
            ->assertOk()
            ->assertSee('Pelanggan QRIS Selesai')
            ->assertDontSee('Pelanggan QRIS Diproses')
            ->assertDontSee('Pelanggan Transfer Selesai')
            ->assertSee('Total Pendapatan')
            ->assertSee('Rp 30.000')
            ->assertSee('value="QRIS" selected', false)
            ->assertSee('value="selesai" selected', false)
            ->assertSee('Pendapatan Bulanan');

        $this->actingAsAdmin()
            ->get(route('admin.reports', $dates + ['metode_pembayaran' => 'QRIS']))
            ->assertOk()
            ->assertSee('Pelanggan QRIS Diproses')
            ->assertSee('Pelanggan QRIS Selesai')
            ->assertDontSee('Pelanggan Transfer Selesai')
            ->assertSee('Rp 25.000');

        $this->actingAsAdmin()
            ->get(route('admin.reports', $dates + ['status_pesanan' => 'selesai']))
            ->assertOk()
            ->assertSee('Pelanggan Transfer Selesai')
            ->assertSee('Pelanggan QRIS Selesai')
            ->assertDontSee('Pelanggan QRIS Diproses')
            ->assertSee('Rp 45.000');

        $this->actingAsAdmin()
            ->get(route('admin.reports', $dates + [
                'metode_pembayaran' => 'QRIS',
                'status_pesanan' => 'menunggu',
            ]))
            ->assertOk()
            ->assertSee('Tidak ada pesanan pada rentang tanggal ini.')
            ->assertSee('Rp 0');

        $this->actingAsAdmin()
            ->get(route('admin.reports', $dates + ['nominal_min' => 30000]))
            ->assertOk()
            ->assertSee('Pelanggan Transfer Selesai')
            ->assertSee('Pelanggan QRIS Selesai')
            ->assertDontSee('Pelanggan QRIS Diproses')
            ->assertDontSee('Pelanggan Menunggu')
            ->assertSee('Rp 45.000')
            ->assertSee('name="nominal_min" min="0" step="0.01" value="30000"', false);

        $this->actingAsAdmin()
            ->get(route('admin.reports', $dates + ['nominal_max' => 25000]))
            ->assertOk()
            ->assertSee('Pelanggan QRIS Diproses')
            ->assertSee('Pelanggan Menunggu')
            ->assertDontSee('Pelanggan Transfer Selesai')
            ->assertDontSee('Pelanggan QRIS Selesai')
            ->assertSee('Rp 25.000');

        $this->actingAsAdmin()
            ->get(route('admin.reports', $dates + [
                'metode_pembayaran' => 'QRIS',
                'status_pesanan' => 'selesai',
                'nominal_min' => 25000,
                'nominal_max' => 35000,
            ]))
            ->assertOk()
            ->assertSee('Pelanggan QRIS Selesai')
            ->assertDontSee('Pelanggan QRIS Diproses')
            ->assertDontSee('Pelanggan Transfer Selesai')
            ->assertSee('Pendapatan Bulanan')
            ->assertSee('Rp 30.000')
            ->assertSee('value="35000"', false);

        $this->actingAsAdmin()
            ->get(route('admin.reports', $dates + [
                'nominal_min' => 35000,
                'nominal_max' => 25000,
            ]))
            ->assertSessionHasErrors('nominal_max');
    }

    public function test_admin_report_pagination_keeps_report_filters(): void
    {
        $orders = [];
        $payments = [];

        foreach (range(1, 16) as $id) {
            $orders[] = [
                'id_pesanan' => $id,
                'id_pelanggan' => $id,
                'tanggal_pemesanan' => '2026-05-10 10:00:00',
                'total_harga' => 1000,
                'status_pesanan' => 'selesai',
            ];
            $payments[] = [
                'id_pesanan' => $id,
                'metode_pembayaran' => 'QRIS',
                'jumlah_bayar' => 1000,
                'status_pembayaran' => 'diverifikasi',
            ];
        }

        DB::table('pesanan')->insert($orders);
        DB::table('pembayaran')->insert($payments);

        $this->actingAsAdmin()
            ->get(route('admin.reports', [
                'dari' => '2026-05-01',
                'sampai' => '2026-05-31',
                'metode_pembayaran' => 'QRIS',
                'status_pesanan' => 'selesai',
                'nominal_min' => 0,
            ]))
            ->assertOk()
            ->assertSee('metode_pembayaran=QRIS')
            ->assertSee('status_pesanan=selesai')
            ->assertSee('nominal_min=0')
            ->assertSee('Rp 16.000')
            ->assertSee('page=2');

        $this->actingAsAdmin()
            ->get(route('admin.reports.print', [
                'dari' => '2026-05-01',
                'sampai' => '2026-05-31',
                'metode_pembayaran' => 'QRIS',
                'status_pesanan' => 'selesai',
            ]))
            ->assertOk()
            ->assertSee('16 pesanan')
            ->assertSee('#16')
            ->assertSee('Cetak / Simpan PDF')
            ->assertSee('window.print()', false);
    }

    public function test_report_summary_and_charts_use_filtered_paid_orders_and_exclude_cancelled_revenue(): void
    {
        DB::table('produk')->insert([
            ['id_produk' => 1, 'nama_produk' => 'Stroberi Terjual', 'harga_produk' => 12500, 'stok_produk' => 10],
            ['id_produk' => 2, 'nama_produk' => 'Selai Pesanan Batal', 'harga_produk' => 20000, 'stok_produk' => 10],
        ]);
        DB::table('pelanggan')->insert([
            ['id_pelanggan' => 1, 'nama_pelanggan' => 'Pelanggan Lunas'],
            ['id_pelanggan' => 2, 'nama_pelanggan' => 'Pelanggan Batal'],
            ['id_pelanggan' => 3, 'nama_pelanggan' => 'Pelanggan Menunggu'],
        ]);
        DB::table('pesanan')->insert([
            ['id_pesanan' => 1, 'id_pelanggan' => 1, 'tanggal_pemesanan' => '2026-05-10 10:00:00', 'total_harga' => 25000, 'ongkir' => 0, 'status_pesanan' => 'selesai'],
            ['id_pesanan' => 2, 'id_pelanggan' => 2, 'tanggal_pemesanan' => '2026-05-11 10:00:00', 'total_harga' => 80000, 'ongkir' => 0, 'status_pesanan' => 'dibatalkan'],
            ['id_pesanan' => 3, 'id_pelanggan' => 3, 'tanggal_pemesanan' => '2026-05-12 10:00:00', 'total_harga' => 12000, 'ongkir' => 0, 'status_pesanan' => 'menunggu'],
        ]);
        DB::table('pembayaran')->insert([
            ['id_pesanan' => 1, 'metode_pembayaran' => 'QRIS', 'jumlah_bayar' => 25000, 'status_pembayaran' => 'diverifikasi'],
            ['id_pesanan' => 2, 'metode_pembayaran' => 'QRIS', 'jumlah_bayar' => 80000, 'status_pembayaran' => 'diverifikasi'],
            ['id_pesanan' => 3, 'metode_pembayaran' => 'BCA', 'jumlah_bayar' => 12000, 'status_pembayaran' => 'menunggu'],
        ]);
        DB::table('detail_pesanan')->insert([
            ['id_pesanan' => 1, 'id_produk' => 1, 'jumlah' => 2, 'harga' => 12500, 'subtotal' => 25000],
            ['id_pesanan' => 2, 'id_produk' => 2, 'jumlah' => 4, 'harga' => 20000, 'subtotal' => 80000],
            ['id_pesanan' => 3, 'id_produk' => 1, 'jumlah' => 1, 'harga' => 12000, 'subtotal' => 12000],
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.reports', [
                'dari' => '2026-05-01',
                'sampai' => '2026-05-31',
                'periode' => 'bulanan',
            ]))
            ->assertOk()
            ->assertSee('class="card-value green">1</div>', false)
            ->assertSee('class="card-value">Rp 25.000</div>', false)
            ->assertSee('Pelanggan Batal')
            ->assertSee('Pelanggan Menunggu')
            ->assertSee('Stroberi Terjual')
            ->assertSee('2 pcs')
            ->assertDontSee('Selai Pesanan Batal')
            ->assertSee('Pendapatan Bulanan')
            ->assertSee('Jumlah Pesanan Bulanan');

        foreach (['harian', 'mingguan', 'bulanan', 'tahunan'] as $period) {
            $this->actingAsAdmin()
                ->get(route('admin.reports', [
                    'dari' => '2026-05-01',
                    'sampai' => '2026-05-31',
                    'periode' => $period,
                ]))
                ->assertOk()
                ->assertSee('Pendapatan '.ucfirst($period))
                ->assertSee('Jumlah Pesanan '.ucfirst($period));
        }

        $this->actingAsAdmin()
            ->get(route('admin.reports', [
                'dari' => '2026-05-01',
                'sampai' => '2026-05-31',
                'q' => 'Pelanggan Batal',
            ]))
            ->assertOk()
            ->assertSee('Pelanggan Batal')
            ->assertDontSee('Pelanggan Lunas')
            ->assertSee('class="card-value">Rp 0</div>', false);

        $this->actingAsAdmin()
            ->get(route('admin.reports', [
                'dari' => '2026-05-01',
                'sampai' => '2026-05-31',
                'urutkan' => 'total',
                'arah' => 'asc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Pelanggan Menunggu', 'Pelanggan Lunas', 'Pelanggan Batal']);
    }

    public function test_admin_report_shows_filtered_database_rows_and_exports_a_real_excel_file(): void
    {
        DB::table('pelanggan')->insert([
            ['id_pelanggan' => 1, 'nama_pelanggan' => 'Pelanggan Mei'],
            ['id_pelanggan' => 2, 'nama_pelanggan' => 'Pelanggan April'],
        ]);
        DB::table('pesanan')->insert([
            [
                'id_pesanan' => 1,
                'id_pelanggan' => 1,
                'tanggal_pemesanan' => '2026-05-10 10:00:00',
                'total_harga' => 25000,
                'status_pesanan' => 'diproses',
            ],
            [
                'id_pesanan' => 2,
                'id_pelanggan' => 2,
                'tanggal_pemesanan' => '2026-04-10 10:00:00',
                'total_harga' => 45000,
                'status_pesanan' => 'selesai',
            ],
        ]);
        DB::table('pembayaran')->insert([
            [
                'id_pesanan' => 1,
                'metode_pembayaran' => 'BCA',
                'jumlah_bayar' => 25000,
                'status_pembayaran' => 'diverifikasi',
            ],
            [
                'id_pesanan' => 2,
                'metode_pembayaran' => 'BRI',
                'jumlah_bayar' => 45000,
                'status_pembayaran' => 'diverifikasi',
            ],
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.reports', ['dari' => '2026-05-01', 'sampai' => '2026-05-31']))
            ->assertOk()
            ->assertSee('Laporan Penjualan')
            ->assertSee('class="report-hero"', false)
            ->assertDontSee('class="page-heading"', false)
            ->assertSee('Pendapatan Harian')
            ->assertSee('Detail Transaksi')
            ->assertSee(route('admin.reports.print', ['dari' => '2026-05-01', 'sampai' => '2026-05-31']))
            ->assertSee('Pelanggan Mei')
            ->assertSee('Jumlah produk')
            ->assertSee('Total pembayaran')
            ->assertSee('Rp 25.000')
            ->assertDontSee('Pelanggan April');

        $this->actingAsAdmin()
            ->get(route('admin.reports.print', ['dari' => '2026-05-01', 'sampai' => '2026-05-31']))
            ->assertOk()
            ->assertSee('Laporan Transaksi Penjualan')
            ->assertSee('Pelanggan Mei')
            ->assertDontSee('Grafik Penjualan')
            ->assertDontSee('Pelanggan April')
            ->assertDontSee('Navigasi admin');

        $response = $this->actingAsAdmin()
            ->get(route('admin.reports.export', [
                'dari' => '2026-05-01',
                'sampai' => '2026-05-31',
                'metode_pembayaran' => 'BCA',
                'status_pesanan' => 'diproses',
            ]))
            ->assertOk()
            ->assertDownload('laporan-penjualan-2026-05-01-2026-05-31.xlsx')
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $downloadResponse = $response->baseResponse;
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $downloadResponse);
        $workbook = new \PharData($downloadResponse->getFile()->getPathname(), 0, null, \Phar::ZIP);
        $sheet = $workbook['xl/worksheets/sheet1.xml']->getContent();
        $this->assertIsString($sheet);
        $this->assertStringContainsString('LAPORAN PENJUALAN', $sheet);
        $this->assertStringContainsString('Subtotal Produk (Rp)', $sheet);
        $this->assertStringContainsString('Ongkir (Rp)', $sheet);
        $sheetElementPositions = array_map(
            static fn (string $element): int|false => strpos($sheet, '<'.$element),
            ['dimension', 'sheetViews', 'sheetFormatPr', 'cols', 'sheetData', 'autoFilter', 'pageMargins']
        );
        $this->assertNotContains(false, $sheetElementPositions);
        $sortedPositions = $sheetElementPositions;
        sort($sortedPositions);
        $this->assertSame($sortedPositions, $sheetElementPositions);
        $this->assertStringContainsString('Pelanggan Mei', $sheet);
        $this->assertStringContainsString('BCA', $sheet);
        $this->assertStringNotContainsString('Pelanggan April', $sheet);
        $hasLogo = is_file(public_path('images/logo samudera.jpeg'));
        if ($hasLogo) {
            $contentTypes = $workbook['[Content_Types].xml']->getContent();
            $this->assertStringContainsString('<drawing r:id="rId1"/>', $sheet);
            $this->assertStringContainsString('Extension="jpg" ContentType="image/jpeg"', $contentTypes);
            $this->assertStringContainsString('/xl/drawings/drawing1.xml', $contentTypes);
        } else {
            $this->assertStringNotContainsString('<drawing ', $sheet);
        }
        $styles = $workbook['xl/styles.xml']->getContent();
        $this->assertIsString($styles);
        $this->assertStringContainsString('<font><name val="Arial"/><b/><color rgb="FFFFFFFF"/><sz val="11"/></font>', $styles);
        $xmlParts = [
            '[Content_Types].xml',
            '_rels/.rels',
            'xl/workbook.xml',
            'xl/_rels/workbook.xml.rels',
            'xl/styles.xml',
            'xl/worksheets/sheet1.xml',
        ];
        if ($hasLogo) {
            $xmlParts = array_merge($xmlParts, [
                'xl/worksheets/_rels/sheet1.xml.rels',
                'xl/drawings/drawing1.xml',
                'xl/drawings/_rels/drawing1.xml.rels',
            ]);
            $this->assertStringContainsString(
                'Target="../drawings/drawing1.xml"',
                $workbook['xl/worksheets/_rels/sheet1.xml.rels']->getContent()
            );
            $this->assertStringContainsString(
                'Target="../media/logo-samudera.jpg"',
                $workbook['xl/drawings/_rels/drawing1.xml.rels']->getContent()
            );
            $this->assertIsString($workbook['xl/media/logo-samudera.jpg']->getContent());
        }
        foreach ($xmlParts as $part) {
            $xml = $workbook[$part]->getContent();
            $this->assertIsString($xml);
            $this->assertNotFalse(simplexml_load_string($xml));
        }
        unset($workbook);
    }

    public function test_admin_sidebar_petugas_menu_opens_database_staff_listing(): void
    {
        DB::table('petugas')->insert([
            [
                'id_petugas' => 1,
                'nama_petugas' => 'Admin Uji',
                'username' => 'admin-uji',
                'password' => Hash::make('password123'),
                'no_telp' => '081234567890',
                'alamat' => 'Alamat Admin',
                'role' => 'Admin',
            ],
            [
                'id_petugas' => 2,
                'nama_petugas' => 'Kurir Uji',
                'username' => 'kurir-uji',
                'password' => Hash::make('password123'),
                'no_telp' => '081234567891',
                'alamat' => 'Alamat Kurir',
                'role' => 'Kurir',
            ],
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.staff'))
            ->assertOk()
            ->assertSee('Petugas')
            ->assertSee(route('admin.staff'))
            ->assertSee('Admin Uji')
            ->assertSee('Kurir Uji')
            ->assertSee('Alamat Kurir');
    }

    public function test_admin_can_add_admin_or_courier_staff_with_hashed_password(): void
    {
        $this->actingAsAdmin()
            ->get(route('admin.staff'))
            ->assertOk()
            ->assertSee('Tambah Petugas Baru')
            ->assertSee(route('admin.staff.store'));

        $this->actingAsAdmin()
            ->post(route('admin.staff.store'), [
                'nama_petugas' => 'Kurir Baru',
                'username' => 'kurir-baru',
                'password' => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
                'no_telp' => '081234567892',
                'alamat' => 'Ciwidey',
                'role' => 'Kurir',
            ])
            ->assertRedirect(route('admin.staff'))
            ->assertSessionHas('success');

        $staff = DB::table('petugas')->where('username', 'kurir-baru')->first();
        $this->assertNotNull($staff);
        $this->assertSame('Kurir', $staff->role);
        $this->assertTrue(Hash::check('password-baru-123', $staff->password));
        $this->assertNotSame('password-baru-123', $staff->password);

        $this->actingAsAdmin()
            ->post(route('admin.staff.store'), [
                'nama_petugas' => 'Admin Baru',
                'username' => 'admin-baru',
                'password' => 'password-admin-123',
                'password_confirmation' => 'password-admin-123',
                'role' => 'Admin',
            ])
            ->assertRedirect(route('admin.staff'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('petugas', [
            'username' => 'admin-baru',
            'role' => 'Admin',
        ]);
    }

    public function test_admin_cannot_add_staff_with_duplicate_username_invalid_role_or_mismatched_password(): void
    {
        DB::table('petugas')->insert([
            'id_petugas' => 1,
            'nama_petugas' => 'Admin Uji',
            'username' => 'admin-sama',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
        ]);

        $this->actingAsAdmin()
            ->post(route('admin.staff.store'), [
                'nama_petugas' => 'Petugas Duplikat',
                'username' => 'admin-sama',
                'password' => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
                'role' => 'Kurir',
            ])
            ->assertSessionHasErrors('username');

        $this->actingAsAdmin()
            ->post(route('admin.staff.store'), [
                'nama_petugas' => 'Role Tidak Valid',
                'username' => 'role-invalid',
                'password' => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
                'role' => 'Pelanggan',
            ])
            ->assertSessionHasErrors('role');

        $this->actingAsAdmin()
            ->post(route('admin.staff.store'), [
                'nama_petugas' => 'Password Tidak Cocok',
                'username' => 'password-invalid',
                'password' => 'password-baru-123',
                'password_confirmation' => 'password-berbeda',
                'role' => 'Kurir',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseCount('petugas', 1);
    }

    public function test_admin_profile_save_redirects_to_the_profile_with_saved_details_visible(): void
    {
        DB::table('petugas')->insert([
            'id_petugas' => 1,
            'nama_petugas' => 'Admin Uji',
            'username' => 'admin-uji',
            'password' => Hash::make('password123'),
            'no_telp' => '081234567890',
            'alamat' => 'Alamat Lama',
            'role' => 'Admin',
        ]);

        $this->actingAsAdmin()
            ->put(route('admin.profile.update'), [
                'nama_petugas' => 'Admin Baru',
                'username' => 'admin-baru',
                'no_telp' => '081234567891',
                'alamat' => 'Alamat Baru',
            ])
            ->assertRedirect(route('admin.profile'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('petugas', [
            'id_petugas' => 1,
            'nama_petugas' => 'Admin Baru',
            'username' => 'admin-baru',
            'no_telp' => '081234567891',
            'alamat' => 'Alamat Baru',
        ]);

        $this->actingAsAdmin()
            ->get(route('admin.profile'))
            ->assertOk()
            ->assertSee('Profil Tersimpan')
            ->assertSee('Admin Baru')
            ->assertSee('admin-baru')
            ->assertSee('Alamat Baru');
    }

    public function test_authenticated_admin_can_open_and_submit_a_password_recovery_request(): void
    {
        DB::table('petugas')->insert([
            'id_petugas' => 1,
            'nama_petugas' => 'Admin Uji',
            'username' => 'admin-uji',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'status_akun' => 'Aktif',
        ]);

        $this->withSession([
            'id_petugas' => 1,
            'nama_petugas' => 'Admin Uji',
            'role' => 'Admin',
        ])->get(route('admin.password.request'))
            ->assertOk()
            ->assertSee('Username Admin');

        $this->post(route('admin.password.verify'), [
            'username' => 'admin-uji',
        ])->assertRedirect(route('admin.password.request'))
            ->assertCookie('admin_password_recovery_v2');

        $this->assertDatabaseHas('permintaan_reset_password_admin', [
            'admin_id' => 1,
            'status' => 'Menunggu Verifikasi',
        ]);
    }

    public function test_admin_password_recovery_form_expires_a_stale_recovery_cookie(): void
    {
        $this->withCookie('admin_password_recovery_v2', 'stale-token')
            ->get(route('admin.password.request'))
            ->assertOk()
            ->assertCookieExpired('admin_password_recovery_v2');
    }

    public function test_admin_password_recovery_requires_another_admin_and_hashes_the_new_password(): void
    {
        DB::table('petugas')->insert([
            [
                'id_petugas' => 1,
                'nama_petugas' => 'Admin Penerbit',
                'username' => 'admin-penerbit',
                'password' => Hash::make('password123'),
                'role' => 'Admin',
            ],
            [
                'id_petugas' => 2,
                'nama_petugas' => 'Admin Pemilik',
                'username' => 'admin-pemilik',
                'password' => Hash::make('password123'),
                'role' => 'Admin',
            ],
        ]);

        $this->get(route('admin.password.request'))
            ->assertOk()
            ->assertSee('Admin lain');

        $requestResponse = $this->post(route('admin.password.verify'), [
            'username' => 'admin-pemilik',
        ]);
        $requestResponse->assertRedirect(route('admin.password.request'))
            ->assertCookie('admin_password_recovery_v2');
        $recoveryCookie = $requestResponse->getCookie('admin_password_recovery_v2');
        $this->assertNotNull($recoveryCookie);
        $this->withCookie('admin_password_recovery_v2', $recoveryCookie->getValue());

        $recoveryRequest = DB::table('permintaan_reset_password_admin')->first();
        $this->assertSame('Menunggu Verifikasi', $recoveryRequest->status);
        $this->assertSame(64, strlen($recoveryRequest->token_hash));

        $this->withSession([
            'id_petugas' => 2,
            'nama_petugas' => 'Admin Pemilik',
            'role' => 'Admin',
        ])->put(route('admin.password-recovery.approve', $recoveryRequest->id))
            ->assertRedirect(route('admin.staff'))
            ->assertSessionHas('error');
        $this->assertSame(
            'Menunggu Verifikasi',
            DB::table('permintaan_reset_password_admin')->value('status')
        );

        $this->withSession([
            'id_petugas' => 1,
            'nama_petugas' => 'Admin Penerbit',
            'role' => 'Admin',
        ])->get(route('admin.staff'))
            ->assertOk()
            ->assertSee('Setujui Pemulihan')
            ->assertSee('admin-pemilik');

        $this->put(route('admin.password-recovery.approve', $recoveryRequest->id))
            ->assertRedirect(route('admin.staff'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('permintaan_reset_password_admin', [
            'id' => $recoveryRequest->id,
            'status' => 'Disetujui',
            'reviewer_id' => 1,
        ]);

        $this->post(route('admin.logout'))
            ->assertRedirect(route('admin.password.reset'))
            ->assertSessionMissing('role');

        $this->withSession([
            'id_petugas' => 2,
            'nama_petugas' => 'Admin Pemilik',
            'role' => 'Admin',
        ])->get(route('admin.password.reset'))
            ->assertOk()
            ->assertSee('sudah disetujui Admin lain');

        $resetResponse = $this->post(route('admin.password.update'), [
            'password' => 'password-baru-456',
            'password_confirmation' => 'password-baru-456',
        ]);
        $resetResponse->assertRedirect(route('login'))
            ->assertCookieExpired('admin_password_recovery_v2')
            ->assertSessionMissing('role');

        $updatedAdmin = DB::table('petugas')->where('id_petugas', 2)->first();
        $this->assertTrue(Hash::check('password-baru-456', $updatedAdmin->password));
        $this->assertNotSame('password-baru-456', $updatedAdmin->password);
        $this->assertDatabaseHas('permintaan_reset_password_admin', [
            'id' => $recoveryRequest->id,
            'status' => 'Selesai',
            'token_hash' => null,
        ]);
        $this->assertTrue(
            $this->post(route('admin.password.update'), [
                'password' => 'password-baru-456',
                'password_confirmation' => 'password-baru-456',
            ])->isRedirect(route('admin.password.request'))
        );

        $secondRequestResponse = $this->post(route('admin.password.verify'), [
            'username' => 'admin-penerbit',
        ]);
        $secondRequestResponse->assertRedirect(route('admin.password.request'))
            ->assertCookie('admin_password_recovery_v2');
        $secondRecoveryRequest = DB::table('permintaan_reset_password_admin')
            ->where('admin_id', 1)
            ->latest('id')
            ->first();

        $secondRecoveryCookie = $secondRequestResponse->getCookie('admin_password_recovery_v2');
        $this->assertNotNull($secondRecoveryCookie);
        $this->withCookie('admin_password_recovery_v2', $secondRecoveryCookie->getValue());

        $this->withSession([
            'id_petugas' => 2,
            'nama_petugas' => 'Admin Pemilik',
            'role' => 'Admin',
        ])->put(route('admin.password-recovery.approve', $secondRecoveryRequest->id))
            ->assertRedirect(route('admin.staff'));

        $this->post(route('admin.logout'))
            ->assertRedirect(route('admin.password.reset'));
        $this->flushSession();
        $this->get(route('admin.password.reset'))->assertOk();
        $this->post(route('admin.password.update'), [
            'password' => 'password-baru-789',
            'password_confirmation' => 'password-baru-789',
        ])->assertRedirect(route('login'));

        $updatedReviewer = DB::table('petugas')->where('id_petugas', 1)->first();
        $this->assertTrue(Hash::check('password-baru-789', $updatedReviewer->password));
    }

    public function test_non_admin_cannot_approve_admin_password_recovery(): void
    {
        DB::table('petugas')->insert([
            [
                'id_petugas' => 1,
                'nama_petugas' => 'Admin Pemilik',
                'username' => 'admin-pemilik',
                'password' => Hash::make('password123'),
                'role' => 'Admin',
            ],
            [
                'id_petugas' => 2,
                'nama_petugas' => 'Kurir Uji',
                'username' => 'kurir-uji',
                'password' => Hash::make('password123'),
                'role' => 'Kurir',
            ],
        ]);
        $requestId = DB::table('permintaan_reset_password_admin')->insertGetId([
            'admin_id' => 1,
            'token_hash' => hash('sha256', 'a'.str_repeat('b', 63)),
            'status' => 'Menunggu Verifikasi',
            'requested_at' => now(),
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->withSession([
            'id_petugas' => 2,
            'nama_petugas' => 'Kurir Uji',
            'role' => 'Kurir',
        ])->put(route('admin.password-recovery.approve', $requestId))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('permintaan_reset_password_admin', [
            'id' => $requestId,
            'status' => 'Menunggu Verifikasi',
        ]);
    }

    private function actingAsAdmin(): static
    {
        return $this->withSession([
            'id_petugas' => 1,
            'nama_petugas' => 'Admin Uji',
            'role' => 'Admin',
        ]);
    }
}
