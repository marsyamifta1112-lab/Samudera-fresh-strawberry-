<?php

namespace Tests\Unit;

use App\Support\ShippingZoneResolver;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class ShippingZoneResolverTest extends TestCase
{
    public function test_it_resolves_addresses_to_their_shipping_zone(): void
    {
        $this->assertSame(
            'Ciwidey & Rancabali',
            ShippingZoneResolver::resolveName('Jl. Raya Ciwidey, Kabupaten Bandung')
        );
        $this->assertSame(
            'Pasirjambu & Soreang',
            ShippingZoneResolver::resolveName('Soreang, Kabupaten Bandung')
        );
        $this->assertSame(
            'Bandung Raya (Bandung, Cimahi, Bandung Barat)',
            ShippingZoneResolver::resolveName('Cimahi, Jawa Barat')
        );
        $this->assertSame(
            'Luar Bandung Raya',
            ShippingZoneResolver::resolveName('Garut, Jawa Barat')
        );
    }

    public function test_shipping_cost_never_exceeds_the_product_subtotal(): void
    {
        $this->assertSame(25000, ShippingZoneResolver::cost(50000, 25000));
        $this->assertSame(10000, ShippingZoneResolver::cost(10000, 25000));
        $this->assertSame(0, ShippingZoneResolver::cost(10000, 0));
    }

    public function test_it_resolves_a_zone_using_available_zone_records(): void
    {
        $zones = new Collection([
            (object) ['nama_wilayah' => 'Bandung Raya', 'tarif' => 25000],
            (object) ['nama_wilayah' => 'Luar Bandung Raya', 'tarif' => 50000],
        ]);

        $this->assertSame(
            'Bandung Raya',
            ShippingZoneResolver::resolve($zones, 'Cimahi')->nama_wilayah
        );
        $this->assertSame(
            'Luar Bandung Raya',
            ShippingZoneResolver::resolve($zones, 'Garut')->nama_wilayah
        );
    }
}
