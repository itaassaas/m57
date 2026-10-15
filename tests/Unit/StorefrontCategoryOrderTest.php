<?php

namespace Tests\Unit;

use App\Http\Controllers\StorefrontController;
use App\Services\HubMarketplaceApi;
use Mockery;
use ReflectionMethod;
use Tests\TestCase;

class StorefrontCategoryOrderTest extends TestCase
{
    public function test_explicit_category_priorities_are_applied_inside_m57(): void
    {
        $controller = new StorefrontController(Mockery::mock(HubMarketplaceApi::class));
        $method = new ReflectionMethod($controller, 'orderedCategories');

        $categories = $method->invoke($controller, [
            ['id' => 30, 'name' => 'Orden por defecto', 'sort_order' => 0],
            ['id' => 20, 'name' => 'Prioridad dos', 'sort_order' => 2],
            ['id' => 10, 'name' => 'Prioridad uno', 'sort_order' => 1],
        ]);

        $this->assertSame([10, 20, 30], array_column($categories, 'id'));
    }
}
