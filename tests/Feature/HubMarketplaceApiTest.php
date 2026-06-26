<?php

namespace Tests\Feature;

use App\Services\HubMarketplaceApi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HubMarketplaceApiTest extends TestCase
{
    public function test_all_products_fetches_pages_and_spreads_owners(): void
    {
        Cache::flush();

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
                    'meta' => ['page' => 1, 'per_page' => 240, 'total' => 4, 'last_page' => 2],
                ])
                ->push([
                    'data' => [
                        $this->product(3, 20, 'OTRO'),
                        $this->product(4, 30, 'TERCERO'),
                    ],
                    'meta' => ['page' => 2, 'per_page' => 240, 'total' => 4, 'last_page' => 2],
                ]),
        ]);

        $products = app(HubMarketplaceApi::class)->allProducts(['sort' => 'newest']);

        $this->assertSame([10, 20, 30, 10], collect($products['data'])->pluck('owner.id')->all());
        Http::assertSentCount(2);
    }

    public function test_all_products_uses_cache_and_stops_on_rate_limit_after_data(): void
    {
        Cache::flush();

        config([
            'services.hub.base_url' => 'https://hub.test',
            'services.hub.token' => 'token',
        ]);

        Http::fake([
            'hub.test/api/m57/catalog/products*' => Http::sequence()
                ->push([
                    'data' => [$this->product(1, 10, 'BUMERANG')],
                    'meta' => ['page' => 1, 'per_page' => 240, 'total' => 500, 'last_page' => 3],
                ])
                ->push(['success' => false, 'message' => 'Too Many Attempts.', 'status' => 429], 429),
        ]);

        $service = app(HubMarketplaceApi::class);

        $this->assertSame([1], collect($service->allProducts(['sort' => 'newest'])['data'])->pluck('id')->all());
        $this->assertSame([1], collect($service->allProducts(['sort' => 'newest'])['data'])->pluck('id')->all());
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
