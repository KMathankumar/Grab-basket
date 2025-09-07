<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Seller extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'shop_name',
    ];

    // ✅ Make sure password is hashed
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Allow Laravel to find the password field
    public function getAuthPassword()
    {
        return $this->password;
    }
}