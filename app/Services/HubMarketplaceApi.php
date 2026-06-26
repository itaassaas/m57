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

    public function allProducts(array $query = []): array
    {
        $query['per_page'] = 48;
        $items = collect();
        $meta = [];

        for ($page = 1; $page <= 50; $page++) {
            $response = $this->products(array_merge($query, ['page' => $page]));
            $data = collect($response['data'] ?? []);
            $meta = $response['meta'] ?? $meta;

            if ($data->isEmpty()) {
                break;
            }

            $items = $items->merge($data);

            if ($page >= (int) ($meta['last_page'] ?? $page)) {
                break;
            }
        }

        $items = $this->spreadByOwner($items->unique('id')->values());

        return [
            'data' => $items->values()->all(),
            'meta' => [
                'page' => 1,
                'per_page' => $items->count(),
                'total' => (int) ($meta['total'] ?? $items->count()),
                'last_page' => 1,
            ],
        ];
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

    private function spreadByOwner($items)
    {
        $groups = $items->groupBy(fn (array $item) => data_get($item, 'owner.id', 0))->map->values();
        $spread = collect();

        while ($groups->isNotEmpty()) {
            foreach ($groups->keys() as $ownerId) {
                $group = $groups->get($ownerId);
                $spread->push($group->shift());

                $group->isEmpty()
                    ? $groups->forget($ownerId)
                    : $groups->put($ownerId, $group);
            }
        }

        return $spread;
    }
}
