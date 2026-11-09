<?php

namespace App\Http\Controllers;

use App\Support\CustomerEmailVerificationLink;
use App\Support\RecoveryCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Throwable;

class PelangganController extends Controller
{
    // Menampilkan profil pelanggan yang sedang login
    public function profile()
    {
        $pelanggan = DB::table('pelanggan')
            ->where('id_pelanggan', session('id_pelanggan'))
            ->first();

        abort_if(! $pelanggan, 404);

        $pesanan = DB::table('pesanan')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderByDesc('tanggal_pemesanan')
            ->orderByDesc('id_pesanan')
            ->limit(5)
            ->get();

        $pembayaran = DB::table('pembayaran as pb')
            ->join(
                'pesanan as p',
                'p.id_pesanan',
                '=',
                'pb.id_pesanan'
            )
            ->where('p.id_pelanggan', $pelanggan->id_pelanggan)
            ->select(
                'pb.metode_pembayaran',
                'pb.tanggal_pembayaran',
                'p.id_pesanan'
            )
            ->orderByDesc('pb.tanggal_pembayaran')
            ->limit(5)
            ->get();

        return view(
            'pelanggan.profile',
            compact('pelanggan', 'pesanan', 'pembayaran')
        );
    }

    public function recoveryCodePage()
    {
        $pelanggan = DB::table('pelanggan')
            ->where('id_pelanggan', session('id_pelanggan'))
            ->first();

        abort_if(! $pelanggan, 404);

        return view('pelanggan.recovery-code', compact('pelanggan'));
    }

    public function issueRecoveryCode(Request $request)
    {
        $pelanggan = DB::table('pelanggan')
            ->where('id_pelanggan', $request->session()->get('id_pelanggan'))
            ->first();

        abort_if(! $pelanggan, 404);

        $code = RecoveryCode::issueForCustomer((int) $pelanggan->id_pelanggan);

        return redirect()
            ->route('pelanggan.recovery-code')
            ->with('success', 'Kode pemulihan berhasil diterbitkan. Simpan kode ini sekarang karena hanya ditampilkan satu kali.')
            ->with('recovery_code', RecoveryCode::format($code));
    }

    public function continuePasswordRecovery(Request $request)
    {
        $hasActiveRecoveryCode = DB::table('pelanggan')
            ->where('id_pelanggan', $request->session()->get('id_pelanggan'))
            ->whereNotNull('recovery_code_hash')
            ->whereNotNull('recovery_code_fingerprint')
            ->whereNull('recovery_code_used_at')
            ->where('recovery_code_expires_at', '>', now())
            ->where('recovery_code_attempts', '<', RecoveryCode::MAX_ATTEMPTS)
            ->exists();

        abort_unless($hasActiveRecoveryCode, 403);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('password.request')
            ->with('info', 'Masukkan username dan kode pemulihan yang baru saja diterbitkan.');
    }

    // Memperbarui profil pelanggan
    public function updateProfile(Request $request)
    {
        $pelanggan = DB::table('pelanggan')
            ->where('id_pelanggan', session('id_pelanggan'))
            ->first();

        abort_if(! $pelanggan, 404);

        $data = $request->validate([
            'nama_pelanggan' => 'required|string|max:100',

            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pelanggan', 'username')
                    ->ignore(
                        $pelanggan->id_pelanggan,
                        'id_pelanggan'
                    ),
            ],

            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('pelanggan', 'email')
                    ->ignore(
                        $pelanggan->id_pelanggan,
                        'id_pelanggan'
                    ),
            ],

            'current_password' =>
                'required_with:password|nullable|string',

            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $emailChanged = array_key_exists('email', $data)
            && strtolower((string) ($data['email'] ?? ''))
                !== strtolower((string) ($pelanggan->email ?? ''));

        if ($emailChanged) {
            $data['email_verified_at'] = null;
        }

        if (! empty($data['password'])) {
            if (
                ! Hash::check(
                    $data['current_password'],
                    $pelanggan->password
                )
            ) {
                return back()
                    ->withErrors([
                        'current_password' =>
                            'Password saat ini tidak sesuai.',
                    ])
                    ->withInput(
                        $request->except([
                            'password',
                            'password_confirmation',
                            'current_password',
                        ])
                    );
            }

            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['current_password']);

        DB::table('pelanggan')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->update($data);

        session(['nama_pelanggan' => $data['nama_pelanggan']]);

        $request->session()->regenerate();

        $verificationSent = false;
        if ($emailChanged && filled($data['email'] ?? null)) {
            $verificationSent = $this->sendVerificationLink(
                (int) $pelanggan->id_pelanggan,
                $data['nama_pelanggan'],
                $data['email']
            );
        }

        return redirect()
            ->route('pelanggan.profile')
            ->with('success', $emailChanged && filled($data['email'] ?? null)
                ? ($verificationSent
                    ? 'Profil diperbarui. Periksa email baru untuk menyelesaikan verifikasi alamat.'
                    : 'Profil diperbarui, tetapi tautan verifikasi belum terkirim. Coba kirim ulang dari Profil.')
                : 'Profil berhasil diperbarui.');
    }

    public function sendEmailVerification(Request $request)
    {
        $pelanggan = DB::table('pelanggan')
            ->where('id_pelanggan', $request->session()->get('id_pelanggan'))
            ->first();

        abort_if(! $pelanggan, 404);

        if (! $pelanggan->email) {
            return redirect()->route('pelanggan.profile')->with('error', 'Tambahkan email terlebih dahulu di Pengaturan Akun.');
        }

        if ($pelanggan->email_verified_at) {
            return redirect()->route('pelanggan.profile')->with('info', 'Email akun ini sudah terverifikasi.');
        }

        $sent = $this->sendVerificationLink(
            (int) $pelanggan->id_pelanggan,
            $pelanggan->nama_pelanggan,
            $pelanggan->email
        );

        return redirect()->route('pelanggan.profile')->with(
            $sent ? 'success' : 'error',
            $sent
                ? 'Tautan verifikasi dikirim. Periksa kotak masuk dan folder spam.'
                : 'Tautan verifikasi belum dapat dikirim. Coba lagi nanti.'
        );
    }

    public function verifyEmail(int $id, string $hash)
    {
        $pelanggan = DB::table('pelanggan')
            ->where('id_pelanggan', $id)
            ->first();

        abort_if(
            ! $pelanggan
                || ! $pelanggan->email
                || ! hash_equals(sha1($pelanggan->email), $hash),
            404
        );

        if (! $pelanggan->email_verified_at) {
            DB::table('pelanggan')
                ->where('id_pelanggan', $id)
                ->update(['email_verified_at' => now()]);
        }

        return redirect()
            ->route('login')
            ->with('success', 'Email berhasil diverifikasi.');
    }

    // Keluar dari akun
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Kamu berhasil keluar dari akun.');
    }

    private function sendVerificationLink(int $customerId, string $customerName, string $email): bool
    {
        $key = 'customer-email-verification:'.$customerId;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return false;
        }

        RateLimiter::hit($key, 3600);

        try {
            CustomerEmailVerificationLink::send($customerId, $customerName, $email);

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}