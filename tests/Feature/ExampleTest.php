<?php

namespace Tests\Feature;

use App\Services\HubMarketplaceApi;
use Mockery;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_renders_marketplace_products(): void
    {
        $mock = Mockery::mock(HubMarketplaceApi::class);
        $mock->shouldReceive('products')->once()->andReturn([
            'data' => [[
                'id' => 1,
                'name' => 'Vestido prueba',
                'sku' => 'SKU-1',
                'price' => 99000,
                'stock' => 5,
                'image' => 'https://example.com/product.jpg',
                'secondary_image' => null,
                'owner' => ['id' => 10, 'name' => 'Hub Demo'],
                'categories' => [['id' => 2, 'name' => 'Moda']],
                'category_name' => 'Moda',
                'is_featured' => true,
                'views_count' => 10,
                'created_at_ts' => now()->timestamp,
                'type' => 'simple',
            ]],
            'meta' => ['page' => 1, 'per_page' => 24, 'total' => 1, 'last_page' => 1],
        ]);
        $mock->shouldReceive('categories')->once()->andReturn([
            ['id' => 2, 'name' => 'Moda'],
        ]);

        $this->app->instance(HubMarketplaceApi::class, $mock);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('M57')
            ->assertSee('Vestido prueba')
            ->assertSee('Hub Demo');
    }

    public function test_product_page_renders_redesigned_pdp(): void
    {
        $mock = Mockery::mock(HubMarketplaceApi::class);
        $mock->shouldReceive('product')->once()->with(1917)->andReturn($this->fakeVariableProduct());
        $mock->shouldReceive('products')->once()->andReturn([
            'data' => [[
                'id' => 1918,
                'name' => 'Jean Wide Leg',
                'sku' => 'SKU-2',
                'price' => 119000,
                'stock' => 8,
                'image' => 'https://example.com/jean.jpg',
                'secondary_image' => null,
                'owner' => ['id' => 10, 'name' => 'Hub Demo'],
                'categories' => [['id' => 11, 'name' => 'Moda']],
                'category_name' => 'Moda',
                'is_featured' => false,
                'views_count' => 4,
                'created_at_ts' => now()->timestamp,
                'type' => 'simple',
            ]],
        ]);

        $this->app->instance(HubMarketplaceApi::class, $mock);

        $response = $this->get('/products/1917');

        $response->assertOk()
            ->assertSee('Comprar ahora')
            ->assertSee('Guia de tallas')
            ->assertSee('Reseñas', false)
            ->assertSee('Camiseta tejida manga corta con cuello en V para mujer');
    }

    public function test_product_state_endpoint_returns_selected_variation(): void
    {
        $mock = Mockery::mock(HubMarketplaceApi::class);
        $mock->shouldReceive('product')->once()->with(1917)->andReturn($this->fakeVariableProduct());

        $this->app->instance(HubMarketplaceApi::class, $mock);

        $response = $this->getJson('/products/1917/state?color=Morado&size=L');

        $response->assertOk()
            ->assertJsonPath('data.color', 'Morado')
            ->assertJsonPath('data.size', 'L')
            ->assertJsonPath('data.variation_id', 497)
            ->assertJsonPath('data.sku', 'CAMISETA-TEJIDA-MA-384855-04');
    }

    public function test_checkout_renders_redesigned_layout(): void
    {
        $response = $this->withSession([
            'cart' => [
                '1:0' => [
                    'product_id' => 1,
                    'variation_id' => null,
                    'name' => 'Vestido prueba',
                    'sku' => 'SKU-1',
                    'variation_name' => null,
                    'attributes' => ['Color' => 'Negro', 'Talla' => 'M'],
                    'owner_id' => 10,
                    'owner_name' => 'Hub Demo',
                    'image' => 'https://example.com/product.jpg',
                    'price' => 99000,
                    'quantity' => 2,
                ],
            ],
        ])->get('/checkout');

        $response->assertOk()
            ->assertSee('Resumen del pedido')
            ->assertSee('Método de envío', false)
            ->assertSee('Continuar')
            ->assertSee('M57SAVE10');
    }

    public function test_cart_update_returns_json_for_ajax_requests(): void
    {
        $response = $this->withSession([
            'cart' => [
                '1:0' => [
                    'product_id' => 1,
                    'variation_id' => null,
                    'name' => 'Vestido prueba',
                    'sku' => 'SKU-1',
                    'variation_name' => null,
                    'attributes' => [],
                    'owner_id' => 10,
                    'owner_name' => 'Hub Demo',
                    'image' => 'https://example.com/product.jpg',
                    'price' => 99000,
                    'quantity' => 1,
                ],
            ],
        ])->patchJson('/cart/1:0', ['quantity' => 3]);

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('cart.count', 3)
            ->assertJsonPath('cart.total', 297000);
    }

    private function fakeVariableProduct(): array
    {
        return [
            'id' => 1917,
            'name' => 'Camiseta tejida manga corta con cuello en V para mujer',
            'description' => '',
            'sku' => 'PRD-6A2DB55D6E2E6',
            'price' => 28000,
            'stock' => 38,
            'type' => 'variable',
            'owner' => ['id' => 600, 'name' => 'FErnando Guzman'],
            'images' => ['https://example.com/base.webp'],
            'categories' => [['id' => 11, 'name' => 'Moda']],
            'variations' => [
                [
                    'id' => 494,
                    'sku' => 'CAMISETA-TEJIDA-MA-384854-01',
                    'name' => 'Color: Beish · Talla: M',
                    'attributes' => ['Color' => 'Beish', 'Talla' => 'M'],
                    'price' => 28000,
                    'stock' => 9,
                    'image' => 'https://example.com/beish-m.webp',
                ],
                [
                    'id' => 495,
                    'sku' => 'CAMISETA-TEJIDA-MA-384855-02',
                    'name' => 'Color: Beish · Talla: L',
                    'attributes' => ['Color' => 'Beish', 'Talla' => 'L'],
                    'price' => 28000,
                    'stock' => 10,
                    'image' => 'https://example.com/beish-l.webp',
                ],
                [
                    'id' => 496,
                    'sku' => 'CAMISETA-TEJIDA-MA-384855-03',
                    'name' => 'Color: Morado · Talla: M',
                    'attributes' => ['Color' => 'Morado', 'Talla' => 'M'],
                    'price' => 28000,
                    'stock' => 10,
                    'image' => 'https://example.com/morado-m.webp',
                ],
                [
                    'id' => 497,
                    'sku' => 'CAMISETA-TEJIDA-MA-384855-04',
                    'name' => 'Color: Morado · Talla: L',
                    'attributes' => ['Color' => 'Morado', 'Talla' => 'L'],
                    'price' => 28000,
                    'stock' => 9,
                    'image' => 'https://example.com/morado-l.webp',
                ],
            ],
        ];
    }
}
