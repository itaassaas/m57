@extends('storefront.layout', ['title' => 'M57 Catalogo', 'cartCount' => $cartCount])

@section('content')
    @php
        $allProducts = collect($products)->values();
        $featured = $allProducts->take(5);
        $newCollection = $allProducts->slice(5, 5);
        $flashSale = $allProducts->slice(10, 5);
        $trending = $allProducts->slice(15, 5);
        $premium = $allProducts->slice(20, 5);
        $megaFeatured = $allProducts->take(3);
        $heroSlides = [
            [
                'badge' => '✨ NUEVA TEMPORADA',
                'title' => "Descubre\ntu nuevo\nestilo favorito",
                'subtitle' => 'Mas de 2.000 referencias nuevas cada semana.',
                'cta' => 'Comprar ahora',
                'cta_href' => '#catalogo',
                'cta_variant' => 'light',
                'theme' => 'midnight',
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
                'products' => $allProducts->slice(4, 4),
            ],
        ];
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
        $visualCategories = collect($categories)->keyBy('name');
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
        $resolveCategory = function (string $name) use ($visualCategories, $categoryAliases) {
            $normalize = static fn (string $value) => \Illuminate\Support\Str::of($value)->lower()->ascii()->replace('&', ' y ')->squish()->value();

            if ($category = $visualCategories->get($name)) {
                return $category;
            }

            $normalized = $normalize($name);
            $aliased = $categoryAliases[$normalized] ?? null;
            if ($aliased && ($category = $visualCategories->get($aliased))) {
                return $category;
            }

            return collect($visualCategories->all())->first(function ($category) use ($normalized, $normalize) {
                return $normalize((string) ($category['name'] ?? '')) === $normalized;
            });
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
    @endphp

    @section('subnav_mega_menu')
        <div class="mega-menu">
            <div class="mega-menu-inner">
                <div class="mega-categories">
                    @foreach($megaColumns as $column)
                        <div class="mega-column">
                            <div class="mega-title">{{ $column['title'] }}</div>
                            @foreach($column['links'] as [$label, $targetCategory])
                                @php
                                    $linkedCategory = $resolveCategory($targetCategory);
                                @endphp
                                <a href="{{ $linkedCategory ? route('categories.show', $linkedCategory['id']) : '#catalogo' }}" class="mega-link">{{ $label }}</a>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <aside class="mega-rail">
                    @php
                        $newCategory = $resolveCategory('Mujer');
                        $offerCategory = $resolveCategory('Vestidos');
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
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
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

    <section class="hero-banner">
        <div class="hero-carousel" data-hero-carousel>
            <div class="hero-carousel-track" data-hero-track>
                @foreach($heroSlides as $slide)
                    <article class="hero-slide hero-slide-{{ $slide['theme'] }}" data-hero-bg="{{ $slide['theme'] === 'summer' ? '#ff8a65' : '#0f172a' }}">
                        <div class="hero-slide-copy">
                            <span class="hero-slide-badge">{{ $slide['badge'] }}</span>
                            <h1 class="hero-slide-title">{!! nl2br(e($slide['title'])) !!}</h1>
                            <p class="hero-slide-subtitle">{{ $slide['subtitle'] }}</p>
                            <a href="{{ $slide['cta_href'] }}" class="hero-slide-cta {{ $slide['cta_variant'] === 'ghost' ? 'ghost' : '' }}">{{ $slide['cta'] }}</a>
                        </div>

                        <div class="hero-slide-products">
                            @foreach($slide['products'] as $product)
                                <article class="card hero-product-card">
                                    <a href="{{ route('products.show', $product['id']) }}" class="card-media hero-product-media">
                                        <span class="sale-chip">{{ $loop->first ? 'HOT' : 'NEW' }}</span>
                                        <span class="wish">♡</span>
                                        <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
                                        @if(!empty($product['secondary_image']))
                                            <img class="secondary" src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy">
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
            @foreach($visualCategoryNames as $categoryName)
                @php
                    $category = $resolveCategory($categoryName);
                @endphp
                <a href="{{ $category ? route('categories.show', ['categoryId' => $category['id'], 'q' => $filters['q'], 'sort' => $filters['sort']]) : '#catalogo' }}" class="category-tile">
                    <img
                        class="category-icon"
                        src="{{ $visualCategoryImages[$categoryName] ?? 'https://picsum.photos/seed/category-'.($category['id'] ?? \Illuminate\Support\Str::slug($categoryName)).'/200' }}"
                        alt="{{ $categoryName }}"
                        loading="lazy"
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
                        <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
                        @if(!empty($product['secondary_image']))
                            <img class="secondary" src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy">
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
                            <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
                            @if(!empty($product['secondary_image']))
                                <img class="secondary" src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy">
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
                            <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
                            @if(!empty($product['secondary_image']))
                                <img class="secondary" src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy">
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
                            <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
                            @if(!empty($product['secondary_image']))
                                <img class="secondary" src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy">
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
                            <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
                            @if(!empty($product['secondary_image']))
                                <img class="secondary" src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy">
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
                $footerCategories = collect($categories)->keyBy('name');
            @endphp
            <div class="footer-grid">
                <div>
                    <p class="footer-title">Comprar</p>
                    <div class="footer-list">
                        <a href="{{ ($footerCategories->get('Mujer')) ? route('categories.show', $footerCategories->get('Mujer')['id']) : '#catalogo' }}">Mujer</a>
                        <a href="{{ ($footerCategories->get('Hombre')) ? route('categories.show', $footerCategories->get('Hombre')['id']) : '#catalogo' }}">Hombre</a>
                        <a href="{{ ($footerCategories->get('Zapatos')) ? route('categories.show', $footerCategories->get('Zapatos')['id']) : '#catalogo' }}">Calzado</a>
                        <a href="{{ ($footerCategories->get('Bolsas & Maletas')) ? route('categories.show', $footerCategories->get('Bolsas & Maletas')['id']) : '#catalogo' }}">Accesorios</a>
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
    </script>
@endsection
