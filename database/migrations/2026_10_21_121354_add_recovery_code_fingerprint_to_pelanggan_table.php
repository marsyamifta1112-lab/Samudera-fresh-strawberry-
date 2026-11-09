<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom recovery_code_fingerprint
        // dikelola oleh migrasi 2026_10_19_000002.
    }

    public function down(): void
    {
        // Tidak menghapus kolom milik migrasi lain.
    }
};