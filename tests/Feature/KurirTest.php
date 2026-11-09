<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KurirTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('petugas', function (Blueprint $table) {
            $table->increments('id_petugas');
            $table->string('nama_petugas');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('no_telp', 15)->nullable();
            $table->text('alamat')->nullable();
            $table->string('role');
            $table->string('status_akun', 30)->default('Aktif');
            $table->dateTime('password_reset_requested_at')->nullable();
            $table->string('password_reset_status', 30)->nullable();
            $table->boolean('must_change_password')->default(false);
        });

        Schema::create('pelanggan', function (Blueprint $table) {
            $table->increments('id_pelanggan');
            $table->string('nama_pelanggan');
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->string('status_pesanan')->default('menunggu');
            $table->string('nama_penerima')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->text('catatan')->nullable();
            $table->decimal('total_harga', 12, 2)->nullable();
            $table->dateTime('tanggal_pemesanan')->nullable();
            $table->decimal('ongkir', 12, 2)->nullable()->default(0);
        });

        Schema::create('pengiriman', function (Blueprint $table) {
            $table->increments('id_pengiriman');
            $table->integer('id_pesanan');
            $table->integer('id_petugas');
            $table->string('nama_penerima')->nullable();
            $table->string('no_telp_penerima')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->string('status_pengiriman');
            $table->dateTime('tanggal_pengiriman')->nullable();
        });
    }

    public function test_courier_login_and_pages_use_the_existing_petugas_account(): void
    {
        DB::table('petugas')->insert([
            'id_petugas' => 1,
            'nama_petugas' => 'Kurir Uji',
            'username' => 'kurir-uji',
            'password' => Hash::make('password123'),
            'role' => 'Kurir',
        ]);

        $this->get(route('kurir.login'))
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('login.process'))
            ->assertSee('Email / Username')
            ->assertSee('Masukkan email atau username');

        $this->post(route('kurir.login.process'), [
            'username' => 'kurir-uji',
            'password' => 'password123',
        ])
            ->assertRedirect(route('kurir.dashboard'))
            ->assertSessionHas('id_petugas', 1)
            ->assertSessionHas('role', 'Kurir');

        $this->get(route('kurir.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Kurir')
            ->assertSee('Daftar Tujuan');

        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->post(route('kurir.logout'))
            ->assertRedirect(route('login'))
            ->assertSessionMissing('role');
    }

    public function test_courier_can_request_an_account_but_cannot_login_before_admin_approval(): void
    {
        $this->get(route('kurir.login'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.login'))
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('kurir.register'));

        $this->get(route('kurir.register'))
            ->assertOk()
            ->assertSee('disetujui oleh Admin');

        $this->post(route('kurir.register.process'), [
            'nama_petugas' => 'Kurir Permohonan',
            'username' => 'kurir-permohonan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_telp' => '081234567890',
            'alamat' => 'Ciwidey',
        ])
            ->assertRedirect(route('kurir.login'))
            ->assertSessionHas('success');

        $courier = DB::table('petugas')->where('username', 'kurir-permohonan')->first();
        $this->assertNotNull($courier);
        $this->assertSame('Kurir', $courier->role);
        $this->assertSame('Menunggu Persetujuan', $courier->status_akun);
        $this->assertTrue(Hash::check('password123', $courier->password));

        $this->post(route('kurir.login.process'), [
            'username' => 'kurir-permohonan',
            'password' => 'password123',
        ])
            ->assertRedirect()
            ->assertSessionHas('error', 'Akun Kurir Anda masih menunggu persetujuan Admin.');

        $this->post(route('kurir.register.process'), [
            'nama_petugas' => 'Kurir Duplikat',
            'username' => 'kurir-permohonan',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasErrors('username');
    }

    public function test_courier_reset_request_is_generic_and_admin_approval_forces_password_change(): void
    {
        DB::table('petugas')->insert([
            'id_petugas' => 1,
            'nama_petugas' => 'Kurir Uji',
            'username' => 'kurir-uji',
            'password' => Hash::make('password123'),
            'role' => 'Kurir',
            'status_akun' => 'Aktif',
        ]);

        $this->get(route('kurir.password-reset.request'))
            ->assertOk()
            ->assertSee('Ajukan Reset Password');

        $this->post(route('kurir.password-reset.submit'), ['username' => 'tidak-terdaftar'])
            ->assertRedirect(route('kurir.password-reset.request'))
            ->assertSessionHas('success');

        $this->post(route('kurir.password-reset.submit'), ['username' => 'kurir-uji'])
            ->assertRedirect(route('kurir.password-reset.request'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('petugas', [
            'id_petugas' => 1,
            'password_reset_status' => 'Menunggu Verifikasi',
        ]);

        $this->withSession(['id_petugas' => 10, 'nama_petugas' => 'Admin', 'role' => 'Admin'])
            ->put(route('admin.staff.password-reset.approve', 1))
            ->assertRedirect(route('admin.staff'))
            ->assertSessionHas('courier_temporary_password');

        $temporaryPassword = session('courier_temporary_password');
        $courier = DB::table('petugas')->where('id_petugas', 1)->first();
        $this->assertTrue(Hash::check($temporaryPassword, $courier->password));

        $this->post(route('login.process'), [
            'username' => 'kurir-uji',
            'password' => $temporaryPassword,
        ])
            ->assertRedirect(route('kurir.password.change'))
            ->assertSessionHas('must_change_password', true);

        $this->get(route('kurir.dashboard'))->assertRedirect(route('kurir.password.change'));

        $this->post(route('kurir.password.update-temporary'), [
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])
            ->assertRedirect(route('kurir.dashboard'));

        $this->assertTrue(Hash::check(
            'password-baru-123',
            DB::table('petugas')->where('id_petugas', 1)->value('password')
        ));
        $this->assertDatabaseHas('petugas', [
            'id_petugas' => 1,
            'must_change_password' => false,
        ]);
    }

    public function test_courier_can_only_view_shipments_assigned_to_their_account(): void
    {
        $this->seedCouriersAndOrders();

        $this->actingAsCourier()
            ->get(route('kurir.shipments'))
            ->assertOk()
            ->assertSee('Pesanan #SP000001')
            ->assertDontSee('Pesanan #SP000002');

        $this->actingAsCourier()
            ->get(route('kurir.shipments.show', 1))
            ->assertOk()
            ->assertSee('Penerima 1');

        $this->actingAsCourier()
            ->get(route('kurir.shipments.show', 2))
            ->assertNotFound();
    }

    public function test_courier_profile_saves_and_shows_updated_database_details(): void
    {
        $this->seedCouriersAndOrders();

        $this->actingAsCourier()
            ->get(route('kurir.profile'))
            ->assertOk()
            ->assertSee('Profil Kurir')
            ->assertSee('Profil Tersimpan')
            ->assertSee('Kurir Satu');

        $this->actingAsCourier()
            ->put(route('kurir.profile.update'), [
                'nama_petugas' => 'Kurir Satu Baru',
                'username' => 'kurir-satu-baru',
                'no_telp' => '081234567890',
                'alamat' => 'Jalan Stroberi, Ciwidey',
            ])
            ->assertRedirect(route('kurir.profile'))
            ->assertSessionHas('success')
            ->assertSessionHas('nama_petugas', 'Kurir Satu Baru');

        $this->assertDatabaseHas('petugas', [
            'id_petugas' => 1,
            'nama_petugas' => 'Kurir Satu Baru',
            'username' => 'kurir-satu-baru',
            'no_telp' => '081234567890',
            'alamat' => 'Jalan Stroberi, Ciwidey',
            'role' => 'Kurir',
        ]);

        $this->actingAsCourier()
            ->get(route('kurir.profile'))
            ->assertOk()
            ->assertSee('Kurir Satu Baru')
            ->assertSee('Jalan Stroberi, Ciwidey');
    }

    public function test_courier_can_advance_shipment_and_confirm_delivery_in_database(): void
    {
        $this->seedCouriersAndOrders();

        $this->actingAsCourier()
            ->put(route('kurir.shipments.update', 1), ['status_pengiriman' => 'diproses'])
            ->assertRedirect(route('kurir.shipments.show', 1));

        $this->actingAsCourier()
            ->put(route('kurir.shipments.update', 1), ['status_pengiriman' => 'dikirim'])
            ->assertRedirect(route('kurir.shipments.show', 1));

        $this->assertDatabaseHas('pengiriman', [
            'id_pengiriman' => 1,
            'status_pengiriman' => 'dikirim',
        ]);
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'status_pesanan' => 'diproses',
        ]);

        $this->actingAsCourier()
            ->put(route('kurir.shipments.update', 1), ['status_pengiriman' => 'diterima'])
            ->assertRedirect(route('kurir.confirmation', 1));

        $this->assertDatabaseHas('pengiriman', [
            'id_pengiriman' => 1,
            'status_pengiriman' => 'diterima',
        ]);
        $this->assertDatabaseHas('pesanan', [
            'id_pesanan' => 1,
            'status_pesanan' => 'selesai',
        ]);

        $this->actingAsCourier()
            ->get(route('kurir.confirmation', 1))
            ->assertOk()
            ->assertSee('Pesanan Diterima')
            ->assertSee('#SP000001');
    }

    private function seedCouriersAndOrders(): void
    {
        DB::table('petugas')->insert([
            ['id_petugas' => 1, 'nama_petugas' => 'Kurir Satu', 'username' => 'kurir-satu', 'password' => Hash::make('password123'), 'role' => 'Kurir', 'no_telp' => null, 'alamat' => null],
            ['id_petugas' => 2, 'nama_petugas' => 'Kurir Dua', 'username' => 'kurir-dua', 'password' => Hash::make('password123'), 'role' => 'Kurir', 'no_telp' => null, 'alamat' => null],
        ]);

        DB::table('pesanan')->insert([
            ['id_pesanan' => 1, 'status_pesanan' => 'menunggu', 'nama_penerima' => 'Penerima 1', 'no_hp' => '0811111111', 'alamat_pengiriman' => 'Jalan Mawar, Ciwidey', 'catatan' => null, 'total_harga' => 25000],
            ['id_pesanan' => 2, 'status_pesanan' => 'menunggu', 'nama_penerima' => 'Penerima 2', 'no_hp' => '0822222222', 'alamat_pengiriman' => 'Jalan Melati, Soreang', 'catatan' => null, 'total_harga' => 30000],
        ]);

        DB::table('pengiriman')->insert([
            ['id_pengiriman' => 1, 'id_pesanan' => 1, 'id_petugas' => 1, 'nama_penerima' => 'Penerima 1', 'no_telp_penerima' => '0811111111', 'alamat_pengiriman' => 'Jalan Mawar, Ciwidey', 'status_pengiriman' => 'menunggu'],
            ['id_pengiriman' => 2, 'id_pesanan' => 2, 'id_petugas' => 2, 'nama_penerima' => 'Penerima 2', 'no_telp_penerima' => '0822222222', 'alamat_pengiriman' => 'Jalan Melati, Soreang', 'status_pengiriman' => 'menunggu'],
        ]);
    }

    private function actingAsCourier(): static
    {
        return $this->withSession([
            'id_petugas' => 1,
            'nama_petugas' => 'Kurir Satu',
            'role' => 'Kurir',
        ]);
    }
}
