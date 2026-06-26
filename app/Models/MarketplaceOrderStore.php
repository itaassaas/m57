<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceOrderStore extends Model
{
    protected $fillable = [
        'marketplace_order_id',
        'owner_user_id',
        'owner_name',
        'hub_order_id',
        'hub_order_number',
        'status',
        'subtotal_amount',
        'total_amount',
        'hub_payload',
    ];

    protected $casts = [
        'subtotal_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'hub_payload' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(MarketplaceOrder::class, 'marketplace_order_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MarketplaceOrderItem::class);
    }
}
