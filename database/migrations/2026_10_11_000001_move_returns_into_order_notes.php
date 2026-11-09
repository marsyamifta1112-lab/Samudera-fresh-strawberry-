<?php

use App\Support\OrderMetadata;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('returns')) {
            return;
        }

        $rows = DB::table('returns')->orderBy('id_return')->get();

        foreach ($rows->groupBy('id_pesanan') as $orderId => $returns) {
            $order = DB::table('pesanan')->where('id_pesanan', $orderId)->first();
            if (! $order) {
                throw new RuntimeException("Cannot preserve returns for missing order {$orderId}.");
            }

            $metadata = OrderMetadata::decode($order->catatan);
            $existingIds = array_map(
                static fn (array $return): int => (int) ($return['id_return'] ?? 0),
                $metadata['returns']
            );

            foreach ($returns as $return) {
                $data = (array) $return;
                if (! in_array((int) $data['id_return'], $existingIds, true)) {
                    $metadata['returns'][] = $data;
                }
            }

            DB::table('pesanan')->where('id_pesanan', $orderId)->update([
                'catatan' => OrderMetadata::encode($metadata['customer_note'], $metadata['returns']),
            ]);
        }

        Schema::drop('returns');
    }

    public function down(): void
    {
        if (Schema::hasTable('returns')) {
            return;
        }

        Schema::create('returns', function (Blueprint $table) {
            $table->increments('id_return');
            $table->integer('id_detail');
            $table->integer('id_pesanan');
            $table->integer('id_pelanggan');
            $table->integer('id_produk');
            $table->unsignedInteger('jumlah');
            $table->decimal('jumlah_pengembalian', 12, 2);
            $table->text('alasan');
            $table->string('foto_bukti', 255);
            $table->string('bank_pengembalian', 30);
            $table->string('nama_pemilik_rekening', 100);
            $table->string('nomor_rekening', 34);
            $table->string('status_return', 40)->default('Diajukan');
            $table->text('catatan_admin')->nullable();
            $table->string('status_pengembalian_dana', 30)->default('Menunggu');
            $table->string('referensi_transfer', 100)->nullable();
            $table->timestamp('stok_dikembalikan_at')->nullable();
            $table->timestamp('dana_ditransfer_at')->nullable();
            $table->timestamps();
        });

        foreach (DB::table('pesanan')->whereNotNull('catatan')->get() as $order) {
            $metadata = OrderMetadata::decode($order->catatan);
            if (! $metadata['returns']) {
                continue;
            }

            foreach ($metadata['returns'] as $return) {
                DB::table('returns')->insert($return);
            }

            DB::table('pesanan')->where('id_pesanan', $order->id_pesanan)->update([
                'catatan' => $metadata['customer_note'],
            ]);
        }
    }
};
