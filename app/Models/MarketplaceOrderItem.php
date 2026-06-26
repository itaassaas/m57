<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceOrderItem extends Model
{
    protected $fillable = [
        'marketplace_order_store_id',
        'product_id',
        'variation_id',
        'product_name',
        'product_sku',
        'variation_name',
        'owner_user_id',
        'quantity',
        'unit_price',
        'line_total',
        'snapshot',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'snapshot' => 'array',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrderStore::class, 'marketplace_order_store_id');
    }
}
