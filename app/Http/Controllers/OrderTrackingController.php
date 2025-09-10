<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function show($id)
    {
        $order = Order::with('product')->findOrFail($id);
        return view('public.order-tracking', compact('order'));
    }
}