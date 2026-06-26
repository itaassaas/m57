@foreach($products as $product)
    @include('storefront.partials.product-card', [
        'product' => $product,
        'chip' => $chip ?? null,
        'showOldPrice' => $showOldPrice ?? false,
        'withActions' => $withActions ?? false,
        'showMeta' => $showMeta ?? false,
    ])
@endforeach
