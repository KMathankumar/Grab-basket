<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Display public order tracking page.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Find the order with its product
        $order = Order::with('product')->findOrFail($id);

        // Pass order to public tracking view
        return view('public.order-tracking', compact('order'));
    }
}