<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceOrder;
use App\Models\MarketplaceOrderItem;
use App\Models\MarketplaceOrderStore;
use App\Services\HubMarketplaceApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function __construct(
        private readonly HubMarketplaceApi $hub
    ) {
    }

    public function index(Request $request): View
    {
        if ($request->integer('category') > 0) {
            return $this->category($request, $request->integer('category'));
        }

        $categories = $this->hub->categories();
        $products = $this->paginatedHomeProducts($request);

        return view('storefront.index', [
            'products' => $products['data'],
            'meta' => $products['meta'],
            'categories' => $categories,
            'cartCount' => $this->cartCount(),
            'filters' => [
                'q' => $request->string('q')->toString(),
                'category' => $request->integer('category'),
                'sort' => $request->string('sort')->toString() ?: 'newest',
            ],
        ]);
    }

    public function homeProducts(Request $request): JsonResponse
    {
        $products = $this->paginatedHomeProducts($request);

        return response()->json([
            'html' => view('storefront.partials.product-cards', [
                'products' => $products['data'],
                'chip' => 'HOT',
                'showOldPrice' => true,
                'withActions' => true,
                'showMeta' => true,
            ])->render(),
            'meta' => $products['meta'],
        ]);
    }

    private function paginatedHomeProducts(Request $request): array
    {
        $products = $this->hub->allProducts([
            'q' => $request->string('q')->toString(),
            'category' => $request->integer('category'),
            'sort' => $request->string('sort')->toString() ?: 'newest',
        ]);
        $items = collect($this->homeFashionProducts($products['data'] ?? []))->values();
        $page = max(1, $request->integer('page', 1));
        $perPage = 24;

        return [
            'data' => $items->forPage($page, $perPage)->values()->all(),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $items->count(),
                'last_page' => max(1, (int) ceil($items->count() / $perPage)),
            ],
        ];
    }

    public function category(Request $request, int $categoryId): View
    {
        $categories = collect($this->hub->categories());
        $category = $categories->firstWhere('id', $categoryId);
        abort_if(!$category, 404);

        $sort = $request->string('sort')->toString() ?: 'newest';
        $products = $this->hub->products([
            'q' => $request->string('q')->toString(),
            'category' => $categoryId,
            'sort' => $sort,
            'page' => max(1, $request->integer('page', 1)),
            'per_page' => 24,
        ]);

        $initialProducts = collect($products['data'] ?? [])->values();

        return view('storefront.category', [
            'category' => $category,
            'categories' => $categories->values()->all(),
            'products' => $initialProducts->all(),
            'meta' => $products['meta'] ?? [],
            'cartCount' => $this->cartCount(),
            'filters' => [
                'q' => $request->string('q')->toString(),
                'sort' => $sort,
            ],
            'filterFacets' => [
                'sizes' => $initialProducts->pluck('sizes')->flatten()->filter()->unique()->sort()->values()->all(),
                'colors' => $initialProducts->pluck('colors')->flatten()->filter()->unique()->values()->all(),
                'brands' => $initialProducts->pluck('owner.name')->filter()->unique()->sort()->values()->all(),
                'price_min' => (int) floor((float) $initialProducts->min('price')),
                'price_max' => (int) ceil((float) $initialProducts->max('price')),
            ],
        ]);
    }

    public function categoryProducts(Request $request, int $categoryId): JsonResponse
    {
        return response()->json($this->hub->products([
            'q' => $request->string('q')->toString(),
            'category' => $categoryId,
            'sort' => $request->string('sort')->toString() ?: 'newest',
            'page' => max(1, $request->integer('page', 1)),
            'per_page' => 24,
        ]));
    }

    public function show(int $productId): View
    {
        $product = $this->hub->product($productId);

        return view('storefront.product', [
            'product' => $product,
            'productState' => $this->productStatePayload($product),
            'recommendedProducts' => $this->recommendedProducts($product),
            'recentProducts' => $this->rememberRecentProduct($product),
            'cartCount' => $this->cartCount(),
        ]);
    }

    public function productState(Request $request, int $productId): JsonResponse
    {
        $product = $this->hub->product($productId);
        $variation = $this->resolveVariation(
            $product,
            $request->integer('variation_id') ?: null,
            $request->string('color')->toString() ?: null,
            $request->string('size')->toString() ?: null,
        );

        return response()->json([
            'data' => $this->productStatePayload($product, $variation),
        ]);
    }

    public function cart(): View
    {
        return view('storefront.cart', [
            'cartItems' => $this->cartItems(),
            'cartCount' => $this->cartCount(),
            'cartTotal' => $this->cartTotal(),
        ]);
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|integer|min:1',
            'variation_id' => 'nullable|integer|min:1',
            'quantity' => 'required|integer|min:1|max:20',
        ]);

        $product = $this->hub->product((int) $data['product_id']);
        $variationId = isset($data['variation_id']) ? (int) $data['variation_id'] : null;
        $variation = collect($product['variations'] ?? [])->firstWhere('id', $variationId);

        if (($product['type'] ?? 'simple') === 'variable' && !$variation) {
            return back()->withErrors(['variation_id' => 'Debes elegir una variación.'])->withInput();
        }

        $key = $this->cartKey((int) $data['product_id'], $variationId);
        $cart = $this->cartData();
        $current = $cart[$key]['quantity'] ?? 0;
        $quantity = min(20, $current + (int) $data['quantity']);

        $cart[$key] = [
            'product_id' => (int) $product['id'],
            'variation_id' => $variationId,
            'name' => (string) $product['name'],
            'sku' => (string) ($variation['sku'] ?? $product['sku'] ?? ''),
            'variation_name' => $variation['name'] ?? null,
            'attributes' => $variation['attributes'] ?? [],
            'owner_id' => (int) data_get($product, 'owner.id', 0),
            'owner_name' => (string) data_get($product, 'owner.name', 'Hub'),
            'image' => $variation['image'] ?? ($product['images'][0] ?? null),
            'price' => (float) ($variation['price'] ?? $product['price'] ?? 0),
            'quantity' => $quantity,
        ];

        session(['cart' => $cart]);

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.show');
        }

        return redirect()->route('cart.show')->with('status', 'Producto agregado al carrito.');
    }

    public function updateCart(Request $request, string $itemKey): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1|max:20',
        ]);

        $cart = $this->cartData();
        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] = (int) $data['quantity'];
            session(['cart' => $cart]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'cart' => $this->cartSnapshot(),
            ]);
        }

        return back();
    }

    public function removeFromCart(string $itemKey): RedirectResponse|JsonResponse
    {
        $cart = $this->cartData();
        unset($cart[$itemKey]);
        session(['cart' => $cart]);

        if (request()->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'cart' => $this->cartSnapshot(),
            ]);
        }

        return back();
    }

    public function checkout(): View
    {
        abort_if($this->cartCount() === 0, 404);

        return view('storefront.checkout', [
            'cartItems' => $this->cartItems(),
            'cartCount' => $this->cartCount(),
            'cartTotal' => $this->cartTotal(),
            'departments' => $this->safeDepartments(),
        ]);
    }

    public function checkoutDepartments(): JsonResponse
    {
        return response()->json([
            'data' => $this->safeDepartments(),
        ]);
    }

    public function checkoutCities(string $department): JsonResponse
    {
        try {
            return response()->json([
                'data' => $this->hub->cities($department),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'data' => [],
                'message' => 'No se pudieron cargar las ciudades.',
            ], 500);
        }
    }

    public function checkoutMapPreview(Request $request): JsonResponse
    {
        $query = collect([
            $request->string('address')->toString(),
            $request->string('locality')->toString(),
            $request->string('city')->toString(),
            $request->string('state')->toString(),
            $request->string('country')->toString() ?: 'Colombia',
        ])->filter()->implode(', ');

        if (blank($query)) {
            return response()->json(['data' => null]);
        }

        $token = (string) config('services.mapbox.secret_token');
        if (blank($token)) {
            return response()->json([
                'data' => null,
                'message' => 'Mapbox no está configurado.',
            ], 500);
        }

        try {
            $geo = Http::timeout(12)
                ->acceptJson()
                ->get('https://api.mapbox.com/geocoding/v5/mapbox.places/' . rawurlencode($query) . '.json', [
                    'access_token' => $token,
                    'limit' => 1,
                    'country' => 'co',
                    'language' => 'es',
                ])
                ->throw()
                ->json();

            $feature = $geo['features'][0] ?? null;
            $center = $feature['center'] ?? null;
            if (!is_array($center) || count($center) < 2) {
                return response()->json(['data' => null]);
            }

            [$lng, $lat] = $center;
            $static = Http::timeout(15)
                ->get(sprintf(
                    'https://api.mapbox.com/styles/v1/mapbox/streets-v12/static/pin-s+ff1f49(%s,%s)/%s,%s,13,0/760x340@2x',
                    $lng,
                    $lat,
                    $lng,
                    $lat
                ), [
                    'access_token' => $token,
                ])
                ->throw();

            return response()->json([
                'data' => [
                    'label' => $feature['place_name_es'] ?? $feature['place_name'] ?? $query,
                    'lat' => $lat,
                    'lng' => $lng,
                    'image_url' => 'data:image/png;base64,' . base64_encode($static->body()),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'data' => null,
                'message' => 'No se pudo cargar el mapa.',
            ], 500);
        }
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        abort_if($this->cartCount() === 0, 404);

        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:40',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:120',
            'shipping_state' => 'required|string|max:120',
            'shipping_locality' => 'nullable|string|max:255',
            'payment_type' => 'required|string|in:cod,anticipado,anticipado_parcial',
            'payment_channel' => 'nullable|string|max:80',
            'notes' => 'nullable|string|max:2000',
        ]);

        $cartItems = $this->cartItems();
        $payload = [
            'customer' => [
                'name' => $data['customer_name'],
                'email' => $data['customer_email'] ?? null,
                'phone' => $data['customer_phone'],
                'address' => $data['shipping_address'],
                'city' => $data['shipping_city'],
                'state' => $data['shipping_state'],
                'locality' => $data['shipping_locality'] ?? null,
            ],
            'payment_type' => $data['payment_type'],
            'payment_channel' => $data['payment_channel'] ?? null,
            'notes' => $data['notes'] ?? null,
            'items' => collect($cartItems)->map(fn (array $item) => [
                'product_id' => $item['product_id'],
                'variation_id' => $item['variation_id'],
                'quantity' => $item['quantity'],
            ])->values()->all(),
        ];

        try {
            $response = $this->hub->checkout($payload);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'checkout' => 'No se pudo crear la orden en Hub: ' . $e->getMessage(),
            ]);
        }

        $marketOrder = DB::transaction(function () use ($data, $response, $cartItems) {
            $order = MarketplaceOrder::create([
                'batch_code' => $response['batch_code'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'shipping_state' => $data['shipping_state'],
                'shipping_locality' => $data['shipping_locality'] ?? null,
                'payment_type' => $data['payment_type'],
                'payment_channel' => $data['payment_channel'] ?? null,
                'notes' => $data['notes'] ?? null,
                'subtotal_amount' => $this->cartTotal(),
                'total_amount' => (float) ($response['grand_total'] ?? $this->cartTotal()),
                'status' => 'created',
                'hub_response' => $response,
            ]);

            $stores = [];
            foreach ($response['orders'] ?? [] as $group) {
                $store = MarketplaceOrderStore::create([
                    'marketplace_order_id' => $order->id,
                    'owner_user_id' => (int) $group['owner_id'],
                    'owner_name' => (string) $group['owner_name'],
                    'hub_order_id' => (int) ($group['order_id'] ?? 0),
                    'hub_order_number' => (string) ($group['order_number'] ?? ''),
                    'status' => (string) ($group['status'] ?? 'pending'),
                    'subtotal_amount' => (float) ($group['subtotal'] ?? 0),
                    'total_amount' => (float) ($group['total'] ?? 0),
                    'hub_payload' => $group,
                ]);

                $stores[$store->owner_user_id] = $store;
            }

            foreach ($cartItems as $item) {
                $store = $stores[$item['owner_id']] ?? null;
                if (!$store) {
                    continue;
                }

                MarketplaceOrderItem::create([
                    'marketplace_order_store_id' => $store->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'],
                    'variation_name' => $item['variation_name'],
                    'owner_user_id' => $item['owner_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'line_total' => round($item['price'] * $item['quantity'], 2),
                    'snapshot' => $item,
                ]);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('checkout.success', $marketOrder);
    }

    public function success(MarketplaceOrder $marketplaceOrder): View
    {
        return view('storefront.success', [
            'order' => $marketplaceOrder->load('stores.items'),
            'cartCount' => $this->cartCount(),
        ]);
    }

    private function cartData(): array
    {
        return session('cart', []);
    }

    private function cartItems(): array
    {
        return array_values($this->cartData());
    }

    private function cartCount(): int
    {
        return (int) collect($this->cartData())->sum('quantity');
    }

    private function cartTotal(): float
    {
        return round((float) collect($this->cartData())->sum(fn (array $item) => $item['price'] * $item['quantity']), 2);
    }

    private function cartKey(int $productId, ?int $variationId): string
    {
        return $productId . ':' . ($variationId ?: 0);
    }

    private function cartSnapshot(): array
    {
        return [
            'items' => $this->cartItems(),
            'count' => $this->cartCount(),
            'total' => $this->cartTotal(),
        ];
    }

    private function safeDepartments(): array
    {
        try {
            return $this->hub->departments();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function homeFashionProducts(array $products): array
    {
        $allowed = collect([
            'Moda',
            'Ropa',
            'Ropa y Accesorios',
            'Accesorios',
            'Mujer',
            'Curvy',
            'Niños',
            'Hombre',
            'Conjuntos',
            'Bolsas & Maletas',
            'Bolsos',
            'Bottoms',
            'Joyería y Accesorios',
            'Joyería',
            'Tops',
            'Bebé',
            'Bebé y Maternidad',
            'Ropa Interior y Pijamas',
            'Mezclilla',
            'Enterizos para mujer',
            'Ropa de Playa',
            'Vestidos',
            'Zapatos',
            'Calzado',
        ])->map(fn (string $name) => $this->categoryKey($name))->all();

        return collect($products)
            ->filter(function (array $product) use ($allowed) {
                if (in_array($product['is_visible_in_catalog'] ?? true, [false, 0, '0'], true)) {
                    return false;
                }

                $names = collect($product['categories'] ?? [])
                    ->pluck('name')
                    ->push($product['category_name'] ?? null)
                    ->filter()
                    ->map(fn (string $name) => $this->categoryKey($name));

                return $names->intersect($allowed)->isNotEmpty();
            })
            ->values()
            ->all();
    }

    private function categoryKey(string $name): string
    {
        return \Illuminate\Support\Str::of($name)->lower()->ascii()->replace('&', ' y ')->squish()->value();
    }

    private function productStatePayload(array $product, ?array $variation = null): array
    {
        $variations = collect($product['variations'] ?? []);
        $selected = $variation ?: $variations->firstWhere('stock', '>', 0) ?: $variations->first();
        $selectedColor = data_get($selected, 'attributes.Color');
        $selectedSize = data_get($selected, 'attributes.Talla');
        $price = (float) ($selected['price'] ?? $product['price'] ?? 0);
        $oldPrice = round($price * 1.18, 0);
        $media = collect([$selected['image'] ?? null])
            ->merge($product['images'] ?? [])
            ->merge($variations->pluck('image'))
            ->filter()
            ->unique()
            ->values()
            ->map(fn (string $src) => [
                'type' => preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $src) ? 'video' : 'image',
                'src' => $src,
            ])
            ->all();

        $sizes = $variations
            ->filter(fn (array $item) => data_get($item, 'attributes.Color') === $selectedColor)
            ->values()
            ->map(fn (array $item) => [
                'name' => (string) data_get($item, 'attributes.Talla', $item['name']),
                'variation_id' => (int) $item['id'],
                'stock' => (int) ($item['stock'] ?? 0),
                'status' => (int) ($item['stock'] ?? 0) < 1 ? 'soldout' : ((int) ($item['stock'] ?? 0) < 4 ? 'low' : 'available'),
                'selected' => (int) $item['id'] === (int) data_get($selected, 'id', 0),
            ])
            ->all();

        return [
            'variation_id' => data_get($selected, 'id'),
            'color' => $selectedColor,
            'size' => $selectedSize,
            'sku' => (string) ($selected['sku'] ?? $product['sku'] ?? ''),
            'price' => $price,
            'old_price' => $oldPrice,
            'discount_percent' => max(0, (int) round((1 - ($price / max($oldPrice, 1))) * 100)),
            'stock' => (int) ($selected['stock'] ?? $product['stock'] ?? 0),
            'stock_label' => (int) ($selected['stock'] ?? $product['stock'] ?? 0) < 1 ? 'Agotado' : ((int) ($selected['stock'] ?? $product['stock'] ?? 0) < 4 ? 'Pocas unidades' : 'Disponible'),
            'installments' => $price >= 60000 ? 3 : null,
            'media' => $media,
            'sizes' => $sizes,
        ];
    }

    private function resolveVariation(array $product, ?int $variationId = null, ?string $color = null, ?string $size = null): ?array
    {
        $variations = collect($product['variations'] ?? []);

        if ($variationId) {
            return $variations->firstWhere('id', $variationId);
        }

        $filtered = $variations
            ->when($color, fn ($items) => $items->filter(fn (array $item) => data_get($item, 'attributes.Color') === $color))
            ->when($size, fn ($items) => $items->filter(fn (array $item) => data_get($item, 'attributes.Talla') === $size));

        return $filtered->firstWhere('stock', '>', 0) ?: $filtered->first();
    }

    private function recommendedProducts(array $product): array
    {
        $categoryId = (int) data_get($product, 'categories.0.id', 0);

        return collect($this->hub->products([
            'category' => $categoryId ?: null,
            'sort' => 'best_selling',
            'page' => 1,
        ])['data'] ?? [])
            ->reject(fn (array $item) => (int) $item['id'] === (int) $product['id'])
            ->take(8)
            ->values()
            ->all();
    }

    private function rememberRecentProduct(array $product): array
    {
        $recent = collect(session('recent_products', []))
            ->reject(fn (array $item) => (int) $item['id'] === (int) $product['id'])
            ->prepend([
                'id' => (int) $product['id'],
                'name' => (string) ($product['name'] ?? ''),
                'price' => (float) ($product['price'] ?? 0),
                'image' => $product['images'][0] ?? null,
                'owner' => $product['owner'] ?? ['id' => 0, 'name' => 'Hub'],
            ])
            ->take(10)
            ->values();

        session(['recent_products' => $recent->all()]);

        return $recent
            ->slice(1)
            ->take(8)
            ->values()
            ->all();
    }
}
