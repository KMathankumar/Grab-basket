<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    /**
     * Display a listing of the seller's orders.
     */
    public function index()
    {
        // Get orders where the product belongs to this seller
        $query = Order::whereHas('product', function ($query) {
            $query->where('seller_id', Auth::guard('seller')->id());
        })->with('product');

        // Apply filters
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('start_date')) {
            $query->whereDate('created_at', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $query->whereDate('created_at', '<=', request('end_date'));
        }

        $orders = $query->latest()->paginate(10);

        return view('seller.orders.index', compact('orders'));
    }

    /**
     * Show the order details.
     */
    public function show($id)
    {
        $order = Order::whereHas('product', function ($query) {
            $query->where('seller_id', Auth::guard('seller')->id());
        })->with('product')
          ->findOrFail($id);

        return view('seller.orders.show', compact('order'));
    }

    /**
     * Update the order status.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Shipped,Delivered,Cancelled',
        ]);

        $order = Order::whereHas('product', function ($query) {
            $query->where('seller_id', Auth::guard('seller')->id());
        })->findOrFail($id);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->route('seller.orders.show', $order->id)
            ->with('success', 'Order status updated successfully!');
    }

    /**
     * Export orders to CSV.
     */
    public function export()
    {
        $orders = Order::whereHas('product', function ($query) {
            $query->where('seller_id', Auth::guard('seller')->id());
        })->with('product')
          ->get();

        $filename = "my-orders-" . now()->format('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');

        // Header
        fputcsv($handle, [
            'Order ID', 'Product', 'Customer', 'Email', 'Phone', 
            'Quantity', 'Total', 'Status', 'Date'
        ]);

        // Data
        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->id,
                $order->product?->name ?? 'Deleted Product',
                $order->customer_name,
                $order->customer_email,
                $order->customer_phone,
                $order->quantity,
                $order->total_price,
                $order->status,
                $order->created_at->format('M d, Y'),
            ]);
        }

        fclose($handle);

        return response()->stream(
            function() use ($handle) {},
            200,
            [
                "Content-Type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
            ]
        );
    }
}