<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AdminPasswordResetController extends Controller
{
    private const RECOVERY_COOKIE = 'admin_password_recovery_v2';
    private const REQUEST_TTL_HOURS = 24;
    private const APPROVAL_TTL_MINUTES = 30;
    private const ATTEMPT_LIMIT = 5;
    private const ATTEMPT_DECAY_SECONDS = 900;

    public function requestForm(Request $request)
    {
        abort_if($request->session()->has('role'), 403);

        $recoveryRequest = $this->currentRequest($request);

        if ($recoveryRequest?->status === 'Disetujui') {
            return redirect()->route('admin.password.reset');
        }

        if ($recoveryRequest?->status === 'Menunggu Verifikasi') {
            return view('passwords.admin-request', [
                'pending' => true,
                'requestedUsername' => $recoveryRequest->username,
            ]);
        }

        if ($recoveryRequest?->status === 'Selesai') {
            return redirect()
                ->route('login')
                ->with('success', 'Password Admin berhasil diubah. Silakan login kembali.')
                ->withCookie($this->forgetRecoveryCookie());
        }

        if ($recoveryRequest?->status === 'Ditolak') {
            return view('passwords.admin-request', [
                'pending' => false,
                'requestedUsername' => null,
            ])->withCookie($this->forgetRecoveryCookie());
        }

        return view('passwords.admin-request', [
            'pending' => false,
            'requestedUsername' => null,
        ])->withCookie($this->forgetRecoveryCookie());
    }

    public function submitRequest(Request $request)
    {
        abort_if($request->session()->has('role'), 403);

        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
        ]);

        $username = trim($data['username']);
        $key = 'admin-password-recovery:'.hash(
            'sha256',
            mb_strtolower($username).'|'.$request->ip()
        );

        if (RateLimiter::tooManyAttempts($key, self::ATTEMPT_LIMIT)) {
            return back()
                ->withInput()
                ->with('error', 'Terlalu banyak permintaan. Silakan coba lagi dalam 15 menit.');
        }

        RateLimiter::hit($key, self::ATTEMPT_DECAY_SECONDS);

        $admin = DB::table('petugas')
            ->where('username', $username)
            ->where('role', 'Admin')
            ->where('status_akun', 'Aktif')
            ->first();

        if (! $admin) {
            return back()->with(
                'success',
                'Jika username cocok dengan akun Admin aktif, permintaan pemulihan akan menunggu verifikasi Admin lainnya.'
            );
        }

        $token = bin2hex(random_bytes(32));
        $requestId = DB::transaction(function () use ($admin, $token): int {
            $lockedAdmin = DB::table('petugas')
                ->where('id_petugas', $admin->id_petugas)
                ->where('role', 'Admin')
                ->where('status_akun', 'Aktif')
                ->lockForUpdate()
                ->first();

            if (! $lockedAdmin) {
                return 0;
            }

            DB::table('permintaan_reset_password_admin')
                ->where('admin_id', $lockedAdmin->id_petugas)
                ->where('status', 'Menunggu Verifikasi')
                ->update(['status' => 'Kedaluwarsa']);

            return DB::table('permintaan_reset_password_admin')->insertGetId([
                'admin_id' => $lockedAdmin->id_petugas,
                'token_hash' => hash('sha256', $token),
                'status' => 'Menunggu Verifikasi',
                'requested_at' => now(),
                'expires_at' => now()->addHours(self::REQUEST_TTL_HOURS),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        if ($requestId === 0) {
            return back()->with(
                'success',
                'Jika username cocok dengan akun Admin aktif, permintaan pemulihan akan menunggu verifikasi Admin lainnya.'
            );
        }

        $request->session()->regenerate();

        return redirect()
            ->route('admin.password.request')
            ->with('success', 'Permintaan dikirim. Admin lain dapat menyetujuinya melalui menu Petugas. Setelah itu, buka kembali halaman ini untuk membuat password baru.')
            ->withCookie($this->recoveryCookie($request, $requestId, $token));
    }

    public function approve(Request $request, int $id)
    {
        return $this->reviewRequest($request, $id, true);
    }

    public function reject(Request $request, int $id)
    {
        return $this->reviewRequest($request, $id, false);
    }

    public function resetForm(Request $request)
    {
        abort_if($request->session()->has('role'), 403);

        $recoveryRequest = $this->currentRequest($request);

        if (! $recoveryRequest || $recoveryRequest->status !== 'Disetujui') {
            $response = redirect()
                ->route('admin.password.request')
                ->with(
                    $recoveryRequest?->status === 'Menunggu Verifikasi' ? 'error' : 'info',
                    $recoveryRequest?->status === 'Menunggu Verifikasi'
                        ? 'Permintaan masih menunggu persetujuan Admin lainnya.'
                        : 'Silakan ajukan permintaan pemulihan baru untuk melanjutkan.'
                );

            return $recoveryRequest
                ? $response
                : $response->withCookie($this->forgetRecoveryCookie());
        }

        return view('passwords.admin-reset');
    }

    public function reset(Request $request)
    {
        abort_if($request->session()->has('role'), 403);

        $recoveryRequest = $this->currentRequest($request);
        if (! $recoveryRequest || $recoveryRequest->status !== 'Disetujui') {
            return redirect()
                ->route('admin.password.request')
                ->with('error', 'Persetujuan pemulihan tidak tersedia atau sudah kedaluwarsa.');
        }

        $data = $request->validate([
            'password' => [
                'required',
                'confirmed',
                PasswordRule::min(8),
            ],
        ]);

        $requestId = (int) $recoveryRequest->id;
        $cookie = (string) $request->cookie(self::RECOVERY_COOKIE);
        $token = preg_match('/^[1-9][0-9]*:([a-f0-9]{64})$/D', $cookie, $matches)
            ? $matches[1]
            : '';

        $updated = DB::transaction(function () use ($requestId, $token, $data): bool {
            $recoveryRequest = DB::table('permintaan_reset_password_admin')
                ->where('id', $requestId)
                ->lockForUpdate()
                ->first();

            if (
                ! $recoveryRequest
                || $recoveryRequest->status !== 'Disetujui'
                || ! $recoveryRequest->expires_at
                || now()->greaterThanOrEqualTo($recoveryRequest->expires_at)
                || ! hash_equals(
                    (string) $recoveryRequest->token_hash,
                    hash('sha256', $token)
                )
            ) {
                return false;
            }

            $admin = DB::table('petugas')
                ->where('id_petugas', $recoveryRequest->admin_id)
                ->where('role', 'Admin')
                ->where('status_akun', 'Aktif')
                ->lockForUpdate()
                ->first();

            if (! $admin) {
                return false;
            }

            DB::table('petugas')
                ->where('id_petugas', $admin->id_petugas)
                ->update(['password' => Hash::make($data['password'])]);

            DB::table('permintaan_reset_password_admin')
                ->where('id', $requestId)
                ->update([
                    'status' => 'Selesai',
                    'token_hash' => null,
                    'updated_at' => now(),
                ]);

            return true;
        });

        if (! $updated) {
            return redirect()
                ->route('admin.password.request')
                ->with('error', 'Persetujuan pemulihan sudah tidak berlaku.')
                ->withCookie($this->forgetRecoveryCookie());
        }

        $request->session()->regenerate();

        return redirect()
            ->route('login')
            ->with('success', 'Password Admin berhasil diubah. Silakan login kembali.')
            ->withCookie($this->forgetRecoveryCookie());
    }

    private function reviewRequest(Request $request, int $id, bool $approve)
    {
        $reviewerId = (int) $request->session()->get('id_petugas');
        $reviewed = DB::transaction(function () use ($id, $reviewerId, $approve): bool {
            $reviewer = DB::table('petugas')
                ->where('id_petugas', $reviewerId)
                ->where('role', 'Admin')
                ->where('status_akun', 'Aktif')
                ->first();
            $recoveryRequest = DB::table('permintaan_reset_password_admin')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();
            $requestingAdmin = $recoveryRequest
                ? DB::table('petugas')
                    ->where('id_petugas', $recoveryRequest->admin_id)
                    ->where('role', 'Admin')
                    ->where('status_akun', 'Aktif')
                    ->first()
                : null;

            if (
                ! $reviewer
                || ! $recoveryRequest
                || ! $requestingAdmin
                || (int) $recoveryRequest->admin_id === $reviewerId
                || $recoveryRequest->status !== 'Menunggu Verifikasi'
                || ! $recoveryRequest->expires_at
                || now()->greaterThanOrEqualTo($recoveryRequest->expires_at)
            ) {
                return false;
            }

            DB::table('permintaan_reset_password_admin')
                ->where('id', $id)
                ->update([
                    'status' => $approve ? 'Disetujui' : 'Ditolak',
                    'reviewer_id' => $reviewerId,
                    'reviewed_at' => now(),
                    'expires_at' => $approve ? now()->addMinutes(self::APPROVAL_TTL_MINUTES) : now(),
                    'token_hash' => $approve ? $recoveryRequest->token_hash : null,
                    'updated_at' => now(),
                ]);

            return true;
        });

        if (! $reviewed) {
            return redirect()
                ->route('admin.staff')
                ->with('error', 'Permintaan tidak dapat diproses. Pastikan permintaan masih berlaku dan bukan milik akun Admin Anda.');
        }

        return redirect()
            ->route('admin.staff')
            ->with('success', $approve
                ? 'Permintaan pemulihan disetujui. Setelah logout, Admin pemohon dapat membuka kembali halaman pemulihan dari browser ini untuk membuat password baru.'
                : 'Permintaan pemulihan password Admin ditolak.');
    }

    private function currentRequest(Request $request): ?object
    {
        $cookie = $request->cookie(self::RECOVERY_COOKIE);

        if (
            ! is_string($cookie)
            || ! preg_match('/^([1-9][0-9]*):([a-f0-9]{64})$/D', $cookie, $matches)
        ) {
            return null;
        }

        $id = (int) $matches[1];
        $token = $matches[2];
        $recoveryRequest = DB::table('permintaan_reset_password_admin as recovery')
            ->join('petugas', 'petugas.id_petugas', '=', 'recovery.admin_id')
            ->where('recovery.id', $id)
            ->where('petugas.role', 'Admin')
            ->where('petugas.status_akun', 'Aktif')
            ->select(
                'recovery.id',
                'recovery.status',
                'recovery.token_hash',
                'recovery.expires_at',
                'petugas.username'
            )
            ->first();

        if (
            ! $recoveryRequest
            || ! is_string($recoveryRequest->token_hash)
            || ! hash_equals(
                $recoveryRequest->token_hash,
                hash('sha256', $token)
            )
            || ! $recoveryRequest->expires_at
            || now()->greaterThanOrEqualTo($recoveryRequest->expires_at)
        ) {
            return null;
        }

        return $recoveryRequest;
    }

    private function recoveryCookie(Request $request, int $requestId, string $token)
    {
        return Cookie::make(
            self::RECOVERY_COOKIE,
            $requestId.':'.$token,
            self::REQUEST_TTL_HOURS * 60,
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            'strict'
        );
    }

    private function forgetRecoveryCookie()
    {
        return Cookie::forget(self::RECOVERY_COOKIE, '/');
    }
}
