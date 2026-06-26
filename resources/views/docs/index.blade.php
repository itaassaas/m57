@extends('storefront.layout', ['title' => 'Docs M57', 'cartCount' => 0])

@section('content')
    <section style="padding:28px 0 48px;">
        <div class="stack-card">
            <div class="eyebrow">Documentacion viva</div>
            <h1 style="margin:8px 0 12px;">M57 + Hub</h1>
            <p>Esta pagina resume que existe hoy, donde esta el codigo y que sigue.</p>

            <div class="summary-list">
                <div><strong>Arquitectura</strong> · `docs/ARCHITECTURE.md`</div>
                <div><strong>Resumen operativo</strong> · `docs/README.md`</div>
                <div><strong>Swagger UI</strong> · <a href="{{ route('docs.swagger') }}">abrir</a></div>
                <div><strong>OpenAPI YAML</strong> · <a href="{{ route('docs.openapi') }}">ver archivo</a></div>
            </div>

            <div style="margin-top:20px;">
                <h2 style="margin-bottom:8px;">Que sigue</h2>
                <div class="summary-list">
                    <div>1. Shipping real por tienda y consolidado en checkout.</div>
                    <div>2. Sincronizar estados desde Hub hacia M57.</div>
                    <div>3. Reglas de pago por tienda y reaprovechar mejor checkouts web de Hub.</div>
                    <div>4. Busqueda instantanea, quick view y scroll infinito real.</div>
                    <div>5. Wishlist persistente y cuenta de cliente.</div>
                </div>
            </div>

            <div style="margin-top:20px;">
                <h2 style="margin-bottom:8px;">Archivos clave</h2>
                <div class="summary-list">
                    <div>`app/Http/Controllers/StorefrontController.php`</div>
                    <div>`app/Services/HubMarketplaceApi.php`</div>
                    <div>`app/Models/MarketplaceOrder*.php`</div>
                    <div>`resources/views/storefront/*`</div>
                </div>
            </div>
        </div>
    </section>
@endsection
