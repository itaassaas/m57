<?php

namespace Tests\Feature;

use App\Services\HubMarketplaceApi;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HubMarketplaceApiTest extends TestCase
{
    public function test_all_products_fetches_pages_and_spreads_owners(): void
    {
        config([
            'services.hub.base_url' => 'https://hub.test',
            'services.hub.token' => 'token',
        ]);

        Http::fake([
            'hub.test/api/m57/catalog/products*' => Http::sequence()
                ->push([
                    'data' => [
                        $this->product(1, 10, 'BUMERANG'),
                        $this->product(2, 10, 'BUMERANG'),
                    ],
                    'meta' => ['page' => 1, 'per_page' => 2, 'total' => 4, 'last_page' => 2],
                ])
                ->push([
                    'data' => [
                        $this->product(3, 20, 'OTRO'),
                        $this->product(4, 30, 'TERCERO'),
                    ],
                    'meta' => ['page' => 2, 'per_page' => 2, 'total' => 4, 'last_page' => 2],
                ]),
        ]);

        $products = app(HubMarketplaceApi::class)->allProducts(['sort' => 'newest']);

        $this->assertSame([10, 20, 30, 10], collect($products['data'])->pluck('owner.id')->all());
        Http::assertSentCount(2);
    }

    private function product(int $id, int $ownerId, string $ownerName): array
    {
        return [
            'id' => $id,
            'name' => 'Producto ' . $id,
            'owner' => ['id' => $ownerId, 'name' => $ownerName],
        ];
    }
}
