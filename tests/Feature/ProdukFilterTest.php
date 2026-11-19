<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProdukFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('produk', function (Blueprint $table) {
            $table->bigIncrements('id_produk');
            $table->string('nama_produk');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_produk', 12, 2);
            $table->unsignedInteger('stok_produk');
            $table->string('foto_produk')->nullable();
        });

        $migration = require base_path('database/migrations/2026_10_09_000001_add_berat_gram_to_produk_table.php');
        $migration->up();

        Schema::create('pengaturan_operasional', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->time('jam_buka');
            $table->time('jam_tutup');
            $table->json('hari_operasional');
            $table->json('tanggal_libur');
            $table->dateTime('updated_at')->nullable();
        });
        DB::table('pengaturan_operasional')->insert([
            'id' => 1,
            'jam_buka' => '08:00:00',
            'jam_tutup' => '17:00:00',
            'hari_operasional' => json_encode([1, 2, 3, 4, 5, 6]),
            'tanggal_libur' => json_encode([]),
        ]);
        Schema::create('produk_grade', function (Blueprint $table) {
            $table->increments('id_produk_grade');
            $table->unsignedBigInteger('id_produk');
            $table->char('grade', 1);
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('stok_kg', 12, 3)->default(0);
        });

    }

    public function test_category_price_and_weight_filters_can_be_combined(): void
    {
        $this->createProduct('Strawberry Segar 250g', 'Buah segar', 20000, 250);
        $this->createProduct('Keripik Strawberry 100g', 'Keripik renyah', 15000, 100);
        $this->createProduct('Selai Strawberry 200g', 'Selai rumahan dengan rasa segar', 25000, 200);

        $this->get(route('produk.index', [
            'kategori' => 'keripik',
            'harga_min' => 10000,
            'harga_max' => 20000,
            'berat_min' => 80,
            'berat_max' => 120,
        ]))
            ->assertOk()
            ->assertSee('site-header', false)
            ->assertSee('Jam Operasional')
            ->assertSee('Tutup: Minggu')
            ->assertSee(route('produk.index'))
            ->assertSee('Keripik Strawberry 100g')
            ->assertDontSee('Strawberry Segar 250g')
            ->assertDontSee('Selai Strawberry 200g')
            ->assertSee('1 produk ditemukan');
    }

    public function test_category_chips_filter_each_product_category(): void
    {
        $this->createProduct('Strawberry Segar 250g', 'Buah segar', 20000, 250);
        $this->createProduct('Keripik Strawberry 100g', 'Keripik renyah', 15000, 100);
        $this->createProduct('Selai Strawberry 200g', 'Selai rumahan dengan rasa segar', 25000, 200);

        foreach ([
            'segar' => ['Stroberi Segar · Grade A', '3 produk ditemukan'],
            'keripik' => ['Keripik Strawberry 100g', '1 produk ditemukan'],
            'selai' => ['Selai Strawberry 200g', '1 produk ditemukan'],
        ] as $category => [$expectedProduct, $expectedCount]) {
            $this->get(route('produk.index', ['kategori' => $category]))
                ->assertOk()
                ->assertSee($expectedProduct)
                ->assertSee($expectedCount);
        }
    }

    public function test_catalog_shows_each_strawberry_grade_with_its_own_price_photo_and_cart_grade(): void
    {
        $strawberryId = $this->createProduct('Strawberry Segar 250g', 'Buah segar', 20000, 250);
        $keripikId = $this->createProduct('Keripik Strawberry 100g', 'Keripik renyah', 15000, 100);
        $selaiId = $this->createProduct('Selai Strawberry 200g', 'Selai rumahan', 25000, 200);
        $this->createProduct('Paket Hemat Strawberry', 'Paket promo', 30000, 500);

        DB::table('produk')->where('id_produk', $keripikId)
            ->update(['foto_produk' => 'keripik-strawberry.jpeg']);
        DB::table('produk')->where('id_produk', $selaiId)
            ->update(['foto_produk' => 'selai-strawberry.jpeg']);

        $this->get(route('produk.index'))
            ->assertOk()
            ->assertSee('Stroberi Segar · Grade A')
            ->assertSee('Stroberi Segar · Grade B')
            ->assertSee('Stroberi Segar · Grade C')
            ->assertSee('Rp80.000')
            ->assertSee('Rp70.000')
            ->assertSee('Rp50.000')
            ->assertSee('Stok: 10 kg')
            ->assertSee(asset('images/strawberry.jpeg'))
            ->assertSee(asset('images/strawberry-grade-b.jpg'))
            ->assertSee(asset('images/strawberry-grade-c.jpg'))
            ->assertSee(asset('images/keripik-strawberry.jpeg'))
            ->assertSee(asset('images/selai-strawberry.jpeg'))
            ->assertSee(route('produk.show', ['id' => $strawberryId, 'grade' => 'B']))
            ->assertSee('name="grade" value="A"', false)
            ->assertSee('name="grade" value="B"', false)
            ->assertSee('name="grade" value="C"', false)
            ->assertSee('5 produk ditemukan')
            ->assertDontSee('Paket Hemat Strawberry');

        $this->get(route('produk.show', ['id' => $strawberryId, 'grade' => 'B']))
            ->assertOk()
            ->assertSee('Grade B')
            ->assertSee('Rp70.000/kg')
            ->assertSee('Stok: 10 kg')
            ->assertSee(asset('images/strawberry-grade-b.jpg'));
    }

    public function test_price_and_weight_ranges_filter_independently(): void
    {
        $this->createProduct('Strawberry Segar 250g', 'Buah segar', 20000, 250);
        $this->createProduct('Keripik Strawberry 100g', 'Keripik renyah', 15000, 100);
        $this->createProduct('Selai Strawberry 200g', 'Selai rumahan', 25000, 200);

        $this->get(route('produk.index', [
            'harga_min' => 50000,
            'harga_max' => 80000,
        ]))
            ->assertOk()
            ->assertSee('Stroberi Segar · Grade A')
            ->assertSee('Stroberi Segar · Grade B')
            ->assertSee('Stroberi Segar · Grade C')
            ->assertDontSee('Selai Strawberry 200g')
            ->assertDontSee('Keripik Strawberry 100g')
            ->assertSee('3 produk ditemukan');

        $this->get(route('produk.index', [
            'harga_min' => 20000,
            'harga_max' => 25000,
        ]))
            ->assertOk()
            ->assertDontSee('Strawberry Segar 250g')
            ->assertSee('Selai Strawberry 200g')
            ->assertDontSee('Keripik Strawberry 100g')
            ->assertSee('1 produk ditemukan');

        $this->get(route('produk.index', [
            'berat_min' => 100,
            'berat_max' => 200,
        ]))
            ->assertOk()
            ->assertSee('Keripik Strawberry 100g')
            ->assertSee('Selai Strawberry 200g')
            ->assertDontSee('Strawberry Segar 250g')
            ->assertSee('2 produk ditemukan');
    }

    public function test_search_matches_product_description(): void
    {
        $this->createProduct('Produk A', 'Buah pilihan dari Ciwidey', 20000, 250);
        $this->createProduct('Produk B', 'Olahan selai stroberi', 25000, 200);

        $this->get(route('produk.index', ['q' => 'Ciwidey']))
            ->assertOk()
            ->assertSee('Produk A')
            ->assertDontSee('Produk B')
            ->assertSee('1 produk ditemukan');
    }

    public function test_hemat_package_is_hidden_from_catalog_and_product_detail(): void
    {
        $id = $this->createProduct('Paket Hemat Strawberry', 'Paket promo', 30000, 500);

        $this->get(route('produk.index'))
            ->assertOk()
            ->assertDontSee('Paket Hemat Strawberry');

        $this->get(route('produk.show', $id))->assertNotFound();
    }

    public function test_product_api_hides_hemat_packages_and_returns_strawberry_grades(): void
    {
        $freshId = $this->createProduct('Strawberry Segar', 'Buah segar', 20000, 250);
        $packageId = $this->createProduct('Paket Hemat Strawberry', 'Paket promo', 30000, 500);

        $listResponse = $this->getJson('/api/produk')
            ->assertOk()
            ->assertJsonMissing(['nama_produk' => 'Paket Hemat Strawberry']);
        $this->assertEquals(80000, $listResponse->json('data.0.grades.0.harga_per_kg'));

        $this->getJson('/api/produk/'.$freshId)
            ->assertOk()
            ->assertJsonPath('data.satuan', 'kg')
            ->assertJsonPath('data.harga_produk', null)
            ->assertJsonPath('data.grades.0.grade', 'A')
            ->assertJsonPath('data.grades.0.foto_produk', 'strawberry.jpeg');

        $this->getJson('/api/produk/'.$packageId)->assertNotFound();
    }

    public function test_invalid_price_and_weight_ranges_are_rejected(): void
    {
        $this->get(route('produk.index', [
            'harga_min' => 30000,
            'harga_max' => 10000,
            'berat_min' => 500,
            'berat_max' => 100,
        ]))
            ->assertRedirect()
            ->assertSessionHasErrors(['harga_max', 'berat_max']);
    }

    public function test_product_detail_adds_selected_quantity_to_session_cart(): void
    {
        $idProduk = $this->createProduct('Selai Strawberry 250g', 'Buah segar', 20000, 250);
        $session = [
            'id_pelanggan' => 7,
            'role' => 'Pelanggan',
        ];

        $this->get(route('produk.show', $idProduk))
            ->assertOk()
            ->assertSee('site-header', false)
            ->assertSee(route('produk.index'))
            ->assertSee('Tambah ke Keranjang')
            ->assertSee('id="quantity"', false)
            ->assertSee('max="10"', false);

        $this->withSession($session)
            ->post(route('keranjang.tambah', $idProduk), ['jumlah' => 3])
            ->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionHas('keranjang', function (array $keranjang) use ($idProduk): bool {
                return $keranjang[$idProduk]['jumlah'] === 3;
            });

        $this->withSession($session)
            ->get(route('keranjang.index'))
            ->assertOk()
            ->assertSee('Selai Strawberry 250g');
    }

    public function test_product_catalog_and_detail_use_the_uploaded_photo_and_medium_text_sizes(): void
    {
        $idProduk = $this->createProduct('Keripik Strawberry 100g', 'Keripik renyah', 20000, 100);
        DB::table('produk')
            ->where('id_produk', $idProduk)
            ->update(['foto_produk' => 'keripik-strawberry.jpeg']);

        $photoUrl = asset('images/keripik-strawberry.jpeg');

        $this->get(route('produk.index'))
            ->assertOk()
            ->assertSee($photoUrl)
            ->assertSee('font-size: 18px', false)
            ->assertSee('font-size: 14px', false);

        $this->get(route('produk.show', $idProduk))
            ->assertOk()
            ->assertSee($photoUrl)
            ->assertSee('font-size: 15px', false);
    }

    public function test_known_weight_in_product_name_is_saved_for_weight_filtering(): void
    {
        $idProduk = DB::table('produk')->insertGetId([
            'nama_produk' => 'Strawberry Segar 500g',
            'deskripsi' => 'Strawberry pilihan',
            'harga_produk' => 45000,
            'stok_produk' => 10,
        ], 'id_produk');

        $migration = require base_path('database/migrations/2026_10_09_000002_fill_known_berat_gram_produk.php');
        $migration->up();

        $this->assertDatabaseHas('produk', [
            'id_produk' => $idProduk,
            'berat_gram' => 500,
        ]);
    }

    public function test_product_description_migration_sets_design_aligned_copy(): void
    {
        $this->createProduct('Strawberry Segar 250g', 'Deskripsi lama', 20000, 250);
        $this->createProduct('Keripik Strawberry 100g', 'Deskripsi lama', 15000, 100);
        $this->createProduct('Selai Strawberry 200g', 'Deskripsi lama', 25000, 200);

        $migration = require base_path('database/migrations/2026_10_09_000003_update_strawberry_product_descriptions.php');
        $migration->up();

        $this->get(route('produk.show', 1))
            ->assertOk()
            ->assertSee('Strawberry segar pilihan langsung dari Ciwidey.')
            ->assertSee('camilan sehat.');

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Keripik Strawberry 100g',
            'deskripsi' => 'Nikmati kerenyahan keripik strawberry dengan rasa manis dan asam yang khas. Dibuat dari buah strawberry pilihan, cocok menjadi camilan praktis untuk menemani aktivitasmu.',
        ]);

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Selai Strawberry 200g',
            'deskripsi' => 'Selai strawberry dengan rasa manis dan segar dari buah pilihan. Teksturnya lembut dan mudah dioles, nikmat disajikan bersama roti, kue, atau camilan favoritmu.',
        ]);
    }

    private function createProduct(string $name, string $description, int $price, int $weight): int
    {
        $id = DB::table('produk')->insertGetId([
            'nama_produk' => $name,
            'deskripsi' => $description,
            'harga_produk' => $price,
            'stok_produk' => 10,
            'foto_produk' => null,
            'berat_gram' => $weight,
        ], 'id_produk');

        if (\App\Support\ProductCatalog::isFreshStrawberry($name)) {
            foreach (\App\Support\ProductCatalog::GRADE_PRICES as $grade => $gradePrice) {
                DB::table('produk_grade')->insert([
                    'id_produk' => $id,
                    'grade' => $grade,
                    'harga_per_kg' => $gradePrice,
                    'stok_kg' => 10,
                ]);
            }
        }

        return $id;
    }
}
