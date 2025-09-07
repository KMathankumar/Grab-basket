<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'seller_id',
        'category_id',
    ];

    // ✅ A product belongs to a seller
    public function seller()
    {
        return $this->belongsTo(\App\Models\Seller::class);
    }

    // ✅ A product has many orders
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }
      public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function images()
    {
        return $this->hasMany(\App\Models\ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(\App\Models\ProductVariant::class);
    }

    // Optional: Helper to get primary image
    public function primaryImage()
    {
        return $this->images()->where('is_primary', true)->first() ??
               $this->images()->first();
    }

}