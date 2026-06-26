@extends('storefront.layout', [
    'title' => ($category['name'] ?? 'Categoría') . ' | M57',
    'cartCount' => $cartCount,
    'bodyClass' => 'storefront-category',
    'headerVariant' => 'light',
    'hideSubnav' => true,
])

@section('content')
    @php
        $sortOptions = [
            'newest' => 'Más recientes',
            'best_selling' => 'Más vendidos',
            'price_asc' => 'Precio ↑',
            'price_desc' => 'Precio ↓',
        ];
        $description = 'Descubre novedades, básicos y favoritos con entrega rápida y scroll continuo.';
    @endphp

    <style>
        .plp-shell {
            padding: 22px 0 56px;
        }
        .plp-breadcrumb {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            color: #8f8f96;
            font-size: 12px;
            margin-bottom: 18px;
        }
        .plp-breadcrumb a:hover {
            color: #111;
        }
        .plp-hero {
            display: grid;
            gap: 8px;
            margin-bottom: 20px;
        }
        .plp-title {
            margin: 0;
            font-size: clamp(30px, 4vw, 44px);
            letter-spacing: -.05em;
            line-height: .92;
        }
        .plp-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            color: #61616a;
            font-size: 13px;
        }
        .plp-toolbar {
            position: sticky;
            top: 74px;
            z-index: 25;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            padding: 16px 0;
            margin-bottom: 14px;
            background: rgba(245,245,245,.94);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #ececec;
        }
        .plp-toolbar-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .plp-count {
            font-size: 13px;
            color: #61616a;
        }
        .plp-select,
        .plp-search {
            min-height: 42px;
            border-radius: 999px;
            border: 1px solid #e9e9ec;
            background: #fff;
            padding: 0 14px;
        }
        .plp-search {
            min-width: 220px;
        }
        .plp-view-switch,
        .plp-filter-button,
        .plp-chip,
        .plp-clear {
            min-height: 40px;
            border-radius: 999px;
            border: 1px solid #e8e8eb;
            background: #fff;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 800;
        }
        .plp-view-switch.is-active,
        .plp-filter-button:hover,
        .plp-chip:hover,
        .plp-clear:hover {
            border-color: #111;
            color: #111;
        }
        .plp-layout {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 28px;
            align-items: start;
        }
        .plp-sidebar {
            position: sticky;
            top: 148px;
            display: grid;
            gap: 16px;
        }
        .plp-panel,
        .plp-drawer-sheet {
            border-radius: 20px;
            border: 1px solid #ececef;
            background: #fff;
            box-shadow: 0 10px 24px rgba(17,17,17,.04);
        }
        .plp-filter-section + .plp-filter-section {
            border-top: 1px solid #f0f0f2;
        }
        .plp-filter-toggle {
            width: 100%;
            min-height: 56px;
            border: 0;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px;
            font-size: 13px;
            font-weight: 800;
        }
        .plp-filter-body {
            padding: 0 18px 18px;
            display: grid;
            gap: 10px;
        }
        .plp-filter-body[hidden] {
            display: none;
        }
        .plp-filter-links,
        .plp-filter-checks,
        .plp-filter-swatches {
            display: grid;
            gap: 10px;
        }
        .plp-filter-link,
        .plp-check {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #42424a;
            font-size: 13px;
        }
        .plp-filter-link.is-current {
            color: #111;
            font-weight: 800;
        }
        .plp-filter-swatches {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .plp-swatch {
            min-height: 40px;
            border-radius: 999px;
            border: 1px solid #ececef;
            background: #fff;
            padding: 0 10px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
        }
        .plp-swatch-dot {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            border: 1px solid rgba(0,0,0,.08);
            background: var(--swatch-color, #ddd);
        }
        .plp-range {
            display: grid;
            gap: 10px;
        }
        .plp-range-values {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            color: #61616a;
            font-size: 12px;
        }
        .plp-range input[type="range"] {
            width: 100%;
        }
        .plp-main {
            min-width: 0;
        }
        .plp-active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }
        .plp-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }
        .plp-grid.view-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .plp-grid.view-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .plp-grid .card {
            animation: plp-card-in 150ms var(--ease-out) both;
            transition: transform 150ms var(--ease-out), box-shadow 150ms var(--ease-out);
        }
        .plp-grid .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 30px rgba(17,17,17,.08);
        }
        @keyframes plp-card-in {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .plp-skeleton {
            border-radius: 18px;
            border: 1px solid #ececef;
            background: linear-gradient(180deg, #fff 0%, #fafafa 100%);
            overflow: hidden;
        }
        .plp-skeleton-media,
        .plp-skeleton-line {
            background: linear-gradient(90deg, #f3f3f4 0%, #ececef 50%, #f3f3f4 100%);
            background-size: 200% 100%;
            animation: plp-skeleton 1.1s linear infinite;
        }
        .plp-skeleton-media {
            aspect-ratio: 11 / 16;
        }
        .plp-skeleton-copy {
            padding: 12px;
            display: grid;
            gap: 8px;
        }
        .plp-skeleton-line {
            height: 12px;
            border-radius: 999px;
        }
        .plp-skeleton-line.short {
            width: 42%;
        }
        .plp-skeleton-line.mid {
            width: 68%;
        }
        @keyframes plp-skeleton {
            from { background-position: 200% 0; }
            to { background-position: -200% 0; }
        }
        .plp-empty {
            border-radius: 24px;
            border: 1px dashed #e5e5e8;
            background: #fff;
            padding: 54px 24px;
            display: grid;
            justify-items: center;
            gap: 14px;
            text-align: center;
        }
        .plp-empty-illustration {
            width: 88px;
            height: 88px;
            border-radius: 999px;
            background: linear-gradient(135deg, #f3f3f4 0%, #ececef 100%);
            display: grid;
            place-items: center;
            font-size: 28px;
        }
        .plp-sentinel {
            height: 2px;
        }
        .plp-drawer,
        .plp-overlay {
            display: none;
        }
        @media (max-width: 1180px) {
            .plp-layout {
                grid-template-columns: 280px minmax(0, 1fr);
                gap: 20px;
            }
            .plp-grid,
            .plp-grid.view-4 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (max-width: 920px) {
            .plp-layout {
                grid-template-columns: 1fr;
            }
            .plp-sidebar {
                display: none;
            }
            .plp-drawer,
            .plp-overlay {
                display: block;
            }
            .plp-overlay {
                position: fixed;
                inset: 0;
                background: rgba(17,17,17,.38);
                opacity: 0;
                pointer-events: none;
                transition: opacity 150ms var(--ease-out);
                z-index: 140;
            }
            .plp-drawer-sheet {
                position: fixed;
                inset: auto 0 0 auto;
                width: min(360px, 88vw);
                height: calc(100vh - 74px);
                right: 0;
                bottom: 0;
                border-radius: 22px 0 0 0;
                transform: translateX(100%);
                transition: transform 150ms var(--ease-out);
                z-index: 141;
                overflow: auto;
            }
            .plp-drawer.is-open .plp-overlay {
                opacity: 1;
                pointer-events: auto;
            }
            .plp-drawer.is-open .plp-drawer-sheet {
                transform: translateX(0);
            }
        }
        @media (max-width: 720px) {
            .plp-shell {
                padding-top: 16px;
            }
            .plp-toolbar {
                top: 74px;
            }
            .plp-search {
                min-width: 0;
                width: 100%;
            }
            .plp-grid,
            .plp-grid.view-4,
            .plp-grid.view-3,
            .plp-grid.view-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }
        }
    </style>

    <div
        class="plp-shell"
        data-plp
        data-category-id="{{ $category['id'] }}"
        data-initial-products='@json($products)'
        data-initial-meta='@json($meta)'
        data-initial-filters='@json($filters)'
        data-products-url="{{ route('categories.products', $category['id']) }}"
        data-product-url-template="{{ route('products.show', '__ID__') }}"
        data-cart-url="{{ route('cart.add') }}"
    >
        <nav class="plp-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Inicio</a>
            <span>›</span>
            <span>{{ $category['name'] }}</span>
        </nav>

        <section class="plp-hero">
            <h1 class="plp-title">{{ $category['name'] }}</h1>
            <div class="plp-meta">
                <strong data-plp-total>{{ number_format((int) ($meta['total'] ?? 0), 0, ',', '.') }}</strong>
                <span>productos</span>
            </div>
            <p class="mini">{{ $description }}</p>
        </section>

        <section class="plp-toolbar">
            <div class="plp-toolbar-group">
                <div class="plp-count" data-plp-count>Mostrando {{ count($products) }} de {{ number_format((int) ($meta['total'] ?? 0), 0, ',', '.') }}</div>
                <input class="plp-search" type="search" value="{{ $filters['q'] }}" placeholder="Buscar en {{ strtolower($category['name']) }}..." data-plp-search>
            </div>
            <div class="plp-toolbar-group">
                <button type="button" class="plp-filter-button" data-plp-drawer-open>Filtros</button>
                <select class="plp-select" data-plp-sort>
                    <option value="newest" @selected($filters['sort'] === 'newest')>Más recientes</option>
                    <option value="best_selling" @selected($filters['sort'] === 'best_selling')>Más vendidos</option>
                    <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Precio ↑</option>
                    <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Precio ↓</option>
                </select>
                <button type="button" class="plp-view-switch" data-view="2">2 col</button>
                <button type="button" class="plp-view-switch" data-view="3">3 col</button>
                <button type="button" class="plp-view-switch is-active" data-view="4">4 col</button>
            </div>
        </section>

        <div class="plp-layout">
            <aside class="plp-sidebar">
                <div class="plp-panel">
                    <section class="plp-filter-section">
                        <button type="button" class="plp-filter-toggle">Categoría <span>▾</span></button>
                        <div class="plp-filter-body plp-filter-links">
                            @foreach($categories as $linkedCategory)
                                <a href="{{ route('categories.show', $linkedCategory['id']) }}" class="plp-filter-link {{ (int) $linkedCategory['id'] === (int) $category['id'] ? 'is-current' : '' }}">{{ $linkedCategory['name'] }}</a>
                            @endforeach
                        </div>
                    </section>
                    @if(!empty($filterFacets['sizes']))
                        <section class="plp-filter-section">
                            <button type="button" class="plp-filter-toggle">Talla <span>▾</span></button>
                            <div class="plp-filter-body plp-filter-checks">
                                @foreach($filterFacets['sizes'] as $size)
                                    <label class="plp-check"><input type="checkbox" value="{{ $size }}" data-filter-size> {{ $size }}</label>
                                @endforeach
                            </div>
                        </section>
                    @endif
                    @if(!empty($filterFacets['colors']))
                        <section class="plp-filter-section">
                            <button type="button" class="plp-filter-toggle">Color <span>▾</span></button>
                            <div class="plp-filter-body plp-filter-swatches">
                                @foreach($filterFacets['colors'] as $color)
                                    <label class="plp-swatch"><input type="checkbox" value="{{ $color }}" data-filter-color> <span class="plp-swatch-dot" style="--swatch-color: {{ match(strtolower($color)) { 'negro' => '#111', 'blanco' => '#fff', 'rojo' => '#d92c2c', 'azul' => '#3267d6', 'verde' => '#2f8f57', 'rosa' => '#f49dbb', default => '#d6d6db' } }}"></span> {{ $color }}</label>
                                @endforeach
                            </div>
                        </section>
                    @endif
                    <section class="plp-filter-section">
                        <button type="button" class="plp-filter-toggle">Precio <span>▾</span></button>
                        <div class="plp-filter-body plp-range">
                            <input type="range" min="{{ max(0, $filterFacets['price_min']) }}" max="{{ max($filterFacets['price_max'], $filterFacets['price_min'] + 1000) }}" value="{{ max(0, $filterFacets['price_min']) }}" data-filter-price-min>
                            <input type="range" min="{{ max(0, $filterFacets['price_min']) }}" max="{{ max($filterFacets['price_max'], $filterFacets['price_min'] + 1000) }}" value="{{ max($filterFacets['price_max'], $filterFacets['price_min'] + 1000) }}" data-filter-price-max>
                            <div class="plp-range-values">
                                <span data-price-min-label>${{ number_format(max(0, $filterFacets['price_min']), 0, ',', '.') }}</span>
                                <span data-price-max-label>${{ number_format(max($filterFacets['price_max'], $filterFacets['price_min'] + 1000), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </section>
                    @if(!empty($filterFacets['brands']))
                        <section class="plp-filter-section">
                            <button type="button" class="plp-filter-toggle">Marca <span>▾</span></button>
                            <div class="plp-filter-body plp-filter-checks">
                                @foreach($filterFacets['brands'] as $brand)
                                    <label class="plp-check"><input type="checkbox" value="{{ $brand }}" data-filter-brand> {{ $brand }}</label>
                                @endforeach
                            </div>
                        </section>
                    @endif
                    <section class="plp-filter-section">
                        <button type="button" class="plp-filter-toggle">Disponibilidad <span>▾</span></button>
                        <div class="plp-filter-body plp-filter-checks">
                            <label class="plp-check"><input type="checkbox" data-filter-available> Disponible</label>
                            <label class="plp-check"><input type="checkbox" data-filter-sale> Oferta</label>
                        </div>
                    </section>
                </div>
            </aside>

            <section class="plp-main">
                <div class="plp-active-filters" data-active-filters hidden></div>
                <div class="plp-grid view-4" data-plp-grid>
                    @foreach($products as $product)
                        @include('storefront.partials.product-card', ['product' => $product, 'chip' => !empty($product['is_featured']) ? 'HOT' : 'NEW', 'showOldPrice' => true, 'withActions' => true])
                    @endforeach
                </div>
                <div class="plp-empty" data-plp-empty hidden>
                    <div class="plp-empty-illustration">⌕</div>
                    <strong>No encontramos productos.</strong>
                    <div class="mini">Intenta cambiar tus filtros.</div>
                </div>
                <div class="plp-sentinel" data-plp-sentinel></div>
            </section>
        </div>

        <div class="plp-drawer" data-plp-drawer>
            <button type="button" class="plp-overlay" data-plp-drawer-close aria-label="Cerrar filtros"></button>
            <aside class="plp-drawer-sheet" data-plp-drawer-sheet></aside>
        </div>
    </div>

    <script>
        (() => {
            const root = document.querySelector('[data-plp]');
            if (!root) return;

            const grid = root.querySelector('[data-plp-grid]');
            const empty = root.querySelector('[data-plp-empty]');
            const sentinel = root.querySelector('[data-plp-sentinel]');
            const totalEl = root.querySelector('[data-plp-total]');
            const countEl = root.querySelector('[data-plp-count]');
            const searchInput = root.querySelector('[data-plp-search]');
            const sortSelect = root.querySelector('[data-plp-sort]');
            const activeFilters = root.querySelector('[data-active-filters]');
            const activeFiltersMobile = root.querySelector('[data-active-filters-mobile]');
            const drawer = root.querySelector('[data-plp-drawer]');
            const drawerSheet = root.querySelector('[data-plp-drawer-sheet]');
            const desktopPanel = root.querySelector('.plp-sidebar .plp-panel');
            const priceMin = root.querySelector('[data-filter-price-min]');
            const priceMax = root.querySelector('[data-filter-price-max]');
            const priceMinLabel = root.querySelector('[data-price-min-label]');
            const priceMaxLabel = root.querySelector('[data-price-max-label]');

            const productUrlTemplate = root.dataset.productUrlTemplate;
            const productsUrl = root.dataset.productsUrl;
            const cartUrl = root.dataset.cartUrl;
            const initialProducts = JSON.parse(root.dataset.initialProducts || '[]');
            const initialMeta = JSON.parse(root.dataset.initialMeta || '{}');
            const initialFilters = JSON.parse(root.dataset.initialFilters || '{}');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            let products = [...initialProducts];
            let meta = { ...initialMeta };
            let loading = false;
            let requestTimer = null;
            let view = 4;
            let ui = {
                q: initialFilters.q || '',
                sort: initialFilters.sort || 'newest',
                sizes: [],
                colors: [],
                brands: [],
                available: false,
                sale: false,
            };

            const money = (value) => `$${new Intl.NumberFormat('es-CO').format(Math.round(Number(value || 0)))}`;
            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            if (drawerSheet && desktopPanel) {
                drawerSheet.innerHTML = `
                    <div class="plp-panel">
                        <section class="plp-filter-section">
                            <button type="button" class="plp-filter-toggle" data-plp-drawer-close>Cerrar filtros <span>✕</span></button>
                        </section>
                        ${desktopPanel.innerHTML}
                    </div>
                `;
            }

            const selectedPriceMin = () => Math.min(Number(priceMin?.value || 0), Number(priceMax?.value || 0));
            const selectedPriceMax = () => Math.max(Number(priceMin?.value || 0), Number(priceMax?.value || 0));
            const syncMirrors = (selector, value, checked) => {
                root.querySelectorAll(selector).forEach((input) => {
                    if (value === null || input.value === value) {
                        input.checked = checked;
                    }
                });
            };

            const renderCard = (product) => {
                const productUrl = productUrlTemplate.replace('__ID__', product.id);
                const hasSecondary = product.secondary_image ? `<img class="secondary" src="${escapeHtml(product.secondary_image)}" alt="${escapeHtml(product.name)}" loading="lazy">` : '';
                return `
                    <article class="card" data-colors="${escapeHtml((product.colors || []).join('|'))}" data-sizes="${escapeHtml((product.sizes || []).join('|'))}" data-brand="${escapeHtml(product.owner?.name || 'Hub')}" data-price="${product.price}" data-sale="${product.is_featured ? 1 : 0}">
                        <a href="${productUrl}" class="card-media">
                            <span class="sale-chip">${product.is_featured ? 'HOT' : 'NEW'}</span>
                            <span class="wish">♡</span>
                            <img class="primary" src="${escapeHtml(product.image)}" alt="${escapeHtml(product.name)}" loading="lazy">
                            ${hasSecondary}
                        </a>
                        <div class="card-body">
                            <div class="eyebrow">${escapeHtml(product.owner?.name || 'Hub')}</div>
                            <a href="${productUrl}" class="title">${escapeHtml(product.name)}</a>
                            <div class="price-row">
                                <span class="price">${money(product.price)}</span>
                                <span class="old-price">${money(product.old_price || (product.price * 1.18))}</span>
                            </div>
                            <div class="card-actions">
                                <a class="quick-link" href="${productUrl}">Ver</a>
                                <form method="post" action="${cartUrl}">
                                    <input type="hidden" name="_token" value="${csrf}">
                                    <input type="hidden" name="product_id" value="${product.id}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="add-btn">Agregar</button>
                                </form>
                            </div>
                        </div>
                    </article>
                `;
            };

            const filterProduct = (product) => {
                const name = String(product.name || '').toLowerCase();
                const q = String(ui.q || '').trim().toLowerCase();
                if (q && !name.includes(q) && !String(product.sku || '').toLowerCase().includes(q)) return false;

                if (ui.sizes.length && !ui.sizes.some((size) => (product.sizes || []).includes(size))) return false;
                if (ui.colors.length && !ui.colors.some((color) => (product.colors || []).includes(color))) return false;
                if (ui.brands.length && !ui.brands.includes(product.owner?.name || 'Hub')) return false;
                if (ui.available && Number(product.stock || 0) < 1) return false;
                if (ui.sale && !product.is_featured) return false;

                const price = Number(product.price || 0);
                if (price < selectedPriceMin() || price > selectedPriceMax()) return false;

                return true;
            };

            const visibleProducts = () => products.filter(filterProduct);

            const renderChips = () => {
                const chips = [];
                ui.sizes.forEach((value) => chips.push({ label: value, type: 'size', value }));
                ui.colors.forEach((value) => chips.push({ label: value, type: 'color', value }));
                ui.brands.forEach((value) => chips.push({ label: value, type: 'brand', value }));
                if (ui.available) chips.push({ label: 'Disponible', type: 'available', value: '1' });
                if (ui.sale) chips.push({ label: 'Oferta', type: 'sale', value: '1' });
                if (selectedPriceMin() > Number(priceMin.min) || selectedPriceMax() < Number(priceMax.max)) {
                    chips.push({ label: `${money(selectedPriceMin())} - ${money(selectedPriceMax())}`, type: 'price', value: 'range' });
                }

                const html = chips.length
                    ? `${chips.map((chip) => `<button type="button" class="plp-chip" data-chip-type="${chip.type}" data-chip-value="${escapeHtml(chip.value)}">${escapeHtml(chip.label)} ✕</button>`).join('')}<button type="button" class="plp-clear" data-clear-filters>Limpiar filtros</button>`
                    : '';

                [activeFilters, activeFiltersMobile].forEach((target) => {
                    if (!target) return;
                    target.hidden = !chips.length;
                    target.innerHTML = html;
                });
            };

            const renderGrid = () => {
                const items = visibleProducts();
                grid.innerHTML = items.map(renderCard).join('');
                empty.hidden = items.length > 0;
                countEl.textContent = `Mostrando ${items.length} de ${new Intl.NumberFormat('es-CO').format(Number(meta.total || items.length))}`;
                totalEl.textContent = new Intl.NumberFormat('es-CO').format(Number(meta.total || items.length));
                renderChips();
            };

            const renderSkeletons = () => {
                const skeleton = Array.from({ length: 8 }).map(() => `
                    <div class="plp-skeleton">
                        <div class="plp-skeleton-media"></div>
                        <div class="plp-skeleton-copy">
                            <div class="plp-skeleton-line short"></div>
                            <div class="plp-skeleton-line"></div>
                            <div class="plp-skeleton-line mid"></div>
                        </div>
                    </div>
                `).join('');
                grid.insertAdjacentHTML('beforeend', skeleton);
            };

            const fetchPage = async (page, replace = false) => {
                if (loading) return;
                loading = true;
                renderSkeletons();

                try {
                    const params = new URLSearchParams({
                        page,
                        sort: ui.sort,
                        q: ui.q,
                    });
                    const response = await fetch(`${productsUrl}?${params.toString()}`, { headers: { Accept: 'application/json' } });
                    const payload = await response.json();
                    const nextProducts = Array.isArray(payload.data) ? payload.data : [];
                    meta = payload.meta || meta;
                    products = replace ? nextProducts : [...products, ...nextProducts];
                    grid.querySelectorAll('.plp-skeleton').forEach((node) => node.remove());
                    renderGrid();
                } finally {
                    loading = false;
                }
            };

            const loadNext = () => {
                const page = Number(meta.page || 1);
                const lastPage = Number(meta.last_page || 1);
                if (loading || page >= lastPage) return;
                fetchPage(page + 1);
            };

            const scheduleRefresh = () => {
                window.clearTimeout(requestTimer);
                requestTimer = window.setTimeout(() => {
                    window.scrollTo(0, 0);
                    fetchPage(1, true);
                }, 260);
            };

            root.querySelectorAll('[data-view]').forEach((button) => {
                button.addEventListener('click', () => {
                    view = Number(button.dataset.view || 4);
                    root.querySelectorAll('[data-view]').forEach((item) => item.classList.toggle('is-active', item === button));
                    grid.classList.remove('view-2', 'view-3', 'view-4');
                    grid.classList.add(`view-${view}`);
                });
            });

            sortSelect?.addEventListener('change', () => {
                ui.sort = sortSelect.value;
                scheduleRefresh();
            });

            searchInput?.addEventListener('input', () => {
                ui.q = searchInput.value.trim();
                window.clearTimeout(requestTimer);
                requestTimer = window.setTimeout(() => {
                    window.scrollTo(0, 0);
                    fetchPage(1, true);
                }, 420);
            });

            root.querySelectorAll('[data-filter-size]').forEach((input) => {
                input.addEventListener('change', () => {
                    syncMirrors('[data-filter-size]', input.value, input.checked);
                    ui.sizes = Array.from(root.querySelectorAll('[data-filter-size]:checked')).map((item) => item.value);
                    renderGrid();
                });
            });

            root.querySelectorAll('[data-filter-color]').forEach((input) => {
                input.addEventListener('change', () => {
                    syncMirrors('[data-filter-color]', input.value, input.checked);
                    ui.colors = Array.from(root.querySelectorAll('[data-filter-color]:checked')).map((item) => item.value);
                    renderGrid();
                });
            });

            root.querySelectorAll('[data-filter-brand]').forEach((input) => {
                input.addEventListener('change', () => {
                    syncMirrors('[data-filter-brand]', input.value, input.checked);
                    ui.brands = Array.from(root.querySelectorAll('[data-filter-brand]:checked')).map((item) => item.value);
                    renderGrid();
                });
            });

            root.querySelectorAll('[data-filter-available]').forEach((input) => {
                input.addEventListener('change', (event) => {
                    syncMirrors('[data-filter-available]', null, event.target.checked);
                    ui.available = event.target.checked;
                    renderGrid();
                });
            });

            root.querySelectorAll('[data-filter-sale]').forEach((input) => {
                input.addEventListener('change', (event) => {
                    syncMirrors('[data-filter-sale]', null, event.target.checked);
                    ui.sale = event.target.checked;
                    renderGrid();
                });
            });

            root.querySelectorAll('.plp-filter-toggle').forEach((button) => {
                if (button.hasAttribute('data-plp-drawer-close')) return;
                button.addEventListener('click', () => {
                    const body = button.nextElementSibling;
                    if (!body || !body.classList.contains('plp-filter-body')) return;
                    body.hidden = !body.hidden;
                });
            });

            [priceMin, priceMax].forEach((input) => {
                input?.addEventListener('input', () => {
                    if (Number(priceMin.value) > Number(priceMax.value)) {
                        if (input === priceMin) priceMax.value = priceMin.value;
                        else priceMin.value = priceMax.value;
                    }
                    priceMinLabel.textContent = money(priceMin.value);
                    priceMaxLabel.textContent = money(priceMax.value);
                    renderGrid();
                });
            });

            [activeFilters, activeFiltersMobile].forEach((target) => {
                target?.addEventListener('click', (event) => {
                    const chip = event.target.closest('[data-chip-type]');
                    if (!chip) {
                        if (event.target.closest('[data-clear-filters]')) {
                            ui.sizes = [];
                            ui.colors = [];
                            ui.brands = [];
                            ui.available = false;
                            ui.sale = false;
                            root.querySelectorAll('input[type="checkbox"]').forEach((input) => input.checked = false);
                            priceMin.value = priceMin.min;
                            priceMax.value = priceMax.max;
                            priceMinLabel.textContent = money(priceMin.value);
                            priceMaxLabel.textContent = money(priceMax.value);
                            renderGrid();
                        }
                        return;
                    }

                    const type = chip.dataset.chipType;
                    const value = chip.dataset.chipValue;
                    if (type === 'size') ui.sizes = ui.sizes.filter((item) => item !== value);
                    if (type === 'color') ui.colors = ui.colors.filter((item) => item !== value);
                    if (type === 'brand') ui.brands = ui.brands.filter((item) => item !== value);
                    if (type === 'available') ui.available = false;
                    if (type === 'sale') ui.sale = false;
                    if (type === 'price') {
                        priceMin.value = priceMin.min;
                        priceMax.value = priceMax.max;
                        priceMinLabel.textContent = money(priceMin.value);
                        priceMaxLabel.textContent = money(priceMax.value);
                    }

                    root.querySelectorAll(`input[value="${CSS.escape(value)}"]`).forEach((input) => input.checked = false);
                    if (type === 'available') root.querySelector('[data-filter-available]').checked = false;
                    if (type === 'sale') root.querySelector('[data-filter-sale]').checked = false;
                    renderGrid();
                });
            });

            root.querySelector('[data-plp-drawer-open]')?.addEventListener('click', () => drawer?.classList.add('is-open'));
            root.querySelectorAll('[data-plp-drawer-close]').forEach((button) => {
                button.addEventListener('click', () => drawer?.classList.remove('is-open'));
            });

            const observer = 'IntersectionObserver' in window ? new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) loadNext();
                });
            }, { rootMargin: '600px 0px' }) : null;

            if (observer && sentinel) observer.observe(sentinel);

            renderGrid();
        })();
    </script>
@endsection
