@extends('storefront.layout', ['title' => 'M57 Catalogo', 'cartCount' => $cartCount])

@section('content')
    @php
        $allProducts = collect($products)->values();
        $storefrontConfig = $storefrontConfig ?? [];
        $storefrontSections = collect($storefrontSections ?? [])->keyBy('code');
        $sectionItems = fn (string $code, $fallback) => collect(data_get($storefrontSections->get($code), 'items', []))
            ->filter(fn ($item) => isset($item['id']))
            ->values()
            ->whenEmpty(fn () => collect($fallback))
            ->take(5);
        $heroConfig = data_get($storefrontConfig, 'hero', []);
        $brandConfig = data_get($storefrontConfig, 'brand', []);
        $featured = $sectionItems('featured_products', $allProducts->take(5));
        $newCollection = $sectionItems('new_collection', $allProducts->slice(5, 5));
        $flashSale = $sectionItems('flash_sale', $allProducts->slice(10, 5));
        $trending = $sectionItems('trending', $allProducts->slice(15, 5));
        $premium = $sectionItems('premium', $allProducts->slice(20, 5));
        $megaFeatured = $allProducts->take(3);
        $legacyHeroSlides = [
            [
                'badge' => '✨ NUEVA TEMPORADA',
                'title' => str_replace('|', "\n", data_get($heroConfig, 'title', "Descubre\ntu nuevo\nestilo favorito")),
                'subtitle' => data_get($heroConfig, 'subtitle', data_get($brandConfig, 'tagline', 'Mas de 2.000 referencias nuevas cada semana.')),
                'cta' => 'Comprar ahora',
                'cta_href' => '#catalogo',
                'cta_variant' => 'light',
                'theme' => 'midnight',
                'image_url' => data_get($heroConfig, 'image_url'),
                'products' => $allProducts->take(4),
            ],
            [
                'badge' => '☀️ SUMMER DROP',
                'title' => "Colores que\nmarcan la\ntemporada",
                'subtitle' => 'Vestidos, accesorios y tendencias para este verano.',
                'cta' => 'Explorar coleccion',
                'cta_href' => '#catalogo',
                'cta_variant' => 'ghost',
                'theme' => 'summer',
                'image_url' => null,
                'products' => $allProducts->slice(4, 4),
            ],
        ];
        $configuredHeroSlides = collect(data_get($storefrontConfig, 'hero_slides', []))
            ->filter(fn ($slide) => is_array($slide) && ($slide['is_active'] ?? false) && (
                filled($slide['title'] ?? null)
                || filled($slide['subtitle'] ?? null)
                || filled($slide['badge'] ?? null)
                || filled($slide['image_url'] ?? null)
                || filled($slide['mobile_image_url'] ?? null)
            ))
            ->sortBy(fn (array $slide) => (int) ($slide['sort_order'] ?? 0) ?: 1000000)
            ->values()
            ->map(function (array $slide) use ($allProducts) {
                $selectedProducts = $allProducts
                    ->whereIn('id', collect($slide['product_ids'] ?? [])->map(fn ($id) => (int) $id)->all())
                    ->values();
                $showProducts = (bool) ($slide['show_products'] ?? false);

                return [
                    'badge' => $slide['badge'] ?? '',
                    'title' => str_replace('|', "\n", (string) $slide['title']),
                    'subtitle' => $slide['subtitle'] ?? '',
                    'cta' => $slide['cta_label'] ?? '',
                    'cta_href' => $slide['cta_href'] ?: '#catalogo',
                    'cta_variant' => ($slide['theme'] ?? 'midnight') === 'summer' ? 'ghost' : 'light',
                    'theme' => in_array($slide['theme'] ?? null, ['midnight', 'summer'], true) ? $slide['theme'] : 'midnight',
                    'image_url' => $slide['image_url'] ?? null,
                    'mobile_image_url' => $slide['mobile_image_url'] ?? null,
                    'content_align' => in_array($slide['content_align'] ?? null, ['left', 'center', 'right'], true) ? $slide['content_align'] : 'left',
                    'image_fit' => in_array($slide['image_fit'] ?? null, ['cover', 'contain'], true) ? $slide['image_fit'] : 'cover',
                    'image_position' => in_array($slide['image_position'] ?? null, ['left', 'center', 'right', 'top', 'bottom'], true) ? $slide['image_position'] : 'center',
                    'show_badge' => array_key_exists('show_badge', $slide) ? (bool) $slide['show_badge'] : true,
                    'show_title' => array_key_exists('show_title', $slide) ? (bool) $slide['show_title'] : true,
                    'show_subtitle' => array_key_exists('show_subtitle', $slide) ? (bool) $slide['show_subtitle'] : true,
                    'show_cta' => array_key_exists('show_cta', $slide) ? (bool) $slide['show_cta'] : true,
                    'show_overlay' => array_key_exists('show_overlay', $slide) ? (bool) $slide['show_overlay'] : true,
                    'overlay_opacity' => max(0, min(90, (int) ($slide['overlay_opacity'] ?? 55))),
                    'show_products' => $showProducts,
                    'products' => $showProducts ? $selectedProducts->whenEmpty(fn () => $allProducts->take(3)) : collect(),
                ];
            });
        $heroSlides = $configuredHeroSlides->isNotEmpty() ? $configuredHeroSlides->all() : $legacyHeroSlides;
        $megaColumns = [
            ['title' => 'Mujer', 'links' => [['Novedades', 'Mujer'], ['Vestidos', 'Vestidos'], ['Tops', 'Tops'], ['Bottoms', 'Bottoms']]],
            ['title' => 'Hombre', 'links' => [['Camisetas', 'Hombre'], ['Jeans', 'Mezclilla'], ['Overshirts', 'Hombre'], ['Sets', 'Conjuntos']]],
            ['title' => 'Calzado', 'links' => [['Sneakers', 'Zapatos'], ['Sandalias', 'Zapatos'], ['Botas', 'Zapatos'], ['Tacones', 'Zapatos']]],
            ['title' => 'Accesorios', 'links' => [['Bolsos', 'Bolsas & Maletas'], ['Joyeria', 'Joyería y Accesorios'], ['Gafas', 'Joyería y Accesorios'], ['Cinturones', 'Joyería y Accesorios']]],
            ['title' => 'Beauty', 'links' => [['Makeup', 'Belleza y salud'], ['Skincare', 'Belleza y salud'], ['Hair', 'Belleza y salud'], ['Tools', 'Belleza y salud']]],
            ['title' => 'Curado', 'links' => [['Top ventas', 'Mujer'], ['Flash sale', 'Vestidos'], ['Premium', 'Zapatos'], ['Looks', 'Conjuntos']]],
        ];
        $visualCategoryNames = [
            'Mujer',
            'Curvy',
            'Niños',
            'Hombre',
            'Conjuntos',
            'Bolsas & Maletas',
            'Bottoms',
            'Joyería y Accesorios',
            'Tops',
            'Bebé y Maternidad',
            'Ropa Interior y Pijamas',
            'Mezclilla',
            'Enterizos para mujer',
            'Ropa de Playa',
            'Vestidos',
            'Zapatos',
        ];
        $availableCategories = collect($categories)->values();
        $visualCategories = $availableCategories->keyBy('name');
        $visualCategoryItems = collect(data_get($storefrontConfig, 'visual_categories.items', []));
        $hasVisualCategoryConfiguration = data_get($storefrontConfig, 'visual_categories.items') !== null;
        $visualCategoryOrder = $visualCategoryItems
            ->keyBy(fn (array $item) => (int) ($item['category_id'] ?? 0));
        $visibleCategoryTiles = $hasVisualCategoryConfiguration
            ? $availableCategories
                ->filter(fn (array $category) => $visualCategoryOrder->has((int) ($category['id'] ?? 0)))
                ->filter(fn (array $category) => empty($category['parent_id']))
                ->map(function (array $category, int $index) use ($visualCategoryOrder) {
                    $sortOrder = (int) data_get($visualCategoryOrder->get((int) $category['id']), 'sort_order', 0);

                    return [
                        'category' => $category,
                        // 1 es la mayor prioridad; 0 conserva el orden normal al final.
                        'priority' => $sortOrder > 0 ? $sortOrder : 1000000 + $index,
                    ];
                })
                ->sortBy('priority')
                ->pluck('category')
                ->values()
            : $availableCategories;
        $categoryAliases = [
            'mujer' => 'Moda',
            'hombre' => 'Moda',
            'curvy' => 'Moda',
            'ninos' => 'Bebé',
            'conjuntos' => 'Moda',
            'celulares y accesorios' => 'Tecnología',
            'bolsas y maletas' => 'Ropa y Accesorios',
            'bottoms' => 'Ropa',
            'joyeria y accesorios' => 'Ropa y Accesorios',
            'tops' => 'Ropa',
            'bebe y maternidad' => 'Bebé',
            'ropa interior y pijamas' => 'Ropa',
            'mezclilla' => 'Ropa',
            'belleza y salud' => 'Belleza',
            'automotriz' => 'Vehículos',
            'enterizos para mujer' => 'Moda',
            'deportes y exteriores' => 'Deportes',
            'ropa de playa' => 'Moda',
            'hogar y vida' => 'Hogar y Jardín',
            'vestidos' => 'Moda',
            'zapatos' => 'Ropa y Accesorios',
            'mascotas' => 'Mascotas',
            'juguetes y juegos' => 'Jugueteria',
            'utiles escolares y de oficina' => 'Otros',
            'calzado' => 'Ropa y Accesorios',
            'accesorios' => 'Ropa y Accesorios',
            'bolsos' => 'Ropa y Accesorios',
            'beauty' => 'Belleza',
            'curado' => 'Moda',
            'novedades' => 'Moda',
            'ofertas' => 'Moda',
            'solo para ti' => 'Moda',
            'joyeria' => 'Ropa y Accesorios',
        ];
        $resolveCategory = function (string $name, ?int $fallbackIndex = null) use ($availableCategories, $visualCategories, $categoryAliases) {
            $normalize = static fn (string $value) => \Illuminate\Support\Str::of($value)->lower()->ascii()->replace('&', ' y ')->squish()->value();

            if ($category = $visualCategories->get($name)) {
                return $category;
            }

            $normalized = $normalize($name);
            $aliased = $categoryAliases[$normalized] ?? null;
            if ($aliased && ($category = $visualCategories->get($aliased))) {
                return $category;
            }

            $matched = collect($visualCategories->all())->first(function ($category) use ($normalized, $normalize) {
                return $normalize((string) ($category['name'] ?? '')) === $normalized;
            });

            if ($matched) {
                return $matched;
            }

            return null;
        };
        $visualCategoryImages = [
            'Mujer' => asset('storage/categorias-visual/Mujer.png'),
            'Curvy' => asset('storage/categorias-visual/Curvy.png'),
            'Niños' => asset('storage/categorias-visual/Niños.png'),
            'Hombre' => asset('storage/categorias-visual/Hombre.png'),
            'Conjuntos' => null,
            'Celulares y Accesorios' => asset('storage/categorias-visual/Celulares y Accesorios.png'),
            'Bolsas & Maletas' => asset('storage/categorias-visual/Bolsas & Maletas.png'),
            'Bottoms' => asset('storage/categorias-visual/Bottoms.png'),
            'Joyería y Accesorios' => asset('storage/categorias-visual/Joyeria y Accesorios.png'),
            'Tops' => asset('storage/categorias-visual/Tops.png'),
            'Bebé y Maternidad' => asset('storage/categorias-visual/Bebé y Maternidad.png'),
            'Ropa Interior y Pijamas' => asset('storage/categorias-visual/Ropa Interior y Pijamas.png'),
            'Mezclilla' => asset('storage/categorias-visual/Mezclilla.png'),
            'Belleza y salud' => asset('storage/categorias-visual/Belleza y Salud.png'),
            'Automotriz' => asset('storage/categorias-visual/Automotriz.png'),
            'Enterizos para mujer' => asset('storage/categorias-visual/Enterizos mujer.png'),
            'Deportes & Exteriores' => asset('storage/categorias-visual/Deportes & Exteriores.png'),
            'Ropa de Playa' => asset('storage/categorias-visual/Ropa de Playa.png'),
            'Hogar y Vida' => asset('storage/categorias-visual/Hogar y Vida.png'),
            'Vestidos' => asset('storage/categorias-visual/Vestidos.png'),
            'Zapatos' => asset('storage/categorias-visual/Zapatos.png'),
            'Mascotas' => asset('storage/categorias-visual/Mascotas.png'),
            'Juguetes y Juegos' => asset('storage/categorias-visual/Juegos y Juguetes.png'),
            'Útiles escolares y de oficina' => asset('storage/categorias-visual/Útiles escolares y de oficina.png'),
        ];
        $visualCategoryImageOverrides = collect(data_get($storefrontConfig, 'visual_categories.images', []))
            ->filter(fn ($url) => filled($url))
            ->all();
        $visualCategoryImages = array_replace($visualCategoryImages, $visualCategoryImageOverrides);

        // El menú y los mosaicos se derivan del catálogo publicado por Hub.
        // No se usan nombres fijos: Superadmin controla visibilidad, prioridad
        // y jerarquía de categorías/subcategorías.
        $mainCategories = $availableCategories->filter(fn (array $category) => empty($category['parent_id']))->values();
        $childrenByParent = $availableCategories
            ->filter(fn (array $category) => !empty($category['parent_id']))
            ->groupBy(fn (array $category) => (int) $category['parent_id']);
        $megaColumns = $mainCategories->map(function (array $category) use ($childrenByParent) {
            $links = $childrenByParent->get((int) $category['id'], collect())
                ->map(fn (array $child) => [
                    'id' => (int) $child['id'],
                    'label' => $child['name'],
                ])
                ->values()
                ->all();

            return [
                'title' => $category['name'],
                'id' => (int) $category['id'],
                'links' => $links ?: [[
                    'id' => (int) $category['id'],
                    'label' => 'Ver todo',
                ]],
            ];
        })->all();
        // Todas las categorías principales publicadas aparecen en inicio. La
        // selección visual ya no las oculta: solo prioriza y personaliza su imagen.
        $visibleCategoryTiles = $mainCategories
            ->map(function (array $category, int $index) use ($visualCategoryOrder) {
                $configuredOrder = (int) data_get($visualCategoryOrder->get((int) $category['id']), 'sort_order', 0);

                return [
                    'category' => $category,
                    'priority' => $configuredOrder > 0 ? $configuredOrder : 1000000 + $index,
                ];
            })
            ->sortBy('priority')
            ->pluck('category')
            ->values();
    @endphp

    @section('subnav_mega_menu')
        <div class="mega-menu">
            <div class="mega-menu-inner">
                <div class="mega-categories">
                    @foreach($megaColumns as $column)
                        <div class="mega-column">
                            <div class="mega-title">{{ $column['title'] }}</div>
                            @foreach($column['links'] as $link)
                                <a href="{{ route('categories.show', $link['id']) }}" class="mega-link">{{ $link['label'] }}</a>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <aside class="mega-rail">
                    @php
                        $newCategory = $mainCategories->first();
                        $offerCategory = $mainCategories->skip(1)->first() ?? $newCategory;
                    @endphp
                    <a href="{{ $newCategory ? route('categories.show', $newCategory['id']) : route('home') }}" class="mega-spotlight dark">
                        <span class="mega-spotlight-kicker">Nuevo</span>
                        <strong>Looks que se venden rapido</strong>
                        <span>Descubre prendas, sets y favoritos del momento.</span>
                    </a>
                    <a href="{{ $offerCategory ? route('categories.show', $offerCategory['id']) : route('home') }}" class="mega-spotlight soft">
                        <span class="mega-spotlight-kicker">Oferta</span>
                        <strong>Descuentos y drops de temporada</strong>
                        <span>Compra tendencias con precios especiales por tiempo limitado.</span>
                    </a>
                </aside>

                <aside class="mega-featured">
                    @foreach($megaFeatured as $product)
                        <a href="{{ route('products.show', $product['id']) }}" class="mega-product">
                            <span class="mega-product-media">
                                <img data-deferred-src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                            </span>
                            <span class="mega-product-copy">
                                <span class="mega-title">Destacado</span>
                                <span class="mega-product-title">{{ $product['name'] }}</span>
                                <span class="mega-product-price">${{ number_format($product['price'], 0, ',', '.') }}</span>
                            </span>
                        </a>
                    @endforeach
                </aside>
            </div>
        </div>
    @endsection

    @section('subnav_links')
        @foreach($mainCategories->take(4) as $navigationCategory)
            <a href="{{ route('categories.show', $navigationCategory['id']) }}" class="nav-pill">{{ $navigationCategory['name'] }}</a>
        @endforeach
    @endsection

    <section class="hero-banner">
        <div class="hero-carousel" data-hero-carousel>
            <div class="hero-carousel-track" data-hero-track>
                @foreach($heroSlides as $slide)
                    @php
                        $showBadge = (!array_key_exists('show_badge', $slide) || !empty($slide['show_badge'])) && filled($slide['badge'] ?? null);
                        $showTitle = (!array_key_exists('show_title', $slide) || !empty($slide['show_title'])) && filled($slide['title'] ?? null);
                        $showSubtitle = (!array_key_exists('show_subtitle', $slide) || !empty($slide['show_subtitle'])) && filled($slide['subtitle'] ?? null);
                        $showCta = (!array_key_exists('show_cta', $slide) || !empty($slide['show_cta'])) && filled($slide['cta'] ?? null);
                        $showOverlay = !array_key_exists('show_overlay', $slide) || !empty($slide['show_overlay']);
                        $hasCopy = $showBadge || $showTitle || $showSubtitle || $showCta;
                        $isImageOnly = ! $hasCopy && empty($slide['show_products']);
                        $overlayOpacity = $showOverlay ? ((int) ($slide['overlay_opacity'] ?? 55) / 100) : 0;
                    @endphp
                    <article class="hero-slide {{ $slide['theme'] }} {{ empty($slide['show_products']) ? 'no-products' : '' }} {{ !$showOverlay ? 'no-overlay' : '' }} {{ $isImageOnly ? 'image-only' : '' }} content-align-{{ $slide['content_align'] ?? 'left' }} image-position-{{ $slide['image_position'] ?? 'center' }}" style="--hero-overlay-opacity: {{ $overlayOpacity }}; --hero-image-fit: {{ $slide['image_fit'] ?? 'cover' }};" data-hero-bg="{{ $slide['theme'] === 'summer' ? '#ff8a65' : '#0f172a' }}">
                        @if(filled($slide['image_url'] ?? null) || filled($slide['mobile_image_url'] ?? null))
                            <picture>
                                @if(filled($slide['mobile_image_url'] ?? null))
                                    <source media="(max-width: 760px)" srcset="{{ $slide['mobile_image_url'] }}">
                                @endif
                                <img class="hero-slide-background" src="{{ $slide['image_url'] ?? $slide['mobile_image_url'] }}" alt="" aria-hidden="true" fetchpriority="high">
                            </picture>
                        @endif
                        @if($hasCopy)
                            <div class="hero-slide-copy">
                                @if($showBadge)<span class="hero-slide-badge">{{ $slide['badge'] }}</span>@endif
                                @if($showTitle)<h1 class="hero-slide-title">{!! nl2br(e($slide['title'])) !!}</h1>@endif
                                @if($showSubtitle)<p class="hero-slide-subtitle">{{ $slide['subtitle'] }}</p>@endif
                                @if($showCta)<a href="{{ $slide['cta_href'] }}" class="hero-slide-cta {{ $slide['cta_variant'] === 'ghost' ? 'ghost' : '' }}">{{ $slide['cta'] }}</a>@endif
                            </div>
                        @endif

                        @if(!empty($slide['show_products']))
                        <div class="hero-slide-products">
                            @foreach($slide['products'] as $product)
                                <article class="card hero-product-card">
                                    <a href="{{ route('products.show', $product['id']) }}" class="card-media hero-product-media">
                                        <span class="sale-chip">{{ $loop->first ? 'HOT' : 'NEW' }}</span>
                                        <span class="wish">♡</span>
                                        <img
                                            class="primary"
                                            src="{{ $product['image'] }}"
                                            alt="{{ $product['name'] }}"
                                            loading="{{ $loop->parent->first && $loop->first ? 'eager' : 'lazy' }}"
                                            decoding="{{ $loop->parent->first && $loop->first ? 'sync' : 'async' }}"
                                            @if($loop->parent->first && $loop->first) fetchpriority="high" @endif
                                        >
                                        @if(!empty($product['secondary_image']))
                                            <img class="secondary" data-deferred-src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                                        @endif
                                    </a>
                                    <div class="card-body">
                                        <div class="eyebrow">{{ $product['owner']['name'] }}</div>
                                        <a href="{{ route('products.show', $product['id']) }}" class="title">{{ $product['name'] }}</a>
                                        <div class="price-row">
                                            <span class="price">${{ number_format($product['price'], 0, ',', '.') }}</span>
                                            <span class="old-price">${{ number_format($product['price'] * 1.18, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="card-actions">
                                            <a class="quick-link" href="{{ route('products.show', $product['id']) }}">Ver</a>
                                            <form method="post" action="{{ route('cart.add') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="add-btn">Agregar</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        @endif
                    </article>
                @endforeach
            </div>

            <button type="button" class="hero-carousel-arrow prev" aria-label="Slide anterior" data-hero-prev>‹</button>
            <button type="button" class="hero-carousel-arrow next" aria-label="Slide siguiente" data-hero-next>›</button>

            <div class="hero-carousel-dots" aria-label="Seleccionar slide">
                @foreach($heroSlides as $slide)
                    <button type="button" class="hero-dot {{ $loop->first ? 'is-active' : '' }}" aria-label="Slide {{ $loop->iteration }}" data-hero-dot="{{ $loop->index }}"></button>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-block">
        <div class="section-head">
            <h2 class="section-title">Categorias visuales</h2>
        </div>
        <div class="category-showcase">
            @foreach($visibleCategoryTiles as $category)
                @php
                    $categoryName = $category['name'];
                @endphp
                <a href="{{ $category ? route('categories.show', ['categoryId' => $category['id'], 'q' => $filters['q'], 'sort' => $filters['sort']]) : route('home') }}" class="category-tile">
                    <img
                        class="category-icon"
                        src="{{ $visualCategoryImages[$categoryName] ?? asset('storage/logo.png') }}"
                        alt="{{ $categoryName }}"
                        loading="lazy" decoding="async"
                    >
                    <div class="category-name">{{ $categoryName }}</div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="section-block">
        <div class="section-head">
            <div>
                <p class="eyebrow">seleccion del dia</p>
                <h2 class="section-title">Super Ofertas</h2>
            </div>
            <div class="mini">Precios especiales por tiempo limitado.</div>
        </div>
        <div class="product-strip">
            @foreach($featured as $product)
                <article class="card">
                    <a href="{{ route('products.show', $product['id']) }}" class="card-media">
                        <span class="sale-chip">TOP</span>
                        <span class="wish">♡</span>
                        <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                        @if(!empty($product['secondary_image']))
                            <img class="secondary" data-deferred-src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                        @endif
                    </a>
                    <div class="card-body">
                        <div class="eyebrow">{{ $product['owner']['name'] }}</div>
                        <a href="{{ route('products.show', $product['id']) }}" class="title">{{ $product['name'] }}</a>
                        <div class="price-row">
                            <span class="price">${{ number_format($product['price'], 0, ',', '.') }}</span>
                            <span class="old-price">${{ number_format($product['price'] * 1.18, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="discover-shell" id="catalogo">
        <div class="section-head" style="justify-content:center;">
            <h2 class="section-title centered-lines">Para ti</h2>
        </div>

        <section class="split-section">
            <div class="editorial-banner dark">
                <div class="promo-kicker">nueva temporada</div>
                <h3>Nueva colección</h3>
                <p>Prendas clave para looks frescos, versátiles y fáciles de combinar.</p>
            </div>
            <div class="product-strip">
                @foreach($newCollection as $product)
                    <article class="card">
                        <a href="{{ route('products.show', $product['id']) }}" class="card-media">
                            <span class="sale-chip">NEW</span>
                            <span class="wish">♡</span>
                            <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                            @if(!empty($product['secondary_image']))
                                <img class="secondary" data-deferred-src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                            @endif
                        </a>
                        <div class="card-body">
                            <div class="eyebrow">Nueva colección</div>
                            <a href="{{ route('products.show', $product['id']) }}" class="title">{{ $product['name'] }}</a>
                            <div class="price-row"><span class="price">${{ number_format($product['price'], 0, ',', '.') }}</span></div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="split-section">
            <div class="editorial-banner red">
                <div class="promo-kicker">hasta agotar existencias</div>
                <h3>Ofertas</h3>
                <p>Descuentos que valen la pena en piezas que se venden rápido.</p>
            </div>
            <div class="product-strip">
                @foreach($flashSale as $product)
                    <article class="card">
                        <a href="{{ route('products.show', $product['id']) }}" class="card-media">
                            <span class="sale-chip">SALE</span>
                            <span class="wish">♡</span>
                            <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                            @if(!empty($product['secondary_image']))
                                <img class="secondary" data-deferred-src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                            @endif
                        </a>
                        <div class="card-body">
                            <div class="eyebrow">Flash sale</div>
                            <a href="{{ route('products.show', $product['id']) }}" class="title">{{ $product['name'] }}</a>
                            <div class="price-row">
                                <span class="price">${{ number_format($product['price'], 0, ',', '.') }}</span>
                                <span class="old-price">${{ number_format($product['price'] * 1.24, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="split-section">
            <div class="editorial-banner gray">
                <div class="promo-kicker">lo mas buscado</div>
                <h3>Tendencias</h3>
                <p>Colores, siluetas y esenciales que están marcando la temporada.</p>
            </div>
            <div class="product-strip">
                @foreach($trending as $product)
                    <article class="card">
                        <a href="{{ route('products.show', $product['id']) }}" class="card-media">
                            <span class="sale-chip">TREND</span>
                            <span class="wish">♡</span>
                            <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                            @if(!empty($product['secondary_image']))
                                <img class="secondary" data-deferred-src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                            @endif
                        </a>
                        <div class="card-body">
                            <div class="eyebrow">Trending</div>
                            <a href="{{ route('products.show', $product['id']) }}" class="title">{{ $product['name'] }}</a>
                            <div class="price-row"><span class="price">${{ number_format($product['price'], 0, ',', '.') }}</span></div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="split-section">
            <div class="editorial-banner beige">
                <div class="promo-kicker">seleccion premium</div>
                <h3>Premium</h3>
                <p>Una curaduría especial para elevar tu armario con piezas destacadas.</p>
            </div>
            <div class="product-strip">
                @foreach($premium as $product)
                    <article class="card">
                        <a href="{{ route('products.show', $product['id']) }}" class="card-media">
                            <span class="sale-chip">PREMIUM</span>
                            <span class="wish">♡</span>
                            <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                            @if(!empty($product['secondary_image']))
                                <img class="secondary" data-deferred-src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy" decoding="async">
                            @endif
                        </a>
                        <div class="card-body">
                            <div class="eyebrow">Premium</div>
                            <a href="{{ route('products.show', $product['id']) }}" class="title">{{ $product['name'] }}</a>
                            <div class="price-row"><span class="price">${{ number_format($product['price'], 0, ',', '.') }}</span></div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <div
            class="wall-card"
            data-home-products
            data-products-url="{{ route('home.products') }}"
            data-initial-meta='@json($meta)'
            data-filters='@json($filters)'
        >
            <div class="wall-head">
                <div>
                    <h2 class="wall-title">Todo el catálogo</h2>
                </div>
            </div>

            <div class="product-wall" data-home-product-grid>
                @forelse($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product, 'chip' => 'HOT', 'showOldPrice' => true, 'withActions' => true, 'showMeta' => true])
                @empty
                    <div class="empty-state" style="grid-column:1 / -1;" data-home-empty>
                        <strong>No hay productos para ese filtro.</strong>
                        <div class="mini">Prueba con otra categoria o limpia la busqueda.</div>
                    </div>
                @endforelse
            </div>
            <div class="mini" data-home-loading hidden style="text-align:center;margin-top:20px;">Cargando más productos...</div>
            <div data-home-sentinel></div>
        </div>

        <section class="newsletter">
            <div>
                <p class="eyebrow" style="color:rgba(255,255,255,.6)">suscribete</p>
                <h2>Recibe ofertas,<br>novedades y lanzamientos.</h2>
                <div class="mini" style="color:rgba(255,255,255,.72)">Sé la primera en enterarte de promociones y nuevas colecciones.</div>
            </div>
            <form class="newsletter-form">
                <input class="field" type="email" placeholder="Tu correo">
                <button type="button" class="button" style="width:auto;">Suscribirme</button>
            </form>
        </section>

        <footer class="site-footer">
            @php
                $footerLinks = $mainCategories->take(4)
                    ->map(fn (array $category) => [$category['name'], $category])
                    ->all();
            @endphp
            <div class="footer-grid">
                <div>
                    <p class="footer-title">Comprar</p>
                    <div class="footer-list">
                        @foreach($footerLinks as [$label, $category])
                            <a href="{{ $category ? route('categories.show', $category['id']) : route('home') }}">{{ $label }}</a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="footer-title">Cuenta</p>
                    <div class="footer-list">
                        <a href="{{ route('cart.show') }}">Carrito</a>
                        <a href="{{ route('checkout.show') }}">Checkout</a>
                        <a href="#">Mi cuenta</a>
                    </div>
                </div>
                <div>
                    <p class="footer-title">Ayuda</p>
                    <div class="footer-list">
                        <a href="#">Envios</a>
                        <a href="#">Pagos</a>
                        <a href="#">Devoluciones</a>
                        <a href="#">Soporte</a>
                        <a href="#" class="footer-cookie-link" data-cc="show-preferencesModal">Cookies</a>
                    </div>
                </div>
                <div class="footer-brand">
                    <img src="{{ asset('storage/logo.png') }}" alt="M57" class="footer-logo">
                    <p class="footer-title">M57</p>
                    <div class="footer-list">
                        <a href="#">Marketplace</a>
                        <a href="#">Nueva coleccion</a>
                        <a href="#">Flash sale</a>
                        <a href="#">Premium</a>
                    </div>
                </div>
            </div>
        </footer>
    </section>
    <script>
        (() => {
            const root = document.querySelector('[data-home-products]');
            if (!root || !('IntersectionObserver' in window)) return;

            const grid = root.querySelector('[data-home-product-grid]');
            const sentinel = root.querySelector('[data-home-sentinel]');
            const loading = root.querySelector('[data-home-loading]');
            const filters = JSON.parse(root.dataset.filters || '{}');
            let meta = JSON.parse(root.dataset.initialMeta || '{}');
            let busy = false;

            const loadNext = async () => {
                const page = Number(meta.page || 1);
                const lastPage = Number(meta.last_page || 1);
                if (busy || page >= lastPage) return;

                busy = true;
                loading.hidden = false;

                try {
                    const params = new URLSearchParams({
                        page: page + 1,
                        q: filters.q || '',
                        sort: filters.sort || 'newest',
                        shuffle_seed: filters.shuffle_seed || '',
                    });
                    const response = await fetch(`${root.dataset.productsUrl}?${params}`, { headers: { Accept: 'application/json' } });
                    const payload = await response.json();
                    meta = payload.meta || meta;
                    grid.insertAdjacentHTML('beforeend', payload.html || '');
                } finally {
                    busy = false;
                    loading.hidden = true;
                }
            };

            new IntersectionObserver((entries) => {
                if (entries.some((entry) => entry.isIntersecting)) loadNext();
            }, { rootMargin: '600px' }).observe(sentinel);
        })();

        (() => {
            const endpoint = @json(route('analytics.track'));
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const send = (eventType, payload = {}) => {
                const body = JSON.stringify({ event_type: eventType, ...payload });

                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        Accept: 'application/json',
                    },
                    body,
                    keepalive: true,
                }).catch(() => {});
            };

            document.addEventListener('click', (event) => {
                const link = event.target.closest('a[href*="/products/"]');
                if (!link) return;

                const card = link.closest('[data-product-id]');
                const match = link.href.match(/\/products\/(\d+)/);
                const productId = Number(card?.dataset.productId || match?.[1] || 0);

                if (productId > 0) {
                    send('product_click', {
                        product_id: productId,
                        owner_user_id: Number(card?.dataset.ownerId || 0) || null,
                        meta: { source: 'home' },
                    });
                }
            });

            document.addEventListener('submit', (event) => {
                const form = event.target.closest('form[action="{{ route('cart.add') }}"]');
                if (!form) return;

                const productId = Number(form.querySelector('[name="product_id"]')?.value || 0);
                if (productId > 0) {
                    send('cart_intent', {
                        product_id: productId,
                        meta: { source: 'home' },
                    });
                }
            }, true);
        })();
    </script>
@endsection
