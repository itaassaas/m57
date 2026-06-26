@extends('storefront.layout', ['title' => $product['name'] ?? 'Producto', 'cartCount' => $cartCount])

@section('content')
    @php
        $productId = (int) ($product['id'] ?? 0);
        $productType = $product['type'] ?? 'simple';
        $rating = number_format(4.7 + (($productId % 3) * 0.1), 1);
        $reviewCount = 138 + ($productId % 240);
        $soldCount = 920 + ($productId % 1800);
        $initialPrice = (float) data_get($productState, 'price', $product['price'] ?? 0);
        $colorMap = collect($product['variations'] ?? [])
            ->groupBy(fn (array $variation) => data_get($variation, 'attributes.Color', 'Unico'))
            ->map(function ($items, $color) {
                $first = collect($items)->first();

                return [
                    'name' => $color,
                    'image' => $first['image'] ?? null,
                    'swatch' => match (mb_strtolower((string) $color)) {
                        'negro' => '#1c1c1e',
                        'blanco' => '#f7f7f7',
                        'beish', 'beige' => '#d4c0a1',
                        'morado' => '#8a63d2',
                        'azul' => '#4467c4',
                        'rojo' => '#cb4545',
                        'verde' => '#6b9f73',
                        default => '#d7d7d9',
                    },
                ];
            })
            ->values()
            ->all();
        $reviews = collect(range(1, 6))->map(function (int $index) use ($product, $productState, $productId) {
            $sizes = collect(data_get($productState, 'sizes', []))->pluck('name')->filter()->values();
            $color = data_get($productState, 'color', 'Beige');
            $likes = 9 + (($productId + $index) % 70);
            $avatar = 'https://i.pravatar.cc/240?img=' . (20 + $index);

            return [
                'name' => ['Laura M.', 'Sofia G.', 'Camila R.', 'Valentina P.', 'Daniela C.', 'Maria J.'][$index - 1],
                'rating' => $index === 5 ? 4 : 5,
                'date' => now()->subDays($index * 6)->format('d M Y'),
                'text' => [
                    'La tela se siente mejor de lo que esperaba y el fit queda super limpio.',
                    'El color es muy bonito, la use con jeans y se ve elegante sin esfuerzo.',
                    'Me gusto mucho para diario. La talla corresponde y llego rapido.',
                    'Se ve premium puesta. La volveria a pedir en otro color.',
                    'Buena caida y no transparenta. Recomendada.',
                    'Muy comoda, fresca y con acabado bonito en las costuras.',
                ][$index - 1],
                'size' => $sizes[$index % max($sizes->count(), 1)] ?? 'M',
                'height' => 160 + ($index * 3),
                'weight' => 52 + ($index * 4),
                'color' => $color,
                'likes' => $likes,
                'with_photo' => $index !== 3,
                'image' => $avatar,
                'timestamp' => now()->subDays($index * 6)->timestamp,
                'store_reply' => $index % 2 === 0 ? 'Gracias por tu compra. Seguimos trayendo nuevas referencias cada semana.' : null,
            ];
        })->all();
        $accordionItems = [
            'Descripcion' => $product['description'] ?: 'Diseno versatil con enfoque en caida, comodidad y uso diario. Ideal para combinar con denim, faldas o sastreria ligera.',
            'Material' => 'Tejido suave con tacto agradable y acabado limpio.',
            'Composicion' => 'Mezcla textil seleccionada por el vendedor en Hub.',
            'Cuidados' => 'Lavar a mano o en ciclo delicado. No usar blanqueador. Secar a la sombra.',
            'Medidas' => 'Las medidas pueden variar ligeramente segun talla y proveedor.',
            'SKU' => $product['sku'] ?? 'N/A',
        ];
    @endphp

    <style>
        .pdp-shell {
            display: grid;
            grid-template-columns: minmax(0, 58%) minmax(320px, 42%);
            gap: 32px;
            padding: 28px 0 48px;
        }
        .pdp-gallery {
            display: grid;
            grid-template-columns: 92px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }
        .pdp-thumbs {
            display: grid;
            gap: 12px;
            position: sticky;
            top: 102px;
        }
        .pdp-thumb {
            width: 92px;
            height: 118px;
            padding: 0;
            border: 1px solid #e8e8ea;
            border-radius: 18px;
            background: #fff;
            overflow: hidden;
            transition: transform 250ms var(--ease-out), border-color 250ms var(--ease-out), box-shadow 250ms var(--ease-out);
        }
        .pdp-thumb.is-active {
            border-color: #111;
            box-shadow: 0 10px 24px rgba(17,17,17,.08);
        }
        .pdp-thumb img,
        .pdp-thumb video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pdp-stage-stack {
            display: grid;
            gap: 18px;
        }
        .pdp-stage {
            position: relative;
            min-height: calc(100svh - 138px);
            border-radius: 28px;
            overflow: hidden;
            background: linear-gradient(180deg, #fafafa 0%, #f2f2f2 100%);
            border: 1px solid rgba(17,17,17,.05);
        }
        .pdp-stage.is-loading::after,
        .pdp-card-media.is-loading::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.7) 50%, rgba(255,255,255,0) 100%);
            animation: pdp-shimmer 1.2s linear infinite;
        }
        @keyframes pdp-shimmer {
            from { transform: translateX(-100%); }
            to { transform: translateX(100%); }
        }
        .pdp-stage-media {
            width: 100%;
            height: 100%;
            min-height: inherit;
            position: relative;
            overflow: hidden;
        }
        .pdp-stage-media img,
        .pdp-stage-media video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(var(--zoom-scale, 1));
            transform-origin: var(--zoom-x, 50%) var(--zoom-y, 50%);
            transition: transform 250ms var(--ease-out), opacity 250ms var(--ease-out);
        }
        .pdp-stage-badges {
            position: absolute;
            top: 18px;
            left: 18px;
            display: flex;
            gap: 8px;
            z-index: 2;
        }
        .pdp-stage-pill,
        .pdp-badge,
        .pdp-filter-btn,
        .pdp-guide-btn,
        .pdp-mini-pill {
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            border: 1px solid rgba(17,17,17,.08);
            background: rgba(255,255,255,.92);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
        }
        .pdp-summary {
            position: sticky;
            top: 92px;
            align-self: start;
            max-height: calc(100svh - 108px);
            overflow: auto;
            padding-right: 4px;
        }
        .pdp-brand {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #71717a;
        }
        .pdp-title {
            margin: 10px 0 14px;
            font-size: clamp(30px, 3vw, 42px);
            line-height: 1.02;
            letter-spacing: -.05em;
        }
        .pdp-meta-row,
        .pdp-rating-row,
        .pdp-price-row,
        .pdp-price-meta,
        .pdp-action-row,
        .pdp-stock-row,
        .pdp-accordion-list,
        .pdp-review-toolbar,
        .pdp-review-head {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .pdp-rating-row {
            color: #52525b;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .pdp-rating-strong {
            color: #111;
            font-weight: 800;
        }
        .pdp-price-row {
            align-items: end;
            gap: 12px;
            margin-bottom: 8px;
        }
        .pdp-price {
            font-size: clamp(34px, 4vw, 44px);
            line-height: .95;
            letter-spacing: -.06em;
            font-weight: 800;
        }
        .pdp-old-price {
            color: #a1a1aa;
            text-decoration: line-through;
            font-size: 18px;
            font-weight: 700;
        }
        .pdp-discount {
            color: #166534;
            background: #effcf2;
            border-color: #ccefd6;
        }
        .pdp-price-meta {
            color: #71717a;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .pdp-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
        }
        .pdp-section {
            padding: 22px 0;
            border-top: 1px solid #ededf0;
        }
        .pdp-section:first-of-type {
            border-top: 0;
            padding-top: 0;
        }
        .pdp-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px;
        }
        .pdp-section-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -.02em;
        }
        .pdp-color-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .pdp-color-btn {
            width: 86px;
            padding: 8px;
            border-radius: 20px;
            border: 1px solid #e8e8ea;
            background: #fff;
            display: grid;
            gap: 8px;
            justify-items: center;
            transition: transform 250ms var(--ease-out), border-color 250ms var(--ease-out), box-shadow 250ms var(--ease-out);
        }
        .pdp-color-btn.is-active {
            border-color: #111;
            box-shadow: 0 12px 22px rgba(17,17,17,.08);
        }
        .pdp-color-image {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 14px;
            overflow: hidden;
            background: linear-gradient(180deg, #f7f7f8 0%, #ededf0 100%);
            position: relative;
        }
        .pdp-color-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pdp-color-dot {
            position: absolute;
            right: 8px;
            bottom: 8px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,.9);
            box-shadow: 0 2px 8px rgba(0,0,0,.14);
        }
        .pdp-color-name {
            font-size: 12px;
            font-weight: 700;
        }
        .pdp-size-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .pdp-size-btn {
            min-width: 92px;
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid #e8e8ea;
            background: #fff;
            display: grid;
            gap: 4px;
            justify-items: start;
            text-align: left;
            transition: transform 250ms var(--ease-out), border-color 250ms var(--ease-out), background 250ms var(--ease-out), box-shadow 250ms var(--ease-out);
        }
        .pdp-size-btn.is-active {
            border-color: #111;
            background: #fafafa;
            box-shadow: 0 12px 22px rgba(17,17,17,.08);
        }
        .pdp-size-btn.is-soldout {
            color: #a1a1aa;
            background: #f7f7f8;
        }
        .pdp-size-name {
            font-size: 14px;
            font-weight: 800;
        }
        .pdp-size-stock {
            font-size: 11px;
            color: #71717a;
        }
        .pdp-stock-row {
            justify-content: space-between;
            margin-top: 14px;
            color: #52525b;
            font-size: 13px;
        }
        .pdp-qty-row {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .pdp-qty {
            width: 92px;
            min-height: 50px;
            border-radius: 16px;
            border: 1px solid #e8e8ea;
            padding: 0 16px;
            background: #fff;
        }
        .pdp-cta-stack {
            position: sticky;
            bottom: 0;
            padding-top: 16px;
            background: linear-gradient(180deg, rgba(245,245,245,0) 0%, rgba(245,245,245,1) 24%);
        }
        .pdp-action-row {
            margin-bottom: 14px;
        }
        .pdp-btn,
        .pdp-icon-btn {
            min-height: 54px;
            border-radius: 18px;
            border: 1px solid #e8e8ea;
            background: #fff;
            padding: 0 18px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 250ms var(--ease-out), box-shadow 250ms var(--ease-out), border-color 250ms var(--ease-out), background 250ms var(--ease-out);
        }
        .pdp-btn.primary {
            min-width: 180px;
            background: #111;
            color: #fff;
            border-color: #111;
        }
        .pdp-btn.secondary {
            min-width: 180px;
        }
        .pdp-icon-btn {
            width: 54px;
            padding: 0;
            border-radius: 16px;
        }
        .pdp-benefits {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .pdp-benefit {
            padding: 16px;
            border-radius: 18px;
            border: 1px solid #ededf0;
            background: rgba(255,255,255,.82);
            display: grid;
            gap: 8px;
        }
        .pdp-benefit-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #f4f4f5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .pdp-benefit strong {
            font-size: 14px;
        }
        .pdp-benefit span {
            color: #71717a;
            font-size: 12px;
            line-height: 1.45;
        }
        .pdp-accordion-list {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }
        .pdp-accordion {
            border-radius: 18px;
            border: 1px solid #ededf0;
            background: #fff;
            overflow: hidden;
        }
        .pdp-accordion summary {
            list-style: none;
            cursor: pointer;
            padding: 18px 20px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .pdp-accordion summary::-webkit-details-marker {
            display: none;
        }
        .pdp-accordion-copy {
            padding: 0 20px 18px;
            color: #62626b;
            line-height: 1.65;
        }
        .pdp-block {
            padding: 34px 0 0;
        }
        .pdp-block-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 20px;
        }
        .pdp-block-title {
            margin: 0;
            font-size: clamp(26px, 3vw, 34px);
            line-height: .98;
            letter-spacing: -.05em;
        }
        .pdp-block-copy {
            margin: 8px 0 0;
            color: #71717a;
            font-size: 14px;
        }
        .pdp-review-toolbar {
            gap: 8px;
            margin-bottom: 16px;
        }
        .pdp-filter-btn.is-active {
            background: #111;
            color: #fff;
            border-color: #111;
        }
        .pdp-review-list {
            display: grid;
            gap: 16px;
        }
        .pdp-review-card {
            display: grid;
            grid-template-columns: 88px minmax(0, 1fr);
            gap: 16px;
            padding: 20px;
            border-radius: 22px;
            border: 1px solid #ededf0;
            background: #fff;
            box-shadow: 0 10px 24px rgba(17,17,17,.03);
        }
        .pdp-review-card.is-hidden {
            display: none;
        }
        .pdp-review-photo {
            width: 88px;
            height: 112px;
            border-radius: 16px;
            overflow: hidden;
            background: linear-gradient(180deg, #f6f6f7 0%, #ededf0 100%);
        }
        .pdp-review-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pdp-review-head strong {
            font-size: 15px;
        }
        .pdp-review-meta {
            color: #71717a;
            font-size: 13px;
        }
        .pdp-review-text {
            margin: 12px 0;
            color: #27272a;
            line-height: 1.65;
        }
        .pdp-review-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .pdp-store-reply {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fafafa;
            border: 1px solid #ededf0;
            color: #52525b;
            font-size: 13px;
            line-height: 1.55;
        }
        .pdp-carousel {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(240px, 280px);
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 8px;
            scroll-snap-type: x proximity;
        }
        .pdp-mini-card {
            position: relative;
            scroll-snap-align: start;
            background: #fff;
            border: 1px solid #ededf0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(17,17,17,.03);
            transition: transform 250ms var(--ease-out), box-shadow 250ms var(--ease-out), border-color 250ms var(--ease-out);
        }
        .pdp-card-media {
            position: relative;
            aspect-ratio: 4 / 5;
            overflow: hidden;
            background: linear-gradient(180deg, #f6f6f7 0%, #ededf0 100%);
        }
        .pdp-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 250ms var(--ease-out), opacity 250ms var(--ease-out);
        }
        .pdp-card-actions {
            position: absolute;
            top: 12px;
            right: 12px;
            display: grid;
            gap: 8px;
            z-index: 2;
        }
        .pdp-card-fab,
        .pdp-card-quick {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid rgba(17,17,17,.08);
            background: rgba(255,255,255,.92);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .pdp-card-quick {
            width: auto;
            padding: 0 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
        }
        .pdp-mini-body {
            padding: 14px;
            display: grid;
            gap: 8px;
        }
        .pdp-mini-title {
            font-size: 14px;
            line-height: 1.45;
        }
        .pdp-mini-price {
            font-size: 22px;
            line-height: 1;
            letter-spacing: -.04em;
            font-weight: 800;
        }
        .pdp-reveal {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity 250ms var(--ease-out), transform 250ms var(--ease-out);
        }
        .pdp-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .pdp-mobile-bar {
            display: none;
        }
        .pdp-dialog {
            width: min(540px, calc(100vw - 24px));
            border: 0;
            padding: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 26px 60px rgba(17,17,17,.2);
        }
        .pdp-dialog::backdrop {
            background: rgba(17,17,17,.32);
        }
        .pdp-dialog-grid {
            display: grid;
            grid-template-columns: minmax(180px, 220px) minmax(0, 1fr);
            background: #fff;
        }
        .pdp-dialog-grid img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pdp-dialog-body {
            padding: 22px;
            display: grid;
            gap: 12px;
            align-content: start;
        }
        .pdp-dialog-close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid rgba(17,17,17,.08);
            background: rgba(255,255,255,.92);
        }
        @media (hover: hover) and (pointer: fine) {
            .pdp-stage:hover .pdp-stage-media img {
                --zoom-scale: 1.14;
            }
            .pdp-thumb:hover,
            .pdp-color-btn:hover,
            .pdp-size-btn:hover,
            .pdp-btn:hover,
            .pdp-icon-btn:hover,
            .pdp-mini-card:hover {
                transform: scale(1.02);
            }
            .pdp-mini-card:hover {
                border-color: #ddd;
                box-shadow: 0 16px 30px rgba(17,17,17,.06);
            }
            .pdp-mini-card:hover .pdp-card-media img {
                transform: scale(1.03);
            }
        }
        @media (max-width: 1040px) {
            .pdp-shell {
                grid-template-columns: 1fr;
            }
            .pdp-summary {
                position: static;
                max-height: none;
                overflow: visible;
            }
            .pdp-gallery {
                grid-template-columns: 1fr;
            }
            .pdp-thumbs {
                position: static;
                grid-auto-flow: column;
                grid-template-columns: repeat(auto-fit, minmax(80px, 80px));
                overflow-x: auto;
            }
            .pdp-thumb {
                width: 80px;
                height: 100px;
            }
            .pdp-stage {
                min-height: 70svh;
            }
        }
        @media (max-width: 760px) {
            .pdp-shell {
                gap: 20px;
                padding-bottom: 104px;
            }
            .pdp-stage {
                min-height: 56svh;
                border-radius: 22px;
            }
            .pdp-title {
                font-size: 30px;
            }
            .pdp-benefits,
            .pdp-dialog-grid {
                grid-template-columns: 1fr;
            }
            .pdp-review-card {
                grid-template-columns: 1fr;
            }
            .pdp-review-photo {
                width: 100%;
                height: 220px;
            }
            .pdp-mobile-bar {
                position: fixed;
                left: 12px;
                right: 12px;
                bottom: 12px;
                z-index: 65;
                display: grid;
                grid-template-columns: auto 1fr 1fr;
                gap: 10px;
                padding: 12px;
                border-radius: 22px;
                background: rgba(255,255,255,.96);
                border: 1px solid rgba(17,17,17,.08);
                box-shadow: 0 18px 36px rgba(17,17,17,.16);
                backdrop-filter: blur(14px);
            }
            .pdp-mobile-price {
                display: grid;
                align-content: center;
                min-width: 84px;
            }
            .pdp-mobile-price strong {
                font-size: 18px;
                line-height: 1;
            }
            .pdp-cta-stack {
                display: none;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .pdp-reveal,
            .pdp-stage-media img,
            .pdp-stage-media video,
            .pdp-thumb,
            .pdp-color-btn,
            .pdp-size-btn,
            .pdp-btn,
            .pdp-icon-btn,
            .pdp-mini-card {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>

    <section class="pdp-shell">
        <div class="pdp-gallery">
            <div class="pdp-thumbs" data-gallery-thumbs></div>

            <div class="pdp-stage-stack">
                <div class="pdp-stage pdp-reveal">
                    <div class="pdp-stage-badges">
                        <span class="pdp-stage-pill">Nuevo</span>
                        @if(($product['stock'] ?? 0) > 20)
                            <span class="pdp-stage-pill">Top ventas</span>
                        @endif
                    </div>
                    <div class="pdp-stage-media is-loading" data-media-shell data-gallery-stage></div>
                </div>
            </div>
        </div>

        <div class="pdp-summary pdp-reveal">
            <div class="pdp-section">
                <div class="pdp-brand">{{ data_get($product, 'owner.name', 'M57') }}</div>
                <h1 class="pdp-title">{{ $product['name'] ?? '' }}</h1>

                <div class="pdp-rating-row">
                    <span class="pdp-rating-strong">★ {{ $rating }}</span>
                    <span>{{ $reviewCount }} reseñas</span>
                    <span>·</span>
                    <span>{{ number_format($soldCount, 0, ',', '.') }} vendidos</span>
                </div>

                <div class="pdp-price-row">
                    <span class="pdp-price" data-product-price>${{ number_format($initialPrice, 0, ',', '.') }}</span>
                    <span class="pdp-old-price" data-product-old-price>${{ number_format((float) data_get($productState, 'old_price', 0), 0, ',', '.') }}</span>
                    <span class="pdp-badge pdp-discount" data-product-discount>-{{ data_get($productState, 'discount_percent', 0) }}%</span>
                </div>

                <div class="pdp-price-meta">
                    <span data-product-installments>
                        @if(data_get($productState, 'installments'))
                            o {{ data_get($productState, 'installments') }} cuotas de ${{ number_format($initialPrice / data_get($productState, 'installments'), 0, ',', '.') }}
                        @endif
                    </span>
                </div>

                <div class="pdp-badges">
                    <span class="pdp-badge">Nuevo</span>
                    <span class="pdp-badge">Top ventas</span>
                    <span class="pdp-badge">Oferta Flash</span>
                    <span class="pdp-badge">Envío gratis</span>
                </div>
            </div>

            @if($productType === 'variable' && count($colorMap))
                <div class="pdp-section">
                    <div class="pdp-section-head">
                        <h2 class="pdp-section-title">Colores</h2>
                        <span class="mini" data-selected-color-label>{{ data_get($productState, 'color', '') }}</span>
                    </div>
                    <div class="pdp-color-grid" data-color-grid>
                        @foreach($colorMap as $color)
                            <button
                                type="button"
                                class="pdp-color-btn {{ data_get($productState, 'color') === $color['name'] ? 'is-active' : '' }}"
                                data-color="{{ $color['name'] }}"
                            >
                                <span class="pdp-color-image">
                                    @if($color['image'])
                                        <img src="{{ $color['image'] }}" alt="{{ $color['name'] }}" loading="lazy">
                                    @endif
                                    <span class="pdp-color-dot" style="background: {{ $color['swatch'] }}"></span>
                                </span>
                                <span class="pdp-color-name">{{ $color['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="pdp-section">
                <div class="pdp-section-head">
                    <h2 class="pdp-section-title">Tallas</h2>
                    <button type="button" class="pdp-guide-btn">Guia de tallas</button>
                </div>
                <div class="pdp-size-grid" data-size-grid>
                    @foreach(data_get($productState, 'sizes', []) as $size)
                        <button
                            type="button"
                            class="pdp-size-btn {{ $size['selected'] ? 'is-active' : '' }} {{ $size['status'] === 'soldout' ? 'is-soldout' : '' }}"
                            data-size="{{ $size['name'] }}"
                            data-variation-id="{{ $size['variation_id'] }}"
                            @disabled($size['status'] === 'soldout')
                        >
                            <span class="pdp-size-name">{{ $size['name'] }}</span>
                            <span class="pdp-size-stock">
                                {{ $size['status'] === 'soldout' ? 'Agotado' : ($size['status'] === 'low' ? 'Pocas unidades' : 'Disponible') }}
                            </span>
                        </button>
                    @endforeach
                </div>
                <div class="pdp-stock-row">
                    <span>SKU <strong data-product-sku>{{ data_get($productState, 'sku', $product['sku'] ?? '') }}</strong></span>
                    <span data-product-stock>{{ data_get($productState, 'stock_label', 'Disponible') }}</span>
                </div>
            </div>

            <div class="pdp-section">
                <form id="product-purchase-form" method="post" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    <input type="hidden" name="variation_id" value="{{ data_get($productState, 'variation_id') }}" data-variation-input>

                    <div class="pdp-qty-row">
                        <input class="pdp-qty" type="number" min="1" max="20" name="quantity" value="1">
                    </div>
                </form>

                <div class="pdp-cta-stack">
                    <div class="pdp-action-row">
                        <button type="submit" form="product-purchase-form" class="pdp-btn secondary">Añadir al carrito</button>
                        <button type="submit" form="product-purchase-form" name="buy_now" value="1" class="pdp-btn primary">Comprar ahora</button>
                        <button type="button" class="pdp-icon-btn" data-wishlist-toggle aria-label="Wishlist">♡</button>
                        <button type="button" class="pdp-icon-btn" data-share-product aria-label="Compartir">↗</button>
                    </div>
                </div>
            </div>

            <div class="pdp-section">
                <div class="pdp-benefits">
                    <div class="pdp-benefit"><span class="pdp-benefit-icon">✓</span><strong>Envío rápido</strong><span>Despacho ágil desde la tienda dueña del producto.</span></div>
                    <div class="pdp-benefit"><span class="pdp-benefit-icon">⌁</span><strong>Pago seguro</strong><span>Tu orden se procesa con checkout protegido y validación clara.</span></div>
                    <div class="pdp-benefit"><span class="pdp-benefit-icon">↺</span><strong>Cambios fáciles</strong><span>Soporte para acompañarte antes y después de la compra.</span></div>
                    <div class="pdp-benefit"><span class="pdp-benefit-icon">✦</span><strong>Garantía</strong><span>Respaldo del vendedor conectado a Hub.</span></div>
                    <div class="pdp-benefit"><span class="pdp-benefit-icon">☏</span><strong>Soporte</strong><span>Atención rápida para pedidos, tallas y seguimiento.</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="pdp-block pdp-reveal">
        <div class="pdp-block-head">
            <div>
                <h2 class="pdp-block-title">Detalles del producto</h2>
                <p class="pdp-block-copy">Información esencial, materiales y cuidados en un formato fácil de escanear.</p>
            </div>
        </div>

        <div class="pdp-accordion-list">
            @foreach($accordionItems as $title => $copy)
                <details class="pdp-accordion" {{ $loop->first ? 'open' : '' }}>
                    <summary><span>{{ $title }}</span><span>+</span></summary>
                    <div class="pdp-accordion-copy">{{ $copy }}</div>
                </details>
            @endforeach
        </div>
    </section>

    <section class="pdp-block pdp-reveal">
        <div class="pdp-block-head">
            <div>
                <h2 class="pdp-block-title">Reseñas</h2>
                <p class="pdp-block-copy">{{ $reviewCount }} clientas ya dejaron su experiencia.</p>
            </div>
        </div>

        <div class="pdp-review-toolbar">
            <button type="button" class="pdp-filter-btn is-active" data-review-filter="all">Todas</button>
            <button type="button" class="pdp-filter-btn" data-review-filter="photos">Con fotos</button>
            <button type="button" class="pdp-filter-btn" data-review-filter="5">5 estrellas</button>
            <button type="button" class="pdp-filter-btn" data-review-filter="recent">Más recientes</button>
            <button type="button" class="pdp-filter-btn" data-review-filter="helpful">Más útiles</button>
        </div>

        <div class="pdp-review-list" data-review-list>
            @foreach($reviews as $review)
                <article
                    class="pdp-review-card"
                    data-review-card
                    data-rating="{{ $review['rating'] }}"
                    data-photos="{{ $review['with_photo'] ? '1' : '0' }}"
                    data-likes="{{ $review['likes'] }}"
                    data-date="{{ $review['timestamp'] }}"
                >
                    <div class="pdp-review-photo">
                        @if($review['with_photo'])
                            <img src="{{ $review['image'] }}" alt="{{ $review['name'] }}" loading="lazy">
                        @endif
                    </div>
                    <div>
                        <div class="pdp-review-head">
                            <strong>{{ $review['name'] }}</strong>
                            <span class="pdp-rating-strong">★ {{ number_format($review['rating'], 1) }}</span>
                            <span class="pdp-review-meta">{{ $review['date'] }}</span>
                        </div>
                        <p class="pdp-review-text">{{ $review['text'] }}</p>
                        <div class="pdp-review-tags">
                            <span class="pdp-mini-pill">Talla {{ $review['size'] }}</span>
                            <span class="pdp-mini-pill">{{ $review['height'] }} cm</span>
                            <span class="pdp-mini-pill">{{ $review['weight'] }} kg</span>
                            <span class="pdp-mini-pill">{{ $review['color'] }}</span>
                            <span class="pdp-mini-pill">❤ {{ $review['likes'] }}</span>
                        </div>
                        @if($review['store_reply'])
                            <div class="pdp-store-reply"><strong>Tienda:</strong> {{ $review['store_reply'] }}</div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    @if(count($recommendedProducts))
        <section class="pdp-block pdp-reveal">
            <div class="pdp-block-head">
                <div>
                    <h2 class="pdp-block-title">Recomendados para ti</h2>
                    <p class="pdp-block-copy">Más referencias conectadas a la misma vibra del producto que estás viendo.</p>
                </div>
            </div>

            <div class="pdp-carousel">
                @foreach($recommendedProducts as $item)
                    <article class="pdp-mini-card">
                        <a href="{{ route('products.show', $item['id']) }}" class="pdp-card-media is-loading" data-media-shell>
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy">
                            <div class="pdp-card-actions">
                                <button type="button" class="pdp-card-fab" data-wishlist-toggle>♡</button>
                                <button
                                    type="button"
                                    class="pdp-card-quick"
                                    data-quick-view
                                    data-name="{{ $item['name'] }}"
                                    data-price="${{ number_format($item['price'], 0, ',', '.') }}"
                                    data-image="{{ $item['image'] }}"
                                    data-url="{{ route('products.show', $item['id']) }}"
                                >Quick View</button>
                            </div>
                        </a>
                        <div class="pdp-mini-body">
                            <div class="eyebrow">{{ data_get($item, 'owner.name', 'M57') }}</div>
                            <a href="{{ route('products.show', $item['id']) }}" class="pdp-mini-title">{{ $item['name'] }}</a>
                            <div class="pdp-mini-price">${{ number_format($item['price'], 0, ',', '.') }}</div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if(count($recentProducts))
        <section class="pdp-block pdp-reveal">
            <div class="pdp-block-head">
                <div>
                    <h2 class="pdp-block-title">Recientemente vistos</h2>
                    <p class="pdp-block-copy">Retoma rápido los productos que has explorado en esta sesión.</p>
                </div>
            </div>

            <div class="pdp-carousel">
                @foreach($recentProducts as $item)
                    <article class="pdp-mini-card">
                        <a href="{{ route('products.show', $item['id']) }}" class="pdp-card-media is-loading" data-media-shell>
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy">
                            @endif
                            <div class="pdp-card-actions">
                                <button type="button" class="pdp-card-fab" data-wishlist-toggle>♡</button>
                                <button
                                    type="button"
                                    class="pdp-card-quick"
                                    data-quick-view
                                    data-name="{{ $item['name'] }}"
                                    data-price="${{ number_format($item['price'], 0, ',', '.') }}"
                                    data-image="{{ $item['image'] }}"
                                    data-url="{{ route('products.show', $item['id']) }}"
                                >Quick View</button>
                            </div>
                        </a>
                        <div class="pdp-mini-body">
                            <div class="eyebrow">{{ data_get($item, 'owner.name', 'M57') }}</div>
                            <a href="{{ route('products.show', $item['id']) }}" class="pdp-mini-title">{{ $item['name'] }}</a>
                            <div class="pdp-mini-price">${{ number_format($item['price'], 0, ',', '.') }}</div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="pdp-mobile-bar">
        <div class="pdp-mobile-price">
            <span class="mini">Precio</span>
            <strong data-mobile-price>${{ number_format($initialPrice, 0, ',', '.') }}</strong>
        </div>
        <button type="submit" form="product-purchase-form" class="pdp-btn secondary">Añadir</button>
        <button type="submit" form="product-purchase-form" name="buy_now" value="1" class="pdp-btn primary">Comprar</button>
    </div>

    <dialog class="pdp-dialog" data-quick-view-dialog>
        <button type="button" class="pdp-dialog-close" data-quick-view-close>×</button>
        <div class="pdp-dialog-grid">
            <img src="" alt="" data-quick-view-image>
            <div class="pdp-dialog-body">
                <div class="eyebrow">Vista rápida</div>
                <h3 class="pdp-block-title" style="font-size:28px" data-quick-view-name></h3>
                <div class="pdp-mini-price" data-quick-view-price></div>
                <a href="#" class="pdp-btn primary" data-quick-view-link>Ver producto</a>
            </div>
        </div>
    </dialog>

    <script>
        (() => {
            const stateUrl = @json(route('products.state', $product['id']));
            const productName = @json($product['name']);
            const initialState = @json($productState);
            const stage = document.querySelector('[data-gallery-stage]');
            const thumbs = document.querySelector('[data-gallery-thumbs]');
            const sizeGrid = document.querySelector('[data-size-grid]');
            const colorGrid = document.querySelector('[data-color-grid]');
            const variationInput = document.querySelector('[data-variation-input]');
            const colorLabel = document.querySelector('[data-selected-color-label]');
            const price = document.querySelector('[data-product-price]');
            const oldPrice = document.querySelector('[data-product-old-price]');
            const discount = document.querySelector('[data-product-discount]');
            const installments = document.querySelector('[data-product-installments]');
            const sku = document.querySelector('[data-product-sku]');
            const stock = document.querySelector('[data-product-stock]');
            const mobilePrice = document.querySelector('[data-mobile-price]');
            const mediaShells = () => document.querySelectorAll('[data-media-shell]');
            let currentState = initialState;
            let currentMediaIndex = 0;

            const money = (value) => new Intl.NumberFormat('es-CO').format(Math.round(value || 0));

            const removeLoading = (target) => target.closest('[data-media-shell]')?.classList.remove('is-loading');

            const renderStage = () => {
                if (!stage) return;

                const media = currentState.media?.[currentMediaIndex] || currentState.media?.[0];
                stage.parentElement.classList.add('is-loading');

                if (!media) {
                    stage.innerHTML = '';
                    return;
                }

                stage.innerHTML = media.type === 'video'
                    ? `<video src="${media.src}" controls playsinline preload="metadata"></video>`
                    : `<img src="${media.src}" alt="${productName}" loading="eager">`;

                stage.querySelectorAll('img, video').forEach((node) => {
                    if (node.complete || node.readyState >= 2) {
                        removeLoading(node);
                    }
                    node.addEventListener('load', () => removeLoading(node), { once: true });
                    if (node.tagName === 'VIDEO') {
                        node.addEventListener('loadeddata', () => removeLoading(node), { once: true });
                    }
                });
            };

            const renderThumbs = () => {
                if (!thumbs) return;

                thumbs.innerHTML = (currentState.media || []).map((item, index) => `
                    <button type="button" class="pdp-thumb ${index === currentMediaIndex ? 'is-active' : ''}" data-thumb-index="${index}">
                        ${item.type === 'video'
                            ? `<video src="${item.src}" muted playsinline preload="metadata"></video>`
                            : `<img src="${item.src}" alt="" loading="lazy">`
                        }
                    </button>
                `).join('');
            };

            const renderSizes = () => {
                if (!sizeGrid) return;

                sizeGrid.innerHTML = (currentState.sizes || []).map((item) => `
                    <button
                        type="button"
                        class="pdp-size-btn ${item.selected ? 'is-active' : ''} ${item.status === 'soldout' ? 'is-soldout' : ''}"
                        data-size="${item.name}"
                        data-variation-id="${item.variation_id}"
                        ${item.status === 'soldout' ? 'disabled' : ''}
                    >
                        <span class="pdp-size-name">${item.name}</span>
                        <span class="pdp-size-stock">${item.status === 'soldout' ? 'Agotado' : item.status === 'low' ? 'Pocas unidades' : 'Disponible'}</span>
                    </button>
                `).join('');
            };

            const renderMeta = () => {
                variationInput.value = currentState.variation_id || '';
                if (colorLabel) colorLabel.textContent = currentState.color || '';
                if (price) price.textContent = `$${money(currentState.price)}`;
                if (oldPrice) oldPrice.textContent = `$${money(currentState.old_price)}`;
                if (discount) discount.textContent = `-${currentState.discount_percent || 0}%`;
                if (installments) {
                    installments.textContent = currentState.installments
                        ? `o ${currentState.installments} cuotas de $${money((currentState.price || 0) / currentState.installments)}`
                        : '';
                }
                if (sku) sku.textContent = currentState.sku || '';
                if (stock) stock.textContent = currentState.stock_label || '';
                if (mobilePrice) mobilePrice.textContent = `$${money(currentState.price)}`;
            };

            const syncColors = () => {
                colorGrid?.querySelectorAll('[data-color]').forEach((button) => {
                    button.classList.toggle('is-active', button.dataset.color === currentState.color);
                });
            };

            const renderState = () => {
                currentMediaIndex = 0;
                renderStage();
                renderThumbs();
                renderSizes();
                renderMeta();
                syncColors();
            };

            const fetchState = async (params) => {
                const url = new URL(stateUrl, window.location.origin);
                Object.entries(params).forEach(([key, value]) => value && url.searchParams.set(key, value));
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } });
                const json = await response.json();
                currentState = json.data;
                renderState();
            };

            colorGrid?.addEventListener('click', (event) => {
                const button = event.target.closest('[data-color]');
                if (!button) return;
                fetchState({ color: button.dataset.color });
            });

            sizeGrid?.addEventListener('click', (event) => {
                const button = event.target.closest('[data-size]');
                if (!button) return;
                fetchState({
                    color: currentState.color,
                    size: button.dataset.size,
                    variation_id: button.dataset.variationId,
                });
            });

            thumbs?.addEventListener('click', (event) => {
                const button = event.target.closest('[data-thumb-index]');
                if (!button) return;
                currentMediaIndex = Number(button.dataset.thumbIndex || 0);
                renderStage();
                renderThumbs();
            });

            stage?.addEventListener('mousemove', (event) => {
                const rect = stage.getBoundingClientRect();
                stage.style.setProperty('--zoom-x', `${((event.clientX - rect.left) / rect.width) * 100}%`);
                stage.style.setProperty('--zoom-y', `${((event.clientY - rect.top) / rect.height) * 100}%`);
            });

            let touchStartX = 0;
            stage?.addEventListener('touchstart', (event) => {
                touchStartX = event.changedTouches[0].clientX;
            }, { passive: true });
            stage?.addEventListener('touchend', (event) => {
                const delta = event.changedTouches[0].clientX - touchStartX;
                const total = currentState.media?.length || 0;
                if (!total || Math.abs(delta) < 30) return;
                currentMediaIndex = delta < 0 ? Math.min(currentMediaIndex + 1, total - 1) : Math.max(currentMediaIndex - 1, 0);
                renderStage();
                renderThumbs();
            }, { passive: true });

            mediaShells().forEach((shell) => {
                shell.querySelectorAll('img').forEach((img) => {
                    if (img.complete) {
                        shell.classList.remove('is-loading');
                    } else {
                        img.addEventListener('load', () => shell.classList.remove('is-loading'), { once: true });
                    }
                });
            });

            const observer = 'IntersectionObserver' in window
                ? new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12 })
                : null;

            document.querySelectorAll('.pdp-reveal').forEach((item) => {
                if (observer) observer.observe(item);
                else item.classList.add('is-visible');
            });

            document.querySelectorAll('[data-wishlist-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    button.classList.toggle('is-active');
                    button.textContent = button.classList.contains('is-active') ? '♥' : '♡';
                });
            });

            document.querySelector('[data-share-product]')?.addEventListener('click', async () => {
                const payload = { title: @json($product['name']), url: window.location.href };
                if (navigator.share) {
                    await navigator.share(payload);
                    return;
                }
                await navigator.clipboard.writeText(payload.url);
            });

            const dialog = document.querySelector('[data-quick-view-dialog]');
            document.querySelectorAll('[data-quick-view]').forEach((button) => {
                button.addEventListener('click', () => {
                    dialog.querySelector('[data-quick-view-name]').textContent = button.dataset.name || '';
                    dialog.querySelector('[data-quick-view-price]').textContent = button.dataset.price || '';
                    dialog.querySelector('[data-quick-view-image]').src = button.dataset.image || '';
                    dialog.querySelector('[data-quick-view-link]').href = button.dataset.url || '#';
                    dialog.showModal();
                });
            });
            document.querySelector('[data-quick-view-close]')?.addEventListener('click', () => dialog.close());
            dialog?.addEventListener('click', (event) => {
                if (event.target === dialog) dialog.close();
            });

            const reviewList = document.querySelector('[data-review-list]');
            document.querySelectorAll('[data-review-filter]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.querySelectorAll('[data-review-filter]').forEach((item) => item.classList.remove('is-active'));
                    button.classList.add('is-active');

                    const cards = Array.from(reviewList.querySelectorAll('[data-review-card]'));
                    cards.forEach((card) => card.classList.remove('is-hidden'));

                    if (button.dataset.reviewFilter === 'photos') {
                        cards.forEach((card) => card.classList.toggle('is-hidden', card.dataset.photos !== '1'));
                    }
                    if (button.dataset.reviewFilter === '5') {
                        cards.forEach((card) => card.classList.toggle('is-hidden', card.dataset.rating !== '5'));
                    }
                    if (button.dataset.reviewFilter === 'recent') {
                        cards.sort((a, b) => Number(b.dataset.date) - Number(a.dataset.date)).forEach((card) => reviewList.appendChild(card));
                    }
                    if (button.dataset.reviewFilter === 'helpful') {
                        cards.sort((a, b) => Number(b.dataset.likes) - Number(a.dataset.likes)).forEach((card) => reviewList.appendChild(card));
                    }
                });
            });

            renderState();
        })();
    </script>
@endsection
