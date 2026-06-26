<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HubMarketplaceApi
{
    public function categories(): array
    {
        return $this->client()->get('/api/m57/catalog/categories')->throw()->json('data') ?? [];
    }

    public function products(array $query = []): array
    {
        return $this->client()->get('/api/m57/catalog/products', $query)->throw()->json();
    }

    public function product(int $productId): array
    {
        return $this->client()->get("/api/m57/catalog/products/{$productId}")->throw()->json('data') ?? [];
    }

    public function checkout(array $payload): array
    {
        return $this->client()->post('/api/m57/checkout', $payload)->throw()->json('data') ?? [];
    }

    public function departments(): array
    {
        return $this->client()->get('/api/m57/locations/departments')->throw()->json('data') ?? [];
    }

    public function cities(string $departmentId): array
    {
        return $this->client()->get("/api/m57/locations/departments/{$departmentId}/cities")->throw()->json('data') ?? [];
    }

    private function client()
    {
        return Http::baseUrl(rtrim((string) config('services.hub.base_url'), '/'))
            ->withToken((string) config('services.hub.token'))
            ->acceptJson()
            ->timeout(20);
    }
}
