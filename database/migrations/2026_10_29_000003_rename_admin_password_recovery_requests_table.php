<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OLD_TABLE = 'admin_password_recovery_requests';

    private const NEW_TABLE = 'permintaan_reset_password_admin';

    public function up(): void
    {
        $hasOldTable = Schema::hasTable(self::OLD_TABLE);
        $hasNewTable = Schema::hasTable(self::NEW_TABLE);

        if ($hasOldTable && ! $hasNewTable) {
            Schema::rename(self::OLD_TABLE, self::NEW_TABLE);

            return;
        }

        if (! $hasOldTable && $hasNewTable) {
            return;
        }

        throw new RuntimeException('Tabel permintaan reset password Admin tidak ditemukan atau nama tabel lama dan baru sama-sama tersedia.');
    }

    public function down(): void
    {
        $hasOldTable = Schema::hasTable(self::OLD_TABLE);
        $hasNewTable = Schema::hasTable(self::NEW_TABLE);

        if ($hasNewTable && ! $hasOldTable) {
            Schema::rename(self::NEW_TABLE, self::OLD_TABLE);

            return;
        }

        if ($hasOldTable && ! $hasNewTable) {
            return;
        }

        throw new RuntimeException('Tabel permintaan reset password Admin tidak ditemukan atau nama tabel lama dan baru sama-sama tersedia.');
    }
};
