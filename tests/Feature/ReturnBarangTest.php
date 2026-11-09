<?php

namespace Tests\Feature;

use App\Support\OrderMetadata;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturnBarangTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('pelanggan', function (Blueprint $table) {
            $table->increments('id_pelanggan');
            $table->string('nama_pelanggan');
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->increments('id_produk');
            $table->string('nama_produk');
            $table->integer('stok_produk')->nullable();
            $table->string('foto_produk')->nullable();
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id_pesanan');
            $table->integer('id_pelanggan');
            $table->string('status_pesanan');
            $table->decimal('total_harga', 12, 2);
            $table->decimal('ongkir', 12, 2)->nullable()->default(0);
            $table->string('nama_penerima')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat_pengiriman')->nullable();
            $table->text('catatan')->nullable();
            $table->dateTime('tanggal_pemesanan')->nullable();
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->increments('id_detail');
            $table->integer('id_pesanan');
            $table->integer('id_produk');
            $table->integer('jumlah');
            $table->decimal('harga', 12, 2);
            $table->decimal('subtotal', 12, 2);
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->increments('id_pembayaran');
            $table->integer('id_pesanan');
            $table->string('status_pembayaran');
            $table->string('nomor_struk', 32)->nullable()->unique();
            $table->dateTime('tanggal_verifikasi')->nullable();
        });

        Schema::create('pengiriman', function (Blueprint $table) {
            $table->increments('id_pengiriman');
            $table->integer('id_pesanan');
            $table->string('status_pengiriman');
        });

        DB::table('pelanggan')->insert([
            ['id_pelanggan' => 1, 'nama_pelanggan' => 'Pelanggan Satu'],
            ['id_pelanggan' => 2, 'nama_pelanggan' => 'Pelanggan Dua'],
        ]);
        DB::table('produk')->insert([
            'id_produk' => 1,
            'nama_produk' => 'Stroberi Segar',
            'stok_produk' => 5,
        ]);
        DB::table('pesanan')->insert([
            'id_pesanan' => 1,
            'id_pelanggan' => 1,
            'status_pesanan' => 'selesai',
            'total_harga' => 50000,
            'nama_penerima' => 'Pelanggan Satu',
            'no_hp' => '081234567890',
            'alamat_pengiriman' => 'Ciwidey',
            'catatan' => 'Tolong hati-hati',
        ]);
        DB::table('detail_pesanan')->insert([
            'id_detail' => 1,
            'id_pesanan' => 1,
            'id_produk' => 1,
            'jumlah' => 2,
            'harga' => 25000,
            'subtotal' => 50000,
        ]);
        DB::table('pembayaran')->insert([
            'id_pesanan' => 1,
            'status_pembayaran' => 'diverifikasi',
        ]);
        DB::table('pengiriman')->insert([
            'id_pesanan' => 1,
            'status_pengiriman' => 'diterima',
        ]);
    }

    public function test_customer_can_request_return_and_only_return_available_order_quantity(): void
    {
        Storage::fake('local');

        $this->customer()
            ->get(route('pesanan.show', 1))
            ->assertOk()
            ->assertSee('Ajukan Return Produk Ini')
            ->assertSee('Stroberi Segar');

        $this->customer()
            ->post(route('pesanan.return.store', 1), $this->returnPayload(2))
            ->assertRedirect(route('pesanan.show', 1))
            ->assertSessionHas('success');

        $this->customer()
            ->get(route('pesanan.show', 1))
            ->assertOk()
            ->assertSee('Status Return')
            ->assertSee('Stroberi Segar')
            ->assertSee('Diajukan');

        $order = DB::table('pesanan')->where('id_pesanan', 1)->first();
        $return = OrderMetadata::findReturn($order->catatan, 1);
        $this->assertSame('Diajukan', $return['status_return']);
        $this->assertSame('Menunggu', $return['status_pengembalian_dana']);
        $this->assertEquals(50000, $return['jumlah_pengembalian']);
        $this->assertSame('Tolong hati-hati', OrderMetadata::customerNote($order->catatan));
        $this->assertFalse(Schema::hasTable('returns'));
        Storage::disk('local')->assertExists($return['foto_bukti']);

        $this->getJson('/api/pesanan/1')
            ->assertOk()
            ->assertJsonPath('data.pesanan.catatan', 'Tolong hati-hati')
            ->assertDontSee('nomor_rekening');

        $this->customer()
            ->post(route('pesanan.return.store', 1), $this->returnPayload(1))
            ->assertSessionHasErrors('jumlah');

        $this->assertCount(1, OrderMetadata::decode(DB::table('pesanan')->where('id_pesanan', 1)->value('catatan'))['returns']);
    }

    public function test_admin_workflow_restocks_once_and_tracks_transfer_completion_once(): void
    {
        Storage::fake('local');
        $this->customer()->post(route('pesanan.return.store', 1), $this->returnPayload(1));
        $returnId = 1;

        $this->admin()->get(route('admin.returns'))
            ->assertOk()
            ->assertSee('Return Barang')
            ->assertSee('Pelanggan Satu')
            ->assertSee('Stroberi Segar')
            ->assertSee(route('admin.returns.proof', [1, $returnId]));

        $this->admin()
            ->put(route('admin.returns.review', [1, $returnId]), [
                'keputusan' => 'setujui',
                'catatan_admin' => 'Silakan kirim barang.',
            ])
            ->assertRedirect(route('admin.returns'));
        $this->assertSame('Disetujui', $this->returnRecord($returnId)['status_return']);

        $this->admin()
            ->put(route('admin.returns.status', [1, $returnId]), ['status_return' => 'Barang Diterima'])
            ->assertRedirect(route('admin.returns'));

        $this->admin()
            ->put(route('admin.returns.status', [1, $returnId]), ['status_return' => 'Layak Dijual'])
            ->assertRedirect(route('admin.returns'));
        $this->assertDatabaseHas('produk', ['id_produk' => 1, 'stok_produk' => 6]);

        $this->admin()
            ->put(route('admin.returns.status', [1, $returnId]), ['status_return' => 'Layak Dijual'])
            ->assertStatus(409);
        $this->assertDatabaseHas('produk', ['id_produk' => 1, 'stok_produk' => 6]);

        $this->admin()
            ->put(route('admin.returns.refund', [1, $returnId]), ['aksi' => 'mulai_transfer'])
            ->assertRedirect(route('admin.returns'));
        $this->admin()
            ->put(route('admin.returns.refund', [1, $returnId]), [
                'aksi' => 'selesai_transfer',
                'referensi_transfer' => 'TRF-12345',
            ])
            ->assertRedirect(route('admin.returns'));

        $return = $this->returnRecord($returnId);
        $this->assertSame('Selesai', $return['status_pengembalian_dana']);
        $this->assertSame('TRF-12345', $return['referensi_transfer']);
        $this->admin()
            ->put(route('admin.returns.refund', [1, $returnId]), ['aksi' => 'selesai_transfer'])
            ->assertStatus(409);
        $this->assertDatabaseHas('produk', ['id_produk' => 1, 'stok_produk' => 6]);
    }

    public function test_customer_cannot_access_another_customers_return_proof_and_admin_role_is_required(): void
    {
        Storage::fake('local');
        $this->customer()->post(route('pesanan.return.store', 1), $this->returnPayload(1));
        $returnId = 1;

        $this->withSession(['id_pelanggan' => 2, 'role' => 'Pelanggan'])
            ->get(route('pesanan.return.proof', [1, $returnId]))
            ->assertNotFound();

        $this->withSession(['id_pelanggan' => 1, 'role' => 'Pelanggan'])
            ->get(route('admin.returns'))
            ->assertRedirect('/login');
    }

    private function returnPayload(int $quantity): array
    {
        return [
            'id_detail' => 1,
            'jumlah' => $quantity,
            'alasan' => 'Produk rusak saat diterima.',
            'foto_bukti' => UploadedFile::fake()->create('bukti.jpg', 1, 'image/jpeg'),
            'bank_pengembalian' => 'BCA',
            'nama_pemilik_rekening' => 'Pelanggan Satu',
            'nomor_rekening' => '1234567890',
        ];
    }

    private function returnRecord(int $returnId): array
    {
        return OrderMetadata::findReturn(
            DB::table('pesanan')->where('id_pesanan', 1)->value('catatan'),
            $returnId
        );
    }

    private function customer(): static
    {
        return $this->withSession(['id_pelanggan' => 1, 'nama_pelanggan' => 'Pelanggan Satu', 'role' => 'Pelanggan']);
    }

    private function admin(): static
    {
        return $this->withSession(['id_petugas' => 1, 'nama_petugas' => 'Admin Uji', 'role' => 'Admin']);
    }
}
