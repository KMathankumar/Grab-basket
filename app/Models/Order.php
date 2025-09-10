<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'product_id',
        'seller_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'total_price',
        'quantity',
        'status',
    ];

    // ✅ Add missing use statements via FQCN or import
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get badge color for status.
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Pending' => 'warning',
            'Shipped', 'Delivered' => 'success',
            'Cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get simplified status label.
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'Shipped', 'Delivered' => 'Completed',
            'Pending' => 'Incomplete',
            'Cancelled' => 'Cancelled',
            default => 'Pending',
        };
    }
}