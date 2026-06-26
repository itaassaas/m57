@extends('storefront.layout', ['title' => 'Carrito M57', 'cartCount' => $cartCount])

@section('content')
    <style>
        .bag-shell {
            display: grid;
            grid-template-columns: minmax(0, 68%) minmax(320px, 32%);
            gap: 28px;
            padding: 28px 0 56px;
        }
        .bag-main {
            min-width: 0;
        }
        .bag-sidebar {
            position: sticky;
            top: 92px;
            align-self: start;
        }
        .bag-stepper {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .bag-step {
            min-height: 72px;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid #ececef;
            background: rgba(255,255,255,.84);
            display: grid;
            gap: 8px;
            align-content: start;
            transition: transform 250ms var(--ease-out), border-color 250ms var(--ease-out), box-shadow 250ms var(--ease-out), background 250ms var(--ease-out);
        }
        .bag-step.is-active {
            background: #111;
            color: #fff;
            border-color: #111;
            box-shadow: 0 18px 36px rgba(17,17,17,.12);
        }
        .bag-step-index {
            font-size: 20px;
            line-height: 1;
        }
        .bag-step-title {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: -.02em;
        }
        .bag-step-copy {
            font-size: 12px;
            color: #71717a;
        }
        .bag-step.is-active .bag-step-copy {
            color: rgba(255,255,255,.68);
        }
        .bag-card,
        .bag-summary,
        .bag-item {
            border-radius: 16px;
            background: #fff;
            border: 1px solid #ececef;
            box-shadow: 0 10px 24px rgba(17,17,17,.04);
        }
        .bag-card,
        .bag-summary {
            padding: 24px;
        }
        .bag-card-head,
        .bag-summary-head,
        .bag-inline,
        .bag-actions,
        .bag-item-meta,
        .bag-totals-row,
        .bag-security {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .bag-card-head,
        .bag-summary-head,
        .bag-totals-row {
            justify-content: space-between;
        }
        .bag-card-head,
        .bag-summary-head {
            margin-bottom: 18px;
        }
        .bag-kicker {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #71717a;
        }
        .bag-title {
            margin: 0;
            font-size: clamp(26px, 3vw, 36px);
            line-height: .98;
            letter-spacing: -.05em;
        }
        .bag-copy,
        .bag-help {
            color: #71717a;
            font-size: 13px;
            line-height: 1.55;
        }
        .bag-list {
            display: grid;
            gap: 14px;
        }
        .bag-item {
            display: grid;
            grid-template-columns: 112px minmax(0, 1fr) auto;
            gap: 18px;
            padding: 18px;
            position: relative;
            overflow: hidden;
            transition: box-shadow 250ms var(--ease-out), border-color 250ms var(--ease-out), transform 250ms var(--ease-out);
        }
        .bag-item.is-loading::after,
        .bag-thumb.is-loading::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.7) 50%, rgba(255,255,255,0) 100%);
            animation: bag-shimmer 1.15s linear infinite;
        }
        @keyframes bag-shimmer {
            from { transform: translateX(-100%); }
            to { transform: translateX(100%); }
        }
        .bag-thumb {
            width: 112px;
            height: 138px;
            border-radius: 16px;
            overflow: hidden;
            background: linear-gradient(180deg, #f7f7f8 0%, #ececef 100%);
            position: relative;
        }
        .bag-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bag-item-title {
            font-size: 16px;
            line-height: 1.45;
            margin-bottom: 10px;
        }
        .bag-item-meta {
            gap: 8px;
            margin-bottom: 12px;
        }
        .bag-pill {
            min-height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            border: 1px solid #ececef;
            background: #fafafa;
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            font-weight: 700;
        }
        .bag-price-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .bag-price {
            font-size: 24px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -.04em;
        }
        .bag-old-price {
            color: #a1a1aa;
            text-decoration: line-through;
            font-size: 14px;
            font-weight: 700;
        }
        .bag-qty {
            width: 82px;
            min-height: 44px;
            border-radius: 12px;
            border: 1px solid #e6e6e9;
            padding: 0 14px;
            background: #fff;
        }
        .bag-link-btn,
        .bag-secondary-btn,
        .bag-primary-btn,
        .bag-mobile-btn {
            min-height: 52px;
            border-radius: 14px;
            border: 1px solid #ececef;
            background: #fff;
            padding: 0 18px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 250ms var(--ease-out), box-shadow 250ms var(--ease-out), border-color 250ms var(--ease-out), background 250ms var(--ease-out), color 250ms var(--ease-out);
        }
        .bag-primary-btn,
        .bag-mobile-btn {
            background: #111;
            color: #fff;
            border-color: #111;
        }
        .bag-link-btn {
            min-height: auto;
            padding: 0;
            border: 0;
            background: transparent;
            color: #52525b;
            font-size: 12px;
            font-weight: 700;
        }
        .bag-item-side {
            min-width: 120px;
            display: grid;
            justify-items: end;
            align-content: space-between;
            gap: 12px;
        }
        .bag-line-total {
            font-size: 22px;
            line-height: 1;
            letter-spacing: -.04em;
            font-weight: 800;
        }
        .bag-summary-list {
            display: grid;
            gap: 12px;
        }
        .bag-totals-row {
            color: #52525b;
            font-size: 14px;
        }
        .bag-totals-row.total {
            padding-top: 14px;
            margin-top: 6px;
            border-top: 1px solid #ececef;
            color: #111;
            font-size: 17px;
            font-weight: 800;
        }
        .bag-cta {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }
        .bag-security {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }
        .bag-security-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #52525b;
            font-size: 13px;
        }
        .bag-security-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f4f4f5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #111;
        }
        .bag-surface {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #fafafa;
            border: 1px solid #ececef;
            display: grid;
            gap: 6px;
        }
        .bag-empty {
            padding: 42px 28px;
            text-align: center;
            border-radius: 20px;
            border: 1px dashed #d4d4d8;
            background: linear-gradient(180deg, #fff 0%, #fafafa 100%);
            display: grid;
            gap: 14px;
            justify-items: center;
        }
        .bag-empty-mark {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #f4f4f5;
            display: grid;
            place-items: center;
            font-size: 26px;
        }
        .bag-mobile-bar {
            display: none;
        }
        .bag-reveal {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity 250ms var(--ease-out), transform 250ms var(--ease-out);
        }
        .bag-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        @media (hover: hover) and (pointer: fine) {
            .bag-step:hover,
            .bag-primary-btn:hover,
            .bag-secondary-btn:hover,
            .bag-mobile-btn:hover,
            .bag-item:hover {
                transform: scale(1.02);
            }
            .bag-primary-btn:hover,
            .bag-mobile-btn:hover {
                box-shadow: 0 18px 36px rgba(17,17,17,.14);
            }
            .bag-item:hover {
                border-color: #ddd;
                box-shadow: 0 16px 28px rgba(17,17,17,.06);
            }
        }
        @media (max-width: 980px) {
            .bag-shell {
                grid-template-columns: 1fr;
                padding-bottom: 110px;
            }
            .bag-sidebar {
                position: static;
            }
        }
        @media (max-width: 760px) {
            .bag-stepper,
            .bag-item {
                grid-template-columns: 1fr;
            }
            .bag-item-side {
                justify-items: start;
                min-width: 0;
            }
            .bag-card,
            .bag-summary {
                padding: 18px;
            }
            .bag-mobile-bar {
                position: fixed;
                left: 12px;
                right: 12px;
                bottom: 12px;
                z-index: 66;
                display: grid;
                grid-template-columns: auto 1fr;
                gap: 12px;
                align-items: center;
                padding: 12px;
                border-radius: 20px;
                border: 1px solid rgba(17,17,17,.08);
                background: rgba(255,255,255,.96);
                box-shadow: 0 18px 34px rgba(17,17,17,.16);
                backdrop-filter: blur(12px);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .bag-step,
            .bag-item,
            .bag-reveal,
            .bag-primary-btn,
            .bag-secondary-btn,
            .bag-link-btn,
            .bag-mobile-btn {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>

    <section class="bag-shell">
        <div class="bag-main">
            <div class="bag-stepper bag-reveal">
                <div class="bag-step is-active">
                    <span class="bag-step-index">🛒</span>
                    <span class="bag-step-title">Carrito</span>
                    <span class="bag-step-copy">{{ $cartCount }} artículos listos para revisar.</span>
                </div>
                <div class="bag-step">
                    <span class="bag-step-index">📦</span>
                    <span class="bag-step-title">Envío</span>
                    <span class="bag-step-copy">Dirección y método.</span>
                </div>
                <div class="bag-step">
                    <span class="bag-step-index">💳</span>
                    <span class="bag-step-title">Pago</span>
                    <span class="bag-step-copy">Confirmación segura.</span>
                </div>
                <div class="bag-step">
                    <span class="bag-step-index">✅</span>
                    <span class="bag-step-title">Confirmación</span>
                    <span class="bag-step-copy">Órdenes separadas en Hub.</span>
                </div>
            </div>

            <section class="bag-card bag-reveal">
                <div class="bag-card-head">
                    <div>
                        <div class="bag-kicker">Tu bolsa</div>
                        <h1 class="bag-title">Revisa antes de pagar</h1>
                    </div>
                    <div class="bag-copy">Todo queda preparado para un checkout rápido y limpio.</div>
                </div>

                <div class="bag-list" data-bag-list>
                    @forelse($cartItems as $item)
                        @php($itemKey = $item['product_id'].':'.($item['variation_id'] ?: 0))
                        <article class="bag-item" data-bag-item data-item-key="{{ $itemKey }}" data-price="{{ $item['price'] }}" data-quantity="{{ $item['quantity'] }}">
                            <div class="bag-thumb is-loading">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy">
                            </div>
                            <div>
                                <div class="bag-item-title">{{ $item['name'] }}</div>
                                <div class="bag-item-meta">
                                    @if(!empty($item['attributes']['Color']))
                                        <span class="bag-pill">Color {{ $item['attributes']['Color'] }}</span>
                                    @endif
                                    @if(!empty($item['attributes']['Talla']))
                                        <span class="bag-pill">Talla {{ $item['attributes']['Talla'] }}</span>
                                    @elseif($item['variation_name'])
                                        <span class="bag-pill">{{ $item['variation_name'] }}</span>
                                    @endif
                                    <span class="bag-pill">{{ $item['owner_name'] }}</span>
                                </div>
                                <div class="bag-price-row">
                                    <span class="bag-price">${{ number_format($item['price'], 0, ',', '.') }}</span>
                                    <span class="bag-old-price">${{ number_format($item['price'] * 1.18, 0, ',', '.') }}</span>
                                </div>
                                <div class="bag-actions">
                                    <input class="bag-qty" type="number" min="1" max="20" value="{{ $item['quantity'] }}" data-qty-input>
                                    <button type="button" class="bag-link-btn" data-bag-save>Actualizar</button>
                                    <button type="button" class="bag-link-btn" data-bag-remove>Quitar</button>
                                    <button type="button" class="bag-link-btn" data-bag-favorite>Guardar</button>
                                </div>
                            </div>
                            <div class="bag-item-side">
                                <div class="bag-line-total" data-line-total>${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</div>
                                <div class="bag-help">x{{ $item['quantity'] }} unidad{{ $item['quantity'] > 1 ? 'es' : '' }}</div>
                            </div>
                        </article>
                    @empty
                        <div class="bag-empty">
                            <div class="bag-empty-mark">⌂</div>
                            <strong>Tu carrito está vacío.</strong>
                            <div class="bag-copy">Explora el catálogo y vuelve cuando tengas tus favoritos listos.</div>
                            <a class="bag-primary-btn" href="{{ route('home') }}">Seguir comprando</a>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="bag-sidebar bag-reveal">
            <div class="bag-summary">
                <div class="bag-summary-head">
                    <div>
                        <div class="bag-kicker">Resumen del pedido</div>
                        <h2 class="bag-title" style="font-size:28px;">Total</h2>
                    </div>
                    <span class="bag-pill" data-bag-count>{{ $cartCount }} artículos</span>
                </div>

                <div class="bag-summary-list">
                    <div class="bag-totals-row"><span>Subtotal</span><strong data-bag-subtotal>${{ number_format($cartTotal, 0, ',', '.') }}</strong></div>
                    <div class="bag-totals-row"><span>Descuento estimado</span><strong data-bag-discount>$0</strong></div>
                    <div class="bag-totals-row"><span>Envío</span><strong>Calculado en checkout</strong></div>
                    <div class="bag-totals-row total"><span>Total productos</span><strong data-bag-total>${{ number_format($cartTotal, 0, ',', '.') }}</strong></div>
                </div>

                <div class="bag-surface">
                    <strong>Compra segura</strong>
                    <span class="bag-help">Hub separa automáticamente las órdenes por tienda dueña para que el checkout siga limpio.</span>
                </div>

                <div class="bag-security">
                    <div class="bag-security-item"><span class="bag-security-icon">✓</span>Pago protegido</div>
                    <div class="bag-security-item"><span class="bag-security-icon">⚡</span>Checkout rápido</div>
                    <div class="bag-security-item"><span class="bag-security-icon">↺</span>Cambios fáciles</div>
                    <div class="bag-security-item"><span class="bag-security-icon">☏</span>Soporte</div>
                </div>

                @if($cartCount)
                    <div class="bag-cta">
                        <a class="bag-primary-btn" href="{{ route('checkout.show') }}">Ir al checkout</a>
                        <a class="bag-secondary-btn" href="{{ route('home') }}">Seguir comprando</a>
                    </div>
                @endif
            </div>
        </aside>
    </section>

    @if($cartCount)
        <div class="bag-mobile-bar">
            <div>
                <div class="bag-help">Total</div>
                <strong data-bag-mobile-total>${{ number_format($cartTotal, 0, ',', '.') }}</strong>
            </div>
            <a class="bag-mobile-btn" href="{{ route('checkout.show') }}">Checkout</a>
        </div>
    @endif

    <script>
        (() => {
            const list = document.querySelector('[data-bag-list]');
            if (!list) return;

            const csrf = @json(csrf_token());
            const updateUrl = @json(route('cart.update', '__KEY__'));
            const removeUrl = @json(route('cart.remove', '__KEY__'));
            const subtotalEl = document.querySelector('[data-bag-subtotal]');
            const totalEl = document.querySelector('[data-bag-total]');
            const countEl = document.querySelector('[data-bag-count]');
            const mobileTotalEl = document.querySelector('[data-bag-mobile-total]');

            const money = (value) => `$${new Intl.NumberFormat('es-CO').format(Math.round(value || 0))}`;

            const syncTotals = () => {
                const items = Array.from(document.querySelectorAll('[data-bag-item]'));
                const visibleItems = items.filter((item) => !item.hidden);
                const subtotal = visibleItems.reduce((sum, item) => {
                    return sum + (Number(item.dataset.price || 0) * Number(item.dataset.quantity || 0));
                }, 0);

                if (subtotalEl) subtotalEl.textContent = money(subtotal);
                if (totalEl) totalEl.textContent = money(subtotal);
                if (countEl) countEl.textContent = `${visibleItems.length} artículos`;
                if (mobileTotalEl) mobileTotalEl.textContent = money(subtotal);
            };

            const request = async (url, method, body) => {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body,
                });

                if (!response.ok) throw new Error('Request failed');
                return response.json();
            };

            list.addEventListener('click', async (event) => {
                const item = event.target.closest('[data-bag-item]');
                if (!item) return;

                if (event.target.matches('[data-bag-favorite]')) {
                    event.target.textContent = event.target.textContent === 'Guardar' ? 'Guardado' : 'Guardar';
                    return;
                }

                if (event.target.matches('[data-bag-remove]')) {
                    item.classList.add('is-loading');
                    try {
                        await request(removeUrl.replace('__KEY__', item.dataset.itemKey), 'POST', new URLSearchParams({ _method: 'DELETE' }));
                        item.hidden = true;
                        item.remove();
                        syncTotals();
                    } finally {
                        item.classList.remove('is-loading');
                    }
                }

                if (event.target.matches('[data-bag-save]')) {
                    const qty = item.querySelector('[data-qty-input]');
                    item.classList.add('is-loading');
                    try {
                        await request(
                            updateUrl.replace('__KEY__', item.dataset.itemKey),
                            'POST',
                            new URLSearchParams({ _method: 'PATCH', quantity: qty.value })
                        );
                        item.dataset.quantity = qty.value;
                        item.querySelector('[data-line-total]').textContent = money(Number(item.dataset.price || 0) * Number(qty.value || 0));
                        syncTotals();
                    } finally {
                        item.classList.remove('is-loading');
                    }
                }
            });

            document.querySelectorAll('.bag-thumb img').forEach((img) => {
                if (img.complete) {
                    img.parentElement.classList.remove('is-loading');
                } else {
                    img.addEventListener('load', () => img.parentElement.classList.remove('is-loading'), { once: true });
                }
            });

            const observer = 'IntersectionObserver' in window
                ? new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1 })
                : null;

            document.querySelectorAll('.bag-reveal').forEach((node) => {
                if (observer) observer.observe(node);
                else node.classList.add('is-visible');
            });

            syncTotals();
        })();
    </script>
@endsection
