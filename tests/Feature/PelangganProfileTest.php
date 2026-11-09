<?php

namespace Tests\Feature;

use App\Mail\CustomerEmailVerification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PelangganProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('pelanggan', function (Blueprint $table) {
            $table->increments('id_pelanggan');
            $table->string('nama_pelanggan');
            $table->string('username')->unique();
            $table->string('email')->nullable()->unique();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('password');
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->integer('id_pelanggan');
            $table->dateTime('tanggal_pemesanan')->nullable();
            $table->decimal('ongkir', 12, 2)->nullable()->default(0);
            $table->decimal('total_harga', 12, 2)->nullable();
            $table->string('status_pesanan')->nullable();
            $table->string('nama_penerima')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->string('no_hp', 20)->nullable();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id_pembayaran');
            $table->integer('id_pesanan');
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->string('metode_pembayaran')->nullable();
        });

        DB::table('pelanggan')->insert([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Siti Aisyah',
            'username' => 'siti',
            'email' => 'siti@example.com',
            'password' => Hash::make('password123'),
        ]);

        DB::table('pesanan')->insert([
            'id_pesanan' => 1,
            'id_pelanggan' => 1,
            'tanggal_pemesanan' => now(),
            'total_harga' => 45000,
            'status_pesanan' => 'diproses',
            'nama_penerima' => 'Siti Aisyah',
            'alamat_pengiriman' => 'Jalan Mawar, Ciwidey',
            'no_hp' => '081234567890',
        ]);

        DB::table('pembayaran')->insert([
            'id_pembayaran' => 1,
            'id_pesanan' => 1,
            'tanggal_pembayaran' => now(),
            'metode_pembayaran' => 'BCA',
        ]);
    }

    public function test_profile_page_uses_customer_navigation_and_shows_their_account_order_address_and_payment(): void
    {
        $this->actingAsCustomer()
            ->get(route('pelanggan.profile'))
            ->assertOk()
            ->assertSee('Profil Pelanggan')
            ->assertSee('Siti Aisyah')
            ->assertDontSee('Email tidak ditambahkan')
            ->assertSee('Pesanan Saya')
            ->assertSee('Alamat Saya')
            ->assertSee('Metode Pembayaran')
            ->assertSee('Pengaturan Akun')
            ->assertSee(route('pelanggan.profile.update'))
            ->assertSee('Profil');
    }

    public function test_customer_can_update_their_profile_and_password_in_the_database(): void
    {
        $this->actingAsCustomer()
            ->put(route('pelanggan.profile.update'), [
                'nama_pelanggan' => 'Siti Aisyah Baru',
                'username' => 'siti-baru',
                'email' => 'siti-baru@example.com',
                'current_password' => 'password123',
                'password' => 'newpassword456',
                'password_confirmation' => 'newpassword456',
            ])
            ->assertRedirect(route('pelanggan.profile'))
            ->assertSessionHas('success')
            ->assertSessionHas('nama_pelanggan', 'Siti Aisyah Baru');

        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Siti Aisyah Baru',
            'username' => 'siti-baru',
            'email' => 'siti-baru@example.com',
        ]);
        $this->assertTrue(Hash::check('newpassword456', DB::table('pelanggan')->where('id_pelanggan', 1)->value('password')));
    }

    public function test_customer_without_email_can_update_their_profile(): void
    {
        DB::table('pelanggan')->where('id_pelanggan', 1)->update(['email' => null]);

        $this->actingAsCustomer()
            ->put(route('pelanggan.profile.update'), [
                'nama_pelanggan' => 'Siti Aisyah',
                'username' => 'siti',
                'email' => '',
            ])
            ->assertRedirect(route('pelanggan.profile'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 1,
            'email' => null,
        ]);
    }

    public function test_changed_email_must_be_verified_before_it_can_receive_recovery_codes(): void
    {
        Mail::fake();

        $this->actingAsCustomer()
            ->put(route('pelanggan.profile.update'), [
                'nama_pelanggan' => 'Siti Aisyah',
                'username' => 'siti',
                'email' => 'siti-baru@example.com',
            ])
            ->assertRedirect(route('pelanggan.profile'))
            ->assertSessionHas('success');

        $customer = DB::table('pelanggan')->where('id_pelanggan', 1)->first();
        $this->assertNull($customer->email_verified_at);

        $verificationUrl = null;
        Mail::assertSent(CustomerEmailVerification::class, function (CustomerEmailVerification $mail) use (&$verificationUrl): bool {
            $verificationUrl = $mail->verificationUrl;

            return $mail->hasTo('siti-baru@example.com');
        });

        $this->get($verificationUrl)
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->assertNotNull(
            DB::table('pelanggan')->where('id_pelanggan', 1)->value('email_verified_at')
        );
    }

    public function test_customer_cannot_change_password_without_the_correct_current_password(): void
    {
        $this->actingAsCustomer()
            ->put(route('pelanggan.profile.update'), [
                'nama_pelanggan' => 'Siti Aisyah',
                'username' => 'siti',
                'email' => 'siti@example.com',
                'current_password' => 'wrong-password',
                'password' => 'newpassword456',
                'password_confirmation' => 'newpassword456',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password123', DB::table('pelanggan')->where('id_pelanggan', 1)->value('password')));
    }

    private function actingAsCustomer(): static
    {
        return $this->withSession([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Siti Aisyah',
            'role' => 'Pelanggan',
        ]);
    }
}
