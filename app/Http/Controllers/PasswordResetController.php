<?php

namespace App\Http\Controllers;

use App\Support\RecoveryCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    private const RESET_GRANT_MINUTES = 10;

    public function showLinkRequestForm(Request $request)
    {
        $this->abortForStaff($request);

        if (
            $request->session()->get('role') === 'Pelanggan'
            && $request->session()->has('id_pelanggan')
        ) {
            return redirect()->route('pelanggan.recovery-code');
        }

        return view('passwords.lupa');
    }

    public function verifyRecoveryCode(Request $request)
    {
        $this->abortForStaff($request);

        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'recovery_code' => ['required', 'string', 'max:30'],
        ]);

        $username = trim($data['username']);
        $normalizedCode = RecoveryCode::normalize($data['recovery_code']);
        $formatValid = preg_match(
            '/^[ABCDEFGHJKLMNPQRSTUVWXYZ23456789]{12}$/D',
            $normalizedCode
        ) === 1;

        $result = DB::transaction(function () use ($username, $normalizedCode, $formatValid): array {
            $pelanggan = DB::table('pelanggan')
                ->where('username', $username)
                ->lockForUpdate()
                ->first();

            if (! $pelanggan) {
                return ['valid' => false];
            }

            $isLocked = $pelanggan
                && $pelanggan->recovery_code_locked_until
                && now()->lt($pelanggan->recovery_code_locked_until);
            $isActive = $pelanggan
                && $pelanggan->recovery_code_hash
                && $pelanggan->recovery_code_fingerprint
                && ! $pelanggan->recovery_code_used_at
                && $pelanggan->recovery_code_expires_at
                && now()->lt($pelanggan->recovery_code_expires_at)
                && (int) $pelanggan->recovery_code_attempts < RecoveryCode::MAX_ATTEMPTS;

            $codeMatches = $formatValid
                && $isActive
                && ! $isLocked
                && Hash::check($normalizedCode, $pelanggan->recovery_code_hash)
                && hash_equals(
                    $pelanggan->recovery_code_fingerprint,
                    RecoveryCode::fingerprint($normalizedCode)
                );

            if ($codeMatches) {
                return [
                    'valid' => true,
                    'id' => $pelanggan->id_pelanggan,
                    'hash' => $pelanggan->recovery_code_hash,
                    'fingerprint' => $pelanggan->recovery_code_fingerprint,
                ];
            }

            if ($isActive && ! $isLocked) {
                $attempts = min(
                    RecoveryCode::MAX_ATTEMPTS,
                    (int) $pelanggan->recovery_code_attempts + 1
                );

                DB::table('pelanggan')
                    ->where('id_pelanggan', $pelanggan->id_pelanggan)
                    ->update([
                        'recovery_code_attempts' => $attempts,
                        'recovery_code_locked_until' => $attempts >= RecoveryCode::MAX_ATTEMPTS
                            ? now()->addMinutes(RecoveryCode::LOCK_MINUTES)
                            : null,
                    ]);
            }

            return ['valid' => false];
        });

        if (! $result['valid']) {
            return redirect()
                ->route('password.request')
                ->withInput(['username' => $username])
                ->with(
                    'error',
                    'Username atau kode pemulihan tidak valid, kedaluwarsa, sudah digunakan, atau sementara dikunci.'
                );
        }

        $request->session()->regenerate();
        $request->session()->put([
            'reset_pelanggan_id' => $result['id'],
            'reset_recovery_code_hash' => $result['hash'],
            'reset_recovery_code_fingerprint' => $result['fingerprint'],
            'reset_grant_expires_at' => now()
                ->addMinutes(self::RESET_GRANT_MINUTES)
                ->timestamp,
        ]);

        return redirect()->route('password.reset');
    }

    public function showResetForm(Request $request)
    {
        $this->abortForStaff($request);

        if ($this->hasValidResetGrant($request)) {
            return view('passwords.reset');
        }

        $this->clearResetGrant($request);

        return redirect()
            ->route('password.request')
            ->with('error', 'Verifikasi kode pemulihan tidak berlaku. Silakan coba lagi.');
    }

    public function updatePassword(Request $request)
    {
        $this->abortForStaff($request);

        if (! $this->hasValidResetGrant($request)) {
            $this->clearResetGrant($request);

            return redirect()
                ->route('password.request')
                ->with('error', 'Verifikasi kode pemulihan tidak berlaku. Silakan coba lagi.');
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $pelangganId = (int) $request->session()->get('reset_pelanggan_id');
        $recoveryHash = (string) $request->session()->get('reset_recovery_code_hash');
        $fingerprint = (string) $request->session()->get('reset_recovery_code_fingerprint');

        $updated = DB::transaction(function () use ($pelangganId, $recoveryHash, $fingerprint, $data) {
            $pelanggan = DB::table('pelanggan')
                ->where('id_pelanggan', $pelangganId)
                ->lockForUpdate()
                ->first();

            if (! $pelanggan || ! $this->matchesResetGrant($pelanggan, $recoveryHash, $fingerprint)) {
                return false;
            }

            DB::table('pelanggan')
                ->where('id_pelanggan', $pelangganId)
                ->update([
                    'password' => Hash::make($data['password']),
                    'recovery_code_hash' => null,
                    'recovery_code_used_at' => now(),
                    'recovery_code_attempts' => 0,
                    'recovery_code_locked_until' => null,
                    'recovery_code_expires_at' => null,
                ]);

            return true;
        });

        $this->clearResetGrant($request);

        if (! $updated) {
            return redirect()
                ->route('password.request')
                ->with('error', 'Kode pemulihan tidak berlaku lagi. Silakan verifikasi ulang.');
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Password berhasil diubah. Silakan login kembali.');
    }

    private function abortForStaff(Request $request): void
    {
        abort_if(
            $request->session()->has('id_petugas')
                || in_array($request->session()->get('role'), ['Admin', 'Kurir'], true),
            403
        );
    }

    private function hasValidResetGrant(Request $request): bool
    {
        $pelangganId = $request->session()->get('reset_pelanggan_id');
        $recoveryHash = $request->session()->get('reset_recovery_code_hash');
        $fingerprint = $request->session()->get('reset_recovery_code_fingerprint');
        $grantExpiresAt = (int) $request->session()->get('reset_grant_expires_at', 0);

        if (! $pelangganId || ! $recoveryHash || ! $fingerprint || $grantExpiresAt <= now()->timestamp) {
            return false;
        }

        $pelanggan = DB::table('pelanggan')
            ->where('id_pelanggan', $pelangganId)
            ->first();

        return $pelanggan
            && $this->matchesResetGrant($pelanggan, $recoveryHash, $fingerprint);
    }

    private function matchesResetGrant(object $pelanggan, string $recoveryHash, string $fingerprint): bool
    {
        return is_string($pelanggan->recovery_code_hash)
            && hash_equals($pelanggan->recovery_code_hash, $recoveryHash)
            && is_string($pelanggan->recovery_code_fingerprint)
            && hash_equals($pelanggan->recovery_code_fingerprint, $fingerprint)
            && $pelanggan->recovery_code_used_at === null
            && $pelanggan->recovery_code_expires_at
            && now()->lt($pelanggan->recovery_code_expires_at)
            && (int) $pelanggan->recovery_code_attempts < RecoveryCode::MAX_ATTEMPTS
            && (
                ! $pelanggan->recovery_code_locked_until
                || now()->gte($pelanggan->recovery_code_locked_until)
            );
    }

    private function clearResetGrant(Request $request): void
    {
        $request->session()->forget([
            'reset_pelanggan_id',
            'reset_recovery_code_hash',
            'reset_recovery_code_fingerprint',
            'reset_grant_expires_at',
        ]);
    }
}