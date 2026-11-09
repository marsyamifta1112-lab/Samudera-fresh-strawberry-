<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class RecoveryCode
{
    public const VALIDITY_DAYS = 30;

    public const MAX_ATTEMPTS = 5;

    public const LOCK_MINUTES = 15;

    private const CHARACTERS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public static function issueForCustomer(int $customerId): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $code = self::generate();
            $fingerprint = self::fingerprint($code);

            try {
                $result = DB::transaction(function () use ($customerId, $code, $fingerprint): array {
                    $customer = DB::table('pelanggan')
                        ->where('id_pelanggan', $customerId)
                        ->lockForUpdate()
                        ->first();

                    abort_if(! $customer, 404);

                    if (DB::table('pelanggan')->where('recovery_code_fingerprint', $fingerprint)->exists()) {
                        return ['status' => 'collision'];
                    }

                    $legacyHashes = DB::table('pelanggan')
                        ->where('id_pelanggan', '<>', $customerId)
                        ->whereNotNull('recovery_code_hash')
                        ->whereNull('recovery_code_fingerprint')
                        ->pluck('recovery_code_hash');

                    foreach ($legacyHashes as $legacyHash) {
                        if (Hash::check($code, $legacyHash)) {
                            return ['status' => 'collision'];
                        }
                    }

                    DB::table('pelanggan')
                        ->where('id_pelanggan', $customerId)
                        ->update([
                            'recovery_code_hash' => Hash::make($code),
                            'recovery_code_fingerprint' => $fingerprint,
                            'recovery_code_expires_at' => now()->addDays(self::VALIDITY_DAYS),
                            'recovery_code_used_at' => null,
                            'recovery_code_attempts' => 0,
                            'recovery_code_locked_until' => null,
                        ]);

                    return ['status' => 'issued', 'code' => $code];
                });
            } catch (QueryException $exception) {
                if (! DB::table('pelanggan')->where('recovery_code_fingerprint', $fingerprint)->exists()) {
                    throw $exception;
                }

                continue;
            }

            if ($result['status'] === 'issued') {
                return $result['code'];
            }
        }

        throw new RuntimeException('Unable to issue a unique recovery code after multiple attempts.');
    }

    public static function generate(): string
    {
        $code = '';
        $maximumIndex = strlen(self::CHARACTERS) - 1;

        for ($index = 0; $index < 12; $index++) {
            $code .= self::CHARACTERS[random_int(0, $maximumIndex)];
        }

        return $code;
    }

    public static function normalize(string $code): string
    {
        $stripped = preg_replace('/[\s-]+/', '', trim($code));

        if ($stripped === null) {
            throw new RuntimeException('Unable to normalize the recovery code.');
        }

        $normalized = strtoupper($stripped);

        return strlen($normalized) === 15 && str_starts_with($normalized, 'STB')
            ? substr($normalized, 3)
            : $normalized;
    }

    public static function format(string $code): string
    {
        return 'STB-'.implode('-', str_split($code, 4));
    }

    public static function fingerprint(string $code): string
    {
        $key = (string) config('app.key');

        if ($key === '') {
            throw new RuntimeException('APP_KEY must be configured before issuing recovery codes.');
        }

        return hash_hmac('sha256', $code, $key);
    }
}
