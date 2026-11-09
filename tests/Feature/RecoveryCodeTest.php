<?php

namespace Tests\Feature;

use App\Support\RecoveryCode;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RecoveryCodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('pelanggan', function (Blueprint $table) {
            $table->increments('id_pelanggan');
            $table->string('nama_pelanggan');
            $table->string('email', 100)->nullable();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('recovery_code_hash')->nullable();
            $table->string('recovery_code_fingerprint', 64)->nullable()->unique();
            $table->dateTime('recovery_code_expires_at')->nullable();
            $table->dateTime('recovery_code_used_at')->nullable();
            $table->unsignedTinyInteger('recovery_code_attempts')->default(0);
            $table->dateTime('recovery_code_locked_until')->nullable();
        });

        Schema::create('petugas', function (Blueprint $table) {
            $table->increments('id_petugas');
            $table->string('nama_petugas')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('role')->nullable();
            $table->string('status_akun')->nullable();
            $table->dateTime('password_reset_requested_at')->nullable();
            $table->string('password_reset_status', 30)->nullable();
            $table->boolean('must_change_password')->default(false);
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->unsignedInteger('id_pelanggan');
            $table->dateTime('tanggal_pemesanan')->nullable();
            $table->decimal('ongkir', 12, 2)->nullable()->default(0);
            $table->text('alamat_pengiriman')->nullable();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id_pembayaran');
            $table->unsignedInteger('id_pesanan');
            $table->string('metode_pembayaran')->nullable();
            $table->dateTime('tanggal_pembayaran')->nullable();
        });
    }

    public function test_correct_username_and_recovery_code_reset_password_and_allow_login(): void
    {
        $this->createCustomer();
        $this->withSession([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Pelanggan Uji 1',
            'role' => 'Pelanggan',
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Lupa Password?')
            ->assertSee(route('password.request'));

        $this->get(route('password.request'))
            ->assertRedirect(route('pelanggan.recovery-code'));

        $this->get(route('pelanggan.recovery-code'))
            ->assertOk()
            ->assertSee('Terbitkan Kode Pemulihan')
            ->assertDontSee('Pesanan Saya')
            ->assertDontSee('Alamat Saya')
            ->assertDontSee('Metode Pembayaran')
            ->assertDontSee('Pengaturan Akun')
            ->assertDontSee('Keluar')
            ->assertDontSee('Password saat ini');

        $issueResponse = $this->post(route('pelanggan.recovery-code.issue'));
        $issueResponse->assertRedirect(route('pelanggan.recovery-code'))
            ->assertSessionHas('recovery_code')
            ->assertSessionHas('success');
        $code = $issueResponse->getSession()->get('recovery_code');
        $this->assertIsString($code);
        $expiresAt = Carbon::parse(
            DB::table('pelanggan')->where('id_pelanggan', 1)->value('recovery_code_expires_at')
        );
        $this->assertGreaterThan(now(), $expiresAt);
        $this->assertLessThanOrEqual(now()->addDays(RecoveryCode::VALIDITY_DAYS)->addSecond(), $expiresAt);

        $this->get(route('pelanggan.recovery-code'))
            ->assertOk()
            ->assertSee($code)
            ->assertSee('30 hari')
            ->assertSee('Selesai, lanjut ke Lupa Password');

        $this->post(route('pelanggan.recovery-code.continue'))
            ->assertRedirect(route('password.request'))
            ->assertSessionMissing('role')
            ->assertSessionHas('info');

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Username')
            ->assertSee('name="username"', false)
            ->assertSee('name="recovery_code"', false)
            ->assertSee('Kode berlaku selama 30 hari')
            ->assertDontSee('email terverifikasi');
        $this->post(route('password.verify'), [
            'username' => 'siti-uji',
            'recovery_code' => $code,
        ])->assertRedirect(route('password.reset'));

        $this->get(route('password.reset'))
            ->assertOk()
            ->assertSee('Buat Password Baru');

        $this->from(route('password.reset'))
            ->post(route('password.update'), [
                'password' => 'password-baru-123',
                'password_confirmation' => 'password-berbeda',
            ])
            ->assertRedirect(route('password.reset'))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 1,
            'recovery_code_used_at' => null,
        ]);

        $this->post(route('password.update'), [
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $customer = DB::table('pelanggan')->where('id_pelanggan', 1)->first();
        $normalizedCode = RecoveryCode::normalize($code);
        $this->assertSame(12, strlen($normalizedCode));
        $this->assertTrue(Hash::check('password-baru-123', $customer->password));
        $this->assertFalse(Hash::check('password-lama-123', $customer->password));
        $this->assertNull($customer->recovery_code_hash);
        $this->assertNotNull($customer->recovery_code_used_at);
        $this->assertNotNull($customer->recovery_code_fingerprint);

        $this->flushSession();
        $this->post(route('password.verify'), [
            'username' => 'siti-uji',
            'recovery_code' => $code,
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('error');

        $this->post(route('login.process'), [
            'username' => 'siti-uji',
            'password' => 'password-lama-123',
        ])
            ->assertSessionHas('error');

        $this->post(route('login.process'), [
            'username' => 'siti-uji',
            'password' => 'password-baru-123',
        ])
            ->assertRedirect('/onboarding')
            ->assertSessionHas('id_pelanggan', 1)
            ->assertSessionHas('role', 'Pelanggan')
            ->assertSessionMissing('recovery_code_issued');

        $this->get('/onboarding')
            ->assertOk()
            ->assertDontSee('Kode pemulihan kamu')
            ->assertDontSee('STB-');
    }

    public function test_recovery_code_normalization_preserves_raw_codes_starting_with_stb(): void
    {
        $this->assertSame(
            'ABCDEFGHJKLM',
            RecoveryCode::normalize('STB-ABCD-EFGH-JKLM')
        );
        $this->assertSame(
            'STBABCDEFGHJK',
            RecoveryCode::normalize('STBABCDEFGHJK')
        );
    }

    public function test_existing_customer_login_does_not_issue_or_display_a_recovery_code(): void
    {
        $this->createCustomer();

        $this->post(route('login.process'), [
            'username' => 'siti-uji',
            'password' => 'password-lama-123',
        ])
            ->assertRedirect('/onboarding')
            ->assertSessionMissing('recovery_code_issued');
        $customer = DB::table('pelanggan')->where('id_pelanggan', 1)->first();

        $this->assertNull($customer->recovery_code_hash);
        $this->assertNull($customer->recovery_code_fingerprint);

        $this->get('/onboarding')
            ->assertOk()
            ->assertDontSee('Kode pemulihan kamu')
            ->assertDontSee('STB-');
    }

    public function test_login_does_not_replace_an_existing_active_recovery_code(): void
    {
        $this->createCustomer();
        $code = RecoveryCode::issueForCustomer(1);
        $hashBefore = DB::table('pelanggan')->where('id_pelanggan', 1)->value('recovery_code_hash');

        $this->post(route('login.process'), [
            'username' => 'siti-uji',
            'password' => 'password-lama-123',
        ])
            ->assertRedirect('/onboarding')
            ->assertSessionMissing('recovery_code_issued');

        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 1,
            'recovery_code_hash' => $hashBefore,
            'recovery_code_fingerprint' => RecoveryCode::fingerprint($code),
        ]);
    }

    public function test_wrong_recovery_code_and_unknown_username_have_generic_responses(): void
    {
        $this->createCustomer();
        RecoveryCode::issueForCustomer(1);

        $this->post(route('password.verify'), [
            'username' => 'siti-uji',
            'recovery_code' => 'AAAAAAAAAAAA',
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('error', 'Username atau kode pemulihan tidak valid, kedaluwarsa, sudah digunakan, atau sementara dikunci.');

        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 1,
            'recovery_code_attempts' => 1,
        ]);

        $this->post(route('password.verify'), [
            'username' => 'username-tidak-ada',
            'recovery_code' => 'AAAAAAAAAAAA',
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('error', 'Username atau kode pemulihan tidak valid, kedaluwarsa, sudah digunakan, atau sementara dikunci.');
    }

    public function test_expired_recovery_code_is_rejected(): void
    {
        $this->createCustomer();
        $code = RecoveryCode::issueForCustomer(1);
        DB::table('pelanggan')
            ->where('id_pelanggan', 1)
            ->update(['recovery_code_expires_at' => now()->subMinute()]);

        $this->post(route('password.verify'), [
            'username' => 'siti-uji',
            'recovery_code' => $code,
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 1,
            'recovery_code_used_at' => null,
        ]);
    }

    public function test_repeated_wrong_codes_lock_the_recovery_code_temporarily(): void
    {
        $this->createCustomer();
        RecoveryCode::issueForCustomer(1);

        for ($attempt = 0; $attempt < RecoveryCode::MAX_ATTEMPTS; $attempt++) {
            $this->post(route('password.verify'), [
                'username' => 'siti-uji',
                'recovery_code' => 'AAAAAAAAAAAA',
            ])->assertSessionHas('error');
        }

        $customer = DB::table('pelanggan')->where('id_pelanggan', 1)->first();
        $this->assertSame(RecoveryCode::MAX_ATTEMPTS, (int) $customer->recovery_code_attempts);
        $this->assertNotNull($customer->recovery_code_locked_until);
    }

    public function test_customer_profile_links_to_a_recovery_only_code_page(): void
    {
        $this->createCustomer();
        $this->withSession([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Pelanggan Uji 1',
            'role' => 'Pelanggan',
        ])
            ->get(route('pelanggan.profile'))
            ->assertOk()
            ->assertSee('Pengaturan Akun')
            ->assertSee(route('pelanggan.recovery-code'))
            ->assertDontSee('name="recovery_code"', false);
        $this->withSession([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Pelanggan Uji 1',
            'role' => 'Pelanggan',
        ])->get(route('pelanggan.recovery-code'))
            ->assertOk()
            ->assertSee('Terbitkan Kode Pemulihan')
            ->assertSee('30 hari')
            ->assertDontSee('Pesanan Saya')
            ->assertDontSee('Alamat Saya')
            ->assertDontSee('Metode Pembayaran')
            ->assertDontSee('Pengaturan Akun')
            ->assertDontSee('Keluar')
            ->assertDontSee('Password saat ini');
    }

    public function test_recovery_code_cannot_be_used_with_another_customers_username(): void
    {
        $this->createCustomer(1, 'elsa01');
        $this->createCustomer(2, 'budi02');
        $elsaCode = RecoveryCode::issueForCustomer(1);

        $this->post(route('password.verify'), [
            'username' => 'budi02',
            'recovery_code' => RecoveryCode::format($elsaCode),
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 1,
            'recovery_code_attempts' => 0,
        ]);
        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 2,
            'recovery_code_hash' => null,
        ]);
    }

    public function test_new_registration_does_not_issue_a_recovery_code_until_customer_uses_profile(): void
    {
        $this->post(route('register.process'), [
            'nama_pelanggan' => 'Pelanggan Baru',
            'username' => 'pelanggan-baru',
            'password' => 'password-baru',
        ])
            ->assertRedirect('/login')
            ->assertSessionMissing('recovery_code_issued')
            ->assertSessionHas('success', 'Pendaftaran berhasil. Setelah login, terbitkan kode pemulihan dari Profil agar dapat digunakan jika lupa password.');

        $customer = DB::table('pelanggan')->where('username', 'pelanggan-baru')->first();
        $this->assertNotNull($customer);
        $this->assertNull($customer->email);
        $this->assertNull($customer->email_verified_at);
        $this->assertNull($customer->recovery_code_hash);
        $this->assertNull($customer->recovery_code_fingerprint);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('terbitkan kode pemulihan dari Profil')
            ->assertDontSee('Kode pemulihan baru kamu');
    }

    public function test_registration_page_does_not_require_or_display_an_email_field(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Username')
            ->assertDontSee('Masukkan email');
    }

    public function test_staff_roles_cannot_use_customer_password_recovery_routes(): void
    {
        foreach (['Admin', 'Kurir'] as $role) {
            $this->withSession(['id_petugas' => 1, 'role' => $role])
                ->get(route('password.request'))
                ->assertForbidden();

            $this->withSession(['id_petugas' => 1, 'role' => $role])
                ->post(route('password.verify'), [
                    'username' => 'siti-uji',
                    'recovery_code' => 'AAAAAAAAAAAA',
                ])
                ->assertForbidden();
        }
    }

    public function test_logged_in_customer_is_sent_to_profile_to_issue_recovery_code(): void
    {
        $this->createCustomer();

        $this->withSession([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Siti Uji',
            'role' => 'Pelanggan',
        ])
            ->get(route('password.request'))
            ->assertRedirect(route('pelanggan.recovery-code'));

        $this->withSession([
            'id_pelanggan' => 1,
            'nama_pelanggan' => 'Siti Uji',
            'role' => 'Pelanggan',
            'recovery_code' => 'STB-ABCD-EFGH-JKLM',
        ])->get(route('pelanggan.recovery-code'))
            ->assertOk()
            ->assertSee('Terbitkan Kode Pemulihan');
    }

    public function test_recovery_codes_are_unique_and_reissuing_invalidates_the_previous_code(): void
    {
        $this->createCustomer();
        $this->createCustomer(2, 'budi-uji');

        $firstCode = RecoveryCode::issueForCustomer(1);
        $secondCode = RecoveryCode::issueForCustomer(2);
        $firstFingerprint = RecoveryCode::fingerprint($firstCode);
        $secondFingerprint = RecoveryCode::fingerprint($secondCode);

        $this->assertNotSame($firstCode, $secondCode);
        $this->assertNotSame($firstFingerprint, $secondFingerprint);
        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 1,
            'recovery_code_fingerprint' => $firstFingerprint,
        ]);
        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 2,
            'recovery_code_fingerprint' => $secondFingerprint,
        ]);

        $replacementCode = RecoveryCode::issueForCustomer(1);
        $this->assertNotSame($firstCode, $replacementCode);

        $this->post(route('password.verify'), [
            'username' => 'siti-uji',
            'recovery_code' => $firstCode,
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('error');

        $this->post(route('password.verify'), [
            'username' => 'siti-uji',
            'recovery_code' => RecoveryCode::format($replacementCode),
        ])->assertRedirect(route('password.reset'));
    }

    public function test_opening_the_forgot_password_page_does_not_issue_or_replace_a_code(): void
    {
        $this->createCustomer();
        $code = RecoveryCode::issueForCustomer(1);
        $hashBefore = DB::table('pelanggan')->where('id_pelanggan', 1)->value('recovery_code_hash');

        $this->get(route('password.request'))->assertOk();

        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => 1,
            'recovery_code_hash' => $hashBefore,
            'recovery_code_fingerprint' => RecoveryCode::fingerprint($code),
        ]);
    }

    public function test_reset_page_requires_recent_successful_code_verification(): void
    {
        $this->get(route('password.reset'))
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('error');

        $this->post(route('password.update'), [
            'password' => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ])
            ->assertRedirect(route('password.request'));
    }

    private function createCustomer(int $id = 1, string $username = 'siti-uji'): void
    {
        DB::table('pelanggan')->insert([
            'id_pelanggan' => $id,
            'nama_pelanggan' => 'Pelanggan Uji '.$id,
            'email' => 'siti'.$id.'@example.test',
            'email_verified_at' => now(),
            'username' => $username,
            'password' => Hash::make('password-lama-123'),
        ]);
    }
}
