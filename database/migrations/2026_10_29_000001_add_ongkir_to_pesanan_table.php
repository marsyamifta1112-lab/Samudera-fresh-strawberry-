<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('ongkir', 12, 2)->nullable()->default(0);
        });

        DB::table('pesanan')->whereNull('ongkir')->update(['ongkir' => 0]);
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('ongkir');
        });
    }
};
