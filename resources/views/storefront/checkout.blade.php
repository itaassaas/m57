@extends('storefront.layout', ['title' => 'Checkout M57', 'cartCount' => $cartCount])

@section('content')
    @php
        $nameParts = preg_split('/\s+/', trim((string) old('customer_name', '')), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';
        $shippingOptions = [
            ['id' => 'express', 'name' => 'Envío Express', 'time' => '24-48 horas', 'price' => 14900, 'description' => 'Prioridad alta y seguimiento en tiempo real.'],
            ['id' => 'standard', 'name' => 'Envío Estándar', 'time' => '3-5 días', 'price' => 7900, 'description' => 'La mejor opción para compra regular.'],
            ['id' => 'pickup', 'name' => 'Recogida programada', 'time' => 'Coordinar con tienda', 'price' => 0, 'description' => 'Disponible con comercios que permiten entrega directa.'],
        ];
        $paymentOptions = [
            ['id' => 'card', 'label' => 'Tarjeta', 'channel' => 'tarjeta', 'type' => 'anticipado', 'copy' => 'Visa, MasterCard y débito con verificación segura.'],
            ['id' => 'pse', 'label' => 'PSE', 'channel' => 'pse', 'type' => 'anticipado', 'copy' => 'Pago instantáneo desde tu banco colombiano.'],
            ['id' => 'nequi', 'label' => 'Nequi', 'channel' => 'nequi', 'type' => 'anticipado', 'copy' => 'Aprobación rápida desde tu celular.'],
            ['id' => 'daviplata', 'label' => 'Daviplata', 'channel' => 'daviplata', 'type' => 'anticipado', 'copy' => 'Transferencia simple para cerrar la compra.'],
            ['id' => 'cod', 'label' => 'Contra entrega', 'channel' => 'contra-entrega', 'type' => 'cod', 'copy' => 'Paga al recibir cuando la tienda lo permita.'],
            ['id' => 'paypal', 'label' => 'PayPal', 'channel' => 'paypal', 'type' => 'anticipado', 'copy' => 'Checkout rápido para cuentas internacionales.'],
            ['id' => 'mercadopago', 'label' => 'Mercado Pago', 'channel' => 'mercadopago', 'type' => 'anticipado', 'copy' => 'Paga con saldo, tarjetas o métodos locales.'],
            ['id' => 'wompi', 'label' => 'Wompi', 'channel' => 'wompi', 'type' => 'anticipado', 'copy' => 'Validación segura y confirmación inmediata.'],
        ];
        $initialShipping = $shippingOptions[1];
        $estimatedDate = now()->addDays(4)->translatedFormat('d M');
    @endphp

    <style>
        .checkout-shell {
            display: grid;
            grid-template-columns: minmax(0, 68%) minmax(320px, 32%);
            gap: 28px;
            padding: 28px 0 56px;
        }
        .checkout-main {
            min-width: 0;
        }
        .checkout-sidebar {
            position: sticky;
            top: 92px;
            align-self: start;
        }
        .checkout-stepper {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .checkout-step {
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
        .checkout-step.is-active {
            background: #111;
            color: #fff;
            border-color: #111;
            box-shadow: 0 18px 36px rgba(17,17,17,.12);
        }
        .checkout-step-index {
            font-size: 20px;
            line-height: 1;
        }
        .checkout-step-title {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: -.02em;
        }
        .checkout-step-copy {
            font-size: 12px;
            color: #71717a;
        }
        .checkout-step.is-active .checkout-step-copy {
            color: rgba(255,255,255,.68);
        }
        .checkout-card,
        .checkout-summary,
        .checkout-product {
            border-radius: 16px;
            background: #fff;
            border: 1px solid #ececef;
            box-shadow: 0 10px 24px rgba(17,17,17,.04);
        }
        .checkout-card {
            padding: 24px;
            margin-bottom: 16px;
        }
        .checkout-card-head,
        .checkout-summary-head,
        .checkout-product-head,
        .checkout-totals-row,
        .checkout-inline,
        .checkout-actions,
        .checkout-product-meta,
        .checkout-security,
        .checkout-summary-payments {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .checkout-card-head,
        .checkout-summary-head {
            justify-content: space-between;
            margin-bottom: 18px;
        }
        .checkout-title {
            margin: 0;
            font-size: 20px;
            line-height: 1;
            letter-spacing: -.04em;
        }
        .checkout-kicker {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #71717a;
        }
        .checkout-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .checkout-grid .full {
            grid-column: 1 / -1;
        }
        .checkout-field-wrap {
            display: grid;
            gap: 8px;
        }
        .checkout-label {
            font-size: 13px;
            font-weight: 700;
            color: #3f3f46;
        }
        .checkout-field,
        .checkout-textarea {
            min-height: 54px;
            width: 100%;
            padding: 0 16px;
            border-radius: 14px;
            border: 1px solid #e6e6e9;
            background: #fcfcfc;
            transition: border-color 250ms var(--ease-out), box-shadow 250ms var(--ease-out), background 250ms var(--ease-out);
        }
        .checkout-textarea {
            min-height: 120px;
            padding: 14px 16px;
            resize: vertical;
        }
        .checkout-field:focus,
        .checkout-textarea:focus {
            outline: 0;
            border-color: #111;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(17,17,17,.05);
        }
        .checkout-field.is-invalid,
        .checkout-textarea.is-invalid {
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220,38,38,.08);
        }
        .checkout-help,
        .checkout-error,
        .checkout-meta-copy {
            font-size: 12px;
            line-height: 1.5;
        }
        .checkout-help,
        .checkout-meta-copy {
            color: #71717a;
        }
        .checkout-error {
            color: #b91c1c;
            display: none;
        }
        .checkout-error.is-visible {
            display: block;
        }
        .checkout-option-grid {
            display: grid;
            gap: 12px;
        }
        .checkout-option {
            position: relative;
            padding: 18px;
            border-radius: 16px;
            border: 1px solid #ececef;
            background: #fff;
            transition: transform 250ms var(--ease-out), border-color 250ms var(--ease-out), box-shadow 250ms var(--ease-out), background 250ms var(--ease-out);
        }
        .checkout-option.is-selected {
            border-color: #111;
            background: #fafafa;
            box-shadow: 0 16px 32px rgba(17,17,17,.06);
        }
        .checkout-option input {
            position: absolute;
            opacity: 0;
            inset: 0;
        }
        .checkout-option-head {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 14px;
        }
        .checkout-option-title {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -.02em;
        }
        .checkout-option-price {
            font-size: 15px;
            font-weight: 800;
        }
        .checkout-option-time {
            color: #52525b;
            font-size: 13px;
        }
        .checkout-radio {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 1.5px solid #d4d4d8;
            flex: 0 0 auto;
            position: relative;
            margin-top: 2px;
        }
        .checkout-option.is-selected .checkout-radio {
            border-color: #111;
        }
        .checkout-option.is-selected .checkout-radio::after {
            content: "";
            position: absolute;
            inset: 4px;
            border-radius: 50%;
            background: #111;
        }
        .checkout-payment-grid {
            display: grid;
            gap: 12px;
        }
        .checkout-payment-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 250ms var(--ease-out), opacity 250ms var(--ease-out), margin-top 250ms var(--ease-out);
            opacity: 0;
        }
        .checkout-option.is-selected .checkout-payment-body {
            max-height: 140px;
            opacity: 1;
            margin-top: 14px;
        }
        .checkout-product-list {
            display: grid;
            gap: 14px;
        }
        .checkout-product {
            padding: 16px;
            display: grid;
            grid-template-columns: 96px minmax(0, 1fr) auto;
            gap: 16px;
            position: relative;
            overflow: hidden;
        }
        .checkout-product.is-loading::after,
        .checkout-product-image.is-loading::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.7) 50%, rgba(255,255,255,0) 100%);
            animation: checkout-shimmer 1.15s linear infinite;
        }
        @keyframes checkout-shimmer {
            from { transform: translateX(-100%); }
            to { transform: translateX(100%); }
        }
        .checkout-product-image {
            width: 96px;
            height: 120px;
            border-radius: 14px;
            overflow: hidden;
            background: linear-gradient(180deg, #f7f7f8 0%, #ececef 100%);
            position: relative;
        }
        .checkout-product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .checkout-product-title {
            font-size: 15px;
            line-height: 1.45;
            margin-bottom: 8px;
        }
        .checkout-product-meta {
            gap: 8px;
            margin-bottom: 12px;
        }
        .checkout-pill {
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
        .checkout-qty {
            width: 78px;
            min-height: 44px;
            border-radius: 12px;
            border: 1px solid #e6e6e9;
            padding: 0 14px;
            background: #fff;
        }
        .checkout-link-btn,
        .checkout-ghost-btn,
        .checkout-primary-btn,
        .checkout-mobile-btn {
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
        .checkout-primary-btn,
        .checkout-mobile-btn {
            background: #111;
            color: #fff;
            border-color: #111;
        }
        .checkout-link-btn {
            min-height: auto;
            padding: 0;
            border: 0;
            background: transparent;
            color: #52525b;
            font-size: 12px;
            font-weight: 700;
        }
        .checkout-product-side {
            display: grid;
            justify-items: end;
            align-content: space-between;
            gap: 12px;
            min-width: 110px;
        }
        .checkout-product-price {
            font-size: 20px;
            line-height: 1;
            letter-spacing: -.04em;
            font-weight: 800;
        }
        .checkout-summary {
            padding: 22px;
        }
        .checkout-summary-list {
            display: grid;
            gap: 12px;
        }
        .checkout-totals-row {
            justify-content: space-between;
            color: #52525b;
            font-size: 14px;
        }
        .checkout-totals-row.total {
            padding-top: 14px;
            margin-top: 6px;
            border-top: 1px solid #ececef;
            color: #111;
            font-size: 17px;
            font-weight: 800;
        }
        .checkout-coupon {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            margin: 18px 0;
        }
        .checkout-summary-payments {
            gap: 8px;
            margin-top: 18px;
        }
        .checkout-pay-chip {
            min-height: 30px;
            padding: 0 10px;
            border-radius: 999px;
            background: #f7f7f8;
            border: 1px solid #ececef;
            font-size: 11px;
            font-weight: 800;
        }
        .checkout-security {
            display: grid;
            gap: 10px;
            margin-top: 18px;
        }
        .checkout-security-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #52525b;
            font-size: 13px;
        }
        .checkout-security-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f4f4f5;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #111;
        }
        .checkout-estimate {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #fafafa;
            border: 1px solid #ececef;
            display: grid;
            gap: 6px;
        }
        .checkout-mobile-bar {
            display: none;
        }
        .checkout-reveal {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity 250ms var(--ease-out), transform 250ms var(--ease-out);
        }
        .checkout-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        @media (hover: hover) and (pointer: fine) {
            .checkout-step:hover,
            .checkout-option:hover,
            .checkout-primary-btn:hover,
            .checkout-ghost-btn:hover,
            .checkout-mobile-btn:hover {
                transform: scale(1.02);
            }
            .checkout-primary-btn:hover,
            .checkout-mobile-btn:hover {
                box-shadow: 0 18px 36px rgba(17,17,17,.14);
            }
        }
        @media (max-width: 980px) {
            .checkout-shell {
                grid-template-columns: 1fr;
                padding-bottom: 112px;
            }
            .checkout-sidebar {
                position: static;
            }
        }
        @media (max-width: 760px) {
            .checkout-stepper,
            .checkout-grid,
            .checkout-coupon,
            .checkout-product {
                grid-template-columns: 1fr;
            }
            .checkout-product-side {
                justify-items: start;
                min-width: 0;
            }
            .checkout-summary {
                padding: 18px;
            }
            .checkout-mobile-bar {
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
            .checkout-mobile-total {
                display: grid;
                gap: 4px;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            .checkout-step,
            .checkout-option,
            .checkout-payment-body,
            .checkout-reveal,
            .checkout-primary-btn,
            .checkout-ghost-btn,
            .checkout-link-btn,
            .checkout-mobile-btn {
                transition: none !important;
                animation: none !important;
            }
        }
    </style>

    <form method="post" action="{{ route('checkout.place') }}" id="checkout-form">
        @csrf
        <input type="hidden" name="customer_name" value="{{ old('customer_name') }}" data-full-name>
        <input type="hidden" name="payment_type" value="{{ old('payment_type', $initialShipping['id'] ? 'anticipado' : 'cod') }}" data-payment-type>
        <input type="hidden" name="payment_channel" value="{{ old('payment_channel', 'pse') }}" data-payment-channel>

        <section class="checkout-shell">
            <div class="checkout-main">
                <div class="checkout-stepper checkout-reveal">
                    <div class="checkout-step">
                        <span class="checkout-step-index">🛒</span>
                        <span class="checkout-step-title">Carrito</span>
                        <span class="checkout-step-copy">{{ $cartCount }} artículos listos.</span>
                    </div>
                    <div class="checkout-step is-active">
                        <span class="checkout-step-index">📦</span>
                        <span class="checkout-step-title">Envío</span>
                        <span class="checkout-step-copy">Dirección y entrega.</span>
                    </div>
                    <div class="checkout-step is-active">
                        <span class="checkout-step-index">💳</span>
                        <span class="checkout-step-title">Pago</span>
                        <span class="checkout-step-copy">Método y confirmación.</span>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step-index">✅</span>
                        <span class="checkout-step-title">Confirmación</span>
                        <span class="checkout-step-copy">Orden separada en Hub.</span>
                    </div>
                </div>

                <section class="checkout-card checkout-reveal">
                    <div class="checkout-card-head">
                        <div>
                            <div class="checkout-kicker">Contacto</div>
                            <h1 class="checkout-title">Finaliza tu compra</h1>
                        </div>
                        <div class="checkout-meta-copy">Checkout rápido, limpio y sin fricción.</div>
                    </div>

                    <div class="checkout-grid">
                        <div class="checkout-field-wrap">
                            <label class="checkout-label" for="customer_email">Email</label>
                            <input class="checkout-field" id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" autocomplete="email" data-validate="email">
                            <span class="checkout-error" data-error-for="customer_email">Ingresa un email válido.</span>
                        </div>
                        <div class="checkout-field-wrap">
                            <label class="checkout-label" for="customer_phone">Teléfono</label>
                            <input class="checkout-field" id="customer_phone" type="text" name="customer_phone" value="{{ old('customer_phone') }}" autocomplete="tel" required data-validate="required">
                            <span class="checkout-error" data-error-for="customer_phone">Este campo es obligatorio.</span>
                        </div>
                        <div class="checkout-field-wrap">
                            <label class="checkout-label" for="first_name">Nombre</label>
                            <input class="checkout-field" id="first_name" type="text" name="first_name" value="{{ $firstName }}" autocomplete="given-name" required data-first-name data-validate="required">
                            <span class="checkout-error" data-error-for="first_name">Este campo es obligatorio.</span>
                        </div>
                        <div class="checkout-field-wrap">
                            <label class="checkout-label" for="last_name">Apellido</label>
                            <input class="checkout-field" id="last_name" type="text" name="last_name" value="{{ $lastName }}" autocomplete="family-name" required data-last-name data-validate="required">
                            <span class="checkout-error" data-error-for="last_name">Este campo es obligatorio.</span>
                        </div>
                    </div>
                </section>

                <section class="checkout-card checkout-reveal">
                    <div class="checkout-card-head">
                        <div>
                            <div class="checkout-kicker">Dirección</div>
                            <h2 class="checkout-title">Entrega</h2>
                        </div>
                        <div class="checkout-meta-copy">Departamentos y ciudades conectados desde Hub.</div>
                    </div>

                    <div class="checkout-grid">
                        <div class="checkout-field-wrap">
                            <label class="checkout-label" for="country">País</label>
                            <input class="checkout-field" id="country" type="text" name="country" value="{{ old('country', 'Colombia') }}" autocomplete="country-name">
                        </div>
                        <div class="checkout-field-wrap">
                            <label class="checkout-label" for="shipping_state">Departamento</label>
                            <select class="checkout-field" id="shipping_state" name="shipping_state" required data-validate="required" data-department-select data-cities-url="{{ url('/checkout/departments') }}">
                                <option value="">Selecciona departamento</option>
                                @foreach($departments as $department)
                                    @php
                                        $departmentName = (string) ($department['name'] ?? $department['nombre'] ?? $department['department'] ?? '');
                                        $departmentId = (string) ($department['id'] ?? $department['cod'] ?? '');
                                    @endphp
                                    @if($departmentName !== '')
                                        <option value="{{ $departmentName }}" data-id="{{ $departmentId }}" @selected(old('shipping_state') === $departmentName)>{{ $departmentName }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <span class="checkout-error" data-error-for="shipping_state">Este campo es obligatorio.</span>
                        </div>
                        <div class="checkout-field-wrap">
                            <label class="checkout-label" for="shipping_city">Ciudad</label>
                            <select class="checkout-field" id="shipping_city" name="shipping_city" required data-validate="required" data-city-select>
                                <option value="">{{ old('shipping_state') ? 'Selecciona ciudad' : 'Primero elige departamento' }}</option>
                                @if(old('shipping_city'))
                                    <option value="{{ old('shipping_city') }}" selected>{{ old('shipping_city') }}</option>
                                @endif
                            </select>
                            <span class="checkout-error" data-error-for="shipping_city">Este campo es obligatorio.</span>
                        </div>
                        <div class="checkout-field-wrap full">
                            <label class="checkout-label" for="shipping_address">Dirección</label>
                            <input class="checkout-field" id="shipping_address" type="text" name="shipping_address" value="{{ old('shipping_address') }}" autocomplete="street-address" required data-validate="required">
                            <span class="checkout-error" data-error-for="shipping_address">Este campo es obligatorio.</span>
                        </div>
                        <div class="checkout-field-wrap full">
                            <label class="checkout-label" for="shipping_locality">Apartamento / Barrio</label>
                            <input class="checkout-field" id="shipping_locality" type="text" name="shipping_locality" value="{{ old('shipping_locality') }}" autocomplete="address-line2">
                        </div>
                    </div>
                </section>

                <section class="checkout-card checkout-reveal">
                    <div class="checkout-card-head">
                        <div>
                            <div class="checkout-kicker">Método de envío</div>
                            <h2 class="checkout-title">Elige cómo recibirlo</h2>
                        </div>
                    </div>

                    <div class="checkout-option-grid" data-shipping-options>
                        @foreach($shippingOptions as $option)
                            <label class="checkout-option {{ $loop->iteration === 2 ? 'is-selected' : '' }}">
                                <input type="radio" name="shipping_option" value="{{ $option['id'] }}" data-shipping-option data-price="{{ $option['price'] }}" @checked($loop->iteration === 2)>
                                <div class="checkout-option-head">
                                    <span class="checkout-radio"></span>
                                    <div style="flex:1 1 auto;">
                                        <div class="checkout-option-title">{{ $option['name'] }}</div>
                                        <div class="checkout-option-time">{{ $option['time'] }}</div>
                                        <div class="checkout-help">{{ $option['description'] }}</div>
                                    </div>
                                    <div class="checkout-option-price">${{ number_format($option['price'], 0, ',', '.') }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="checkout-card checkout-reveal">
                    <div class="checkout-card-head">
                        <div>
                            <div class="checkout-kicker">Pago</div>
                            <h2 class="checkout-title">Selecciona tu método</h2>
                        </div>
                    </div>

                    <div class="checkout-payment-grid" data-payment-options>
                        @foreach($paymentOptions as $option)
                            <label class="checkout-option {{ $option['id'] === 'pse' ? 'is-selected' : '' }}">
                                <input
                                    type="radio"
                                    name="checkout_payment_ui"
                                    value="{{ $option['id'] }}"
                                    data-payment-option
                                    data-payment-type="{{ $option['type'] }}"
                                    data-payment-channel="{{ $option['channel'] }}"
                                    @checked($option['id'] === 'pse')
                                >
                                <div class="checkout-option-head">
                                    <span class="checkout-radio"></span>
                                    <div style="flex:1 1 auto;">
                                        <div class="checkout-option-title">{{ $option['label'] }}</div>
                                        <div class="checkout-help">{{ $option['copy'] }}</div>
                                    </div>
                                </div>
                                <div class="checkout-payment-body">
                                    <div class="checkout-inline">
                                        <span class="checkout-pill">{{ strtoupper($option['channel']) }}</span>
                                        <span class="checkout-help">Expande suavemente para reforzar la selección actual.</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="checkout-card checkout-reveal">
                    <div class="checkout-card-head">
                        <div>
                            <div class="checkout-kicker">Notas del pedido</div>
                            <h2 class="checkout-title">Últimos detalles</h2>
                        </div>
                    </div>

                    <div class="checkout-field-wrap">
                        <label class="checkout-label" for="notes">Notas</label>
                        <textarea class="checkout-textarea" id="notes" name="notes" placeholder="Instrucciones para la entrega, portería, referencias o solicitudes especiales.">{{ old('notes') }}</textarea>
                    </div>
                </section>

                <section class="checkout-card checkout-reveal">
                    <div class="checkout-card-head">
                        <div>
                            <div class="checkout-kicker">Productos</div>
                            <h2 class="checkout-title">Tu pedido</h2>
                        </div>
                        <div class="checkout-meta-copy">Edita cantidades o elimina sin salir del checkout.</div>
                    </div>

                    <div class="checkout-product-list" data-cart-list>
                        @foreach($cartItems as $item)
                            @php($itemKey = $item['product_id'].':'.($item['variation_id'] ?: 0))
                            <article class="checkout-product" data-cart-item data-item-key="{{ $itemKey }}" data-price="{{ $item['price'] }}" data-quantity="{{ $item['quantity'] }}">
                                <div class="checkout-product-image is-loading">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy">
                                </div>
                                <div>
                                    <div class="checkout-product-title">{{ $item['name'] }}</div>
                                    <div class="checkout-product-meta">
                                        @if(!empty($item['attributes']['Color']))
                                            <span class="checkout-pill">Color {{ $item['attributes']['Color'] }}</span>
                                        @endif
                                        @if(!empty($item['attributes']['Talla']))
                                            <span class="checkout-pill">Talla {{ $item['attributes']['Talla'] }}</span>
                                        @elseif(!empty($item['variation_name']))
                                            <span class="checkout-pill">{{ $item['variation_name'] }}</span>
                                        @endif
                                        <span class="checkout-pill">{{ $item['owner_name'] }}</span>
                                    </div>
                                    <div class="checkout-actions">
                                        <input class="checkout-qty" type="number" min="1" max="20" value="{{ $item['quantity'] }}" data-qty-input>
                                        <button type="button" class="checkout-link-btn" data-cart-save>Editar cantidad</button>
                                        <button type="button" class="checkout-link-btn" data-cart-remove>Eliminar</button>
                                        <button type="button" class="checkout-link-btn" data-favorite-toggle>Mover a favoritos</button>
                                    </div>
                                </div>
                                <div class="checkout-product-side">
                                    <div class="checkout-product-price" data-line-total>${{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</div>
                                    <div class="checkout-help">Unitario ${{ number_format($item['price'], 0, ',', '.') }}</div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="checkout-sidebar checkout-reveal">
                <div class="checkout-summary">
                    <div class="checkout-summary-head">
                        <div>
                            <div class="checkout-kicker">Resumen del pedido</div>
                            <h2 class="checkout-title">Total</h2>
                        </div>
                        <span class="checkout-pill" data-summary-items>{{ $cartCount }} artículos</span>
                    </div>

                    <div class="checkout-summary-list">
                        <div class="checkout-totals-row"><span>Subtotal</span><strong data-summary-subtotal>${{ number_format($cartTotal, 0, ',', '.') }}</strong></div>
                        <div class="checkout-totals-row"><span>Descuentos</span><strong data-summary-discount>$0</strong></div>
                        <div class="checkout-totals-row"><span>Envío</span><strong data-summary-shipping>${{ number_format($initialShipping['price'], 0, ',', '.') }}</strong></div>
                        <div class="checkout-totals-row"><span>Impuestos</span><strong data-summary-tax>$0</strong></div>
                        <div class="checkout-totals-row total"><span>Total</span><strong data-summary-total>${{ number_format($cartTotal + $initialShipping['price'], 0, ',', '.') }}</strong></div>
                    </div>

                    <div class="checkout-coupon">
                        <input type="text" class="checkout-field" placeholder="Cupón" data-coupon-input>
                        <button type="button" class="checkout-ghost-btn" data-apply-coupon>Aplicar</button>
                    </div>

                    <div class="checkout-help" data-coupon-message>Prueba `M57SAVE10` para validar el flujo del cupón.</div>

                    <div class="checkout-estimate">
                        <strong>Entrega estimada</strong>
                        <span class="checkout-help">Llega entre hoy y {{ $estimatedDate }} según método seleccionado.</span>
                        <span class="checkout-help">Ahorro actual: <strong data-summary-savings>$0</strong></span>
                    </div>

                    <div class="checkout-summary-payments">
                        <span class="checkout-pay-chip">VISA</span>
                        <span class="checkout-pay-chip">PSE</span>
                        <span class="checkout-pay-chip">NEQUI</span>
                        <span class="checkout-pay-chip">WOMPI</span>
                    </div>

                    <div class="checkout-security">
                        <div class="checkout-security-item"><span class="checkout-security-icon">✓</span>Pago seguro</div>
                        <div class="checkout-security-item"><span class="checkout-security-icon">↺</span>Devoluciones</div>
                        <div class="checkout-security-item"><span class="checkout-security-icon">⚡</span>Envío rápido</div>
                        <div class="checkout-security-item"><span class="checkout-security-icon">☏</span>Soporte</div>
                    </div>

                    <div style="margin-top:18px;">
                        <button type="submit" form="checkout-form" class="checkout-primary-btn" style="width:100%;">Continuar</button>
                    </div>
                </div>
            </aside>
        </section>
    </form>

    <div class="checkout-mobile-bar">
        <div class="checkout-mobile-total">
            <span class="checkout-help">Total</span>
            <strong data-mobile-total>${{ number_format($cartTotal + $initialShipping['price'], 0, ',', '.') }}</strong>
        </div>
        <button type="submit" form="checkout-form" class="checkout-mobile-btn">Comprar</button>
    </div>

    <script>
        (() => {
            const cartList = document.querySelector('[data-cart-list]');
            const qtyUrl = @json(route('cart.update', '__KEY__'));
            const removeUrl = @json(route('cart.remove', '__KEY__'));
            const csrf = @json(csrf_token());
            const subtotalEl = document.querySelector('[data-summary-subtotal]');
            const discountEl = document.querySelector('[data-summary-discount]');
            const shippingEl = document.querySelector('[data-summary-shipping]');
            const taxEl = document.querySelector('[data-summary-tax]');
            const totalEl = document.querySelector('[data-summary-total]');
            const savingsEl = document.querySelector('[data-summary-savings]');
            const itemsEl = document.querySelector('[data-summary-items]');
            const mobileTotalEl = document.querySelector('[data-mobile-total]');
            const paymentTypeInput = document.querySelector('[data-payment-type]');
            const paymentChannelInput = document.querySelector('[data-payment-channel]');
            const fullNameInput = document.querySelector('[data-full-name]');
            const firstNameInput = document.querySelector('[data-first-name]');
            const lastNameInput = document.querySelector('[data-last-name]');
            const couponInput = document.querySelector('[data-coupon-input]');
            const couponMessage = document.querySelector('[data-coupon-message]');
            const validatable = document.querySelectorAll('[data-validate]');
            const departmentSelect = document.querySelector('[data-department-select]');
            const citySelect = document.querySelector('[data-city-select]');
            let shippingPrice = {{ $initialShipping['price'] }};
            let discountAmount = 0;

            const money = (value) => `$${new Intl.NumberFormat('es-CO').format(Math.round(value || 0))}`;

            const cartState = () => {
                const items = Array.from(document.querySelectorAll('[data-cart-item]'));
                const subtotal = items.reduce((sum, item) => {
                    if (item.hidden) return sum;
                    return sum + (Number(item.dataset.price || 0) * Number(item.dataset.quantity || 0));
                }, 0);
                const tax = 0;
                const total = Math.max(0, subtotal - discountAmount + shippingPrice + tax);

                subtotalEl.textContent = money(subtotal);
                discountEl.textContent = `-${money(discountAmount).replace('-', '')}`;
                shippingEl.textContent = money(shippingPrice);
                taxEl.textContent = money(tax);
                totalEl.textContent = money(total);
                savingsEl.textContent = money(discountAmount);
                mobileTotalEl.textContent = money(total);
                itemsEl.textContent = `${items.filter((item) => !item.hidden).length} artículos`;
            };

            const syncFullName = () => {
                fullNameInput.value = [firstNameInput?.value || '', lastNameInput?.value || ''].join(' ').trim();
            };

            const validateField = (field) => {
                const type = field.dataset.validate;
                const value = field.value.trim();
                let valid = true;

                if (type === 'required') valid = value.length > 0;
                if (type === 'email') {
                    field.value = value;
                    valid = value.length === 0 || field.checkValidity();
                }

                field.classList.toggle('is-invalid', !valid);
                const error = document.querySelector(`[data-error-for="${field.id}"]`);
                error?.classList.toggle('is-visible', !valid);
                return valid;
            };

            const setCityOptions = (cities, selectedName = '') => {
                if (!citySelect) return;
                const options = cities.map((city) => {
                    const name = String(city?.name || city?.nombre || city?.city || '').trim();
                    if (!name) return '';
                    const selected = name === selectedName ? ' selected' : '';
                    return `<option value="${name.replace(/"/g, '&quot;')}"${selected}>${name}</option>`;
                }).filter(Boolean);

                citySelect.innerHTML = options.length
                    ? `<option value="">Selecciona ciudad</option>${options.join('')}`
                    : '<option value="">No hay ciudades disponibles</option>';
            };

            const loadCities = async (selectedName = '') => {
                if (!departmentSelect || !citySelect) return;
                const departmentId = departmentSelect.options[departmentSelect.selectedIndex]?.dataset.id;
                if (!departmentId) {
                    citySelect.innerHTML = '<option value="">Primero elige departamento</option>';
                    return;
                }

                citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';

                try {
                    const response = await fetch(`${departmentSelect.dataset.citiesUrl}/${encodeURIComponent(departmentId)}/cities`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const payload = await response.json();
                    setCityOptions(Array.isArray(payload.data) ? payload.data : [], selectedName);
                } catch (error) {
                    citySelect.innerHTML = '<option value="">No se pudieron cargar las ciudades</option>';
                }
            };

            validatable.forEach((field) => {
                field.addEventListener('input', () => validateField(field));
                field.addEventListener('blur', () => validateField(field));
            });

            firstNameInput?.addEventListener('input', syncFullName);
            lastNameInput?.addEventListener('input', syncFullName);
            syncFullName();

            departmentSelect?.addEventListener('change', async () => {
                validateField(departmentSelect);
                await loadCities();
            });

            citySelect?.addEventListener('change', () => {
                validateField(citySelect);
            });

            if (departmentSelect?.value) {
                loadCities(@json(old('shipping_city')));
            }

            document.querySelectorAll('[data-shipping-option]').forEach((input) => {
                input.addEventListener('change', () => {
                    document.querySelectorAll('[data-shipping-option]').forEach((item) => item.closest('.checkout-option')?.classList.remove('is-selected'));
                    input.closest('.checkout-option')?.classList.add('is-selected');
                    shippingPrice = Number(input.dataset.price || 0);
                    cartState();
                });
            });

            document.querySelectorAll('[data-payment-option]').forEach((input) => {
                input.addEventListener('change', () => {
                    document.querySelectorAll('[data-payment-option]').forEach((item) => item.closest('.checkout-option')?.classList.remove('is-selected'));
                    input.closest('.checkout-option')?.classList.add('is-selected');
                    paymentTypeInput.value = input.dataset.paymentType || 'anticipado';
                    paymentChannelInput.value = input.dataset.paymentChannel || '';
                });
            });

            document.querySelector('[data-apply-coupon]')?.addEventListener('click', () => {
                const code = couponInput.value.trim().toUpperCase();
                const subtotal = Array.from(document.querySelectorAll('[data-cart-item]')).reduce((sum, item) => {
                    if (item.hidden) return sum;
                    return sum + (Number(item.dataset.price || 0) * Number(item.dataset.quantity || 0));
                }, 0);

                discountAmount = code === 'M57SAVE10' ? subtotal * 0.1 : 0;
                couponMessage.textContent = discountAmount
                    ? 'Cupón aplicado. Descuento actualizado en tiempo real.'
                    : 'Cupón no válido. Usa M57SAVE10 para probar el flujo.';
                if (discountAmount) {
                    couponMessage.animate([
                        { transform: 'scale(1)', opacity: 1 },
                        { transform: 'scale(1.02)', opacity: 1 },
                        { transform: 'scale(1)', opacity: 1 }
                    ], { duration: 250, easing: 'ease-out' });
                }
                cartState();
            });

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

            cartList?.addEventListener('click', async (event) => {
                const item = event.target.closest('[data-cart-item]');
                if (!item) return;

                if (event.target.matches('[data-favorite-toggle]')) {
                    event.target.textContent = event.target.textContent.includes('favoritos') ? 'Guardado' : 'Mover a favoritos';
                    return;
                }

                if (event.target.matches('[data-cart-remove]')) {
                    item.classList.add('is-loading');
                    try {
                        await request(removeUrl.replace('__KEY__', item.dataset.itemKey), 'POST', new URLSearchParams({ _method: 'DELETE' }));
                        item.hidden = true;
                        item.remove();
                        cartState();
                    } finally {
                        item.classList.remove('is-loading');
                    }
                }

                if (event.target.matches('[data-cart-save]')) {
                    const qty = item.querySelector('[data-qty-input]');
                    item.classList.add('is-loading');
                    try {
                        await request(
                            qtyUrl.replace('__KEY__', item.dataset.itemKey),
                            'POST',
                            new URLSearchParams({ _method: 'PATCH', quantity: qty.value })
                        );
                        item.dataset.quantity = qty.value;
                        item.querySelector('[data-line-total]').textContent = money(Number(item.dataset.price || 0) * Number(qty.value || 0));
                        cartState();
                    } finally {
                        item.classList.remove('is-loading');
                    }
                }
            });

            document.querySelectorAll('.checkout-product-image img').forEach((img) => {
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

            document.querySelectorAll('.checkout-reveal').forEach((item) => {
                if (observer) observer.observe(item);
                else item.classList.add('is-visible');
            });

            document.getElementById('checkout-form')?.addEventListener('submit', (event) => {
                syncFullName();
                Array.from(validatable).forEach((field) => validateField(field));
            });

            cartState();
        })();
    </script>
@endsection
