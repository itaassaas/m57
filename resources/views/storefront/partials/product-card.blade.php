<article class="card {{ $cardClass ?? '' }}" @if(!empty($product['colors'])) data-colors="{{ implode('|', $product['colors']) }}" @endif @if(!empty($product['sizes'])) data-sizes="{{ implode('|', $product['sizes']) }}" @endif data-brand="{{ $product['owner']['name'] ?? 'Hub' }}" data-price="{{ $product['price'] }}" data-sale="{{ !empty($product['is_featured']) ? 1 : 0 }}">
    <a href="{{ route('products.show', $product['id']) }}" class="card-media">
        @if(!empty($chip))
            <span class="sale-chip">{{ $chip }}</span>
        @endif
        <span class="wish">♡</span>
        <img class="primary" src="{{ $product['image'] }}" alt="{{ $product['name'] }}" loading="lazy">
        @if(!empty($product['secondary_image']))
            <img class="secondary" src="{{ $product['secondary_image'] }}" alt="{{ $product['name'] }}" loading="lazy">
        @endif
    </a>
    <div class="card-body">
        @if(!empty($showMeta))
            <div class="eyebrow">{{ $product['owner']['name'] ?? 'Hub' }} · {{ $product['category_name'] ?? 'Catalogo' }}</div>
        @else
            <div class="eyebrow">{{ $product['owner']['name'] ?? 'Hub' }}</div>
        @endif
        <a href="{{ route('products.show', $product['id']) }}" class="title">{{ $product['name'] }}</a>
        <div class="price-row">
            <span class="price">${{ number_format($product['price'], 0, ',', '.') }}</span>
            @if(!empty($showOldPrice))
                <span class="old-price">${{ number_format(($product['old_price'] ?? ($product['price'] * 1.18)), 0, ',', '.') }}</span>
            @endif
        </div>
        @if(!empty($withActions))
            @if(!empty($showMeta))
                <div class="rating-row">
                    <span>★ 4.8</span>
                    <span>·</span>
                    <span>SKU {{ ($product['sku'] ?? '') ?: 'N/A' }}</span>
                </div>
            @endif
            <div class="card-actions">
                <a class="quick-link" href="{{ route('products.show', $product['id']) }}">Ver</a>
                <form method="post" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="add-btn">Agregar</button>
                </form>
            </div>
        @endif
    </div>
</article>
