<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceOrder extends Model
{
    protected $fillable = [
        'batch_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_locality',
        'payment_type',
        'payment_channel',
        'notes',
        'subtotal_amount',
        'total_amount',
        'status',
        'hub_response',
    ];

    protected $casts = [
        'subtotal_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'hub_response' => 'array',
    ];

    public function stores(): HasMany
    {
        return $this->hasMany(MarketplaceOrderStore::class);
    }
}
