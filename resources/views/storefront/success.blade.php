@extends('storefront.layout', ['title' => 'Orden creada', 'cartCount' => $cartCount])

@section('content')
    <section style="padding:28px 0 48px;">
        <div class="stack-card">
            <div class="eyebrow">Batch {{ $order->batch_code }}</div>
            <h1 style="margin:8px 0 12px;">Orden creada en Hub</h1>
            <p class="mini">Separamos el pedido por tienda dueña y ya quedaron creadas las ordenes individuales.</p>
            <div class="success-list">
                @foreach($order->stores as $store)
                    <article class="success-card">
                        <div style="width:72px;height:72px;border-radius:18px;background:#111;color:#fff;display:grid;place-items:center;font-weight:800;">{{ $loop->iteration }}</div>
                        <div>
                            <strong>{{ $store->owner_name }}</strong>
                            <div class="mini">Orden Hub {{ $store->hub_order_number }}</div>
                            <div class="mini">Estado {{ $store->status }} · Total ${{ number_format($store->total_amount, 0, ',', '.') }}</div>
                            <div class="mini">{{ $store->items->count() }} item(s)</div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="cta-row">
                <a class="button" href="{{ route('home') }}">Volver al catalogo</a>
                <a class="button secondary" href="{{ route('cart.show') }}">Ver carrito</a>
            </div>
        </div>
    </section>
@endsection
