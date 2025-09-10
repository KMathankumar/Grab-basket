<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class SellerOrderController extends Controller
{
    public function show($id)
    {
        $order = Order::whereHas('product', function ($q) {
            $q->where('seller_id', Auth::guard('seller')->id());
        })->with('product')
          ->findOrFail($id);

        return view('seller.orders.show', compact('order'));
    }

    public function index()
    {
        $query = Order::whereHas('product', function ($q) {
            $q->where('seller_id', Auth::guard('seller')->id());
        })->with('product');

        if (request('search')) {
            $search = request('search');
            $query->where('id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
        }

        if (request('status')) {
            $status = request('status');
            if ($status === 'Completed') {
                $query->whereIn('status', ['Shipped', 'Delivered']);
            } elseif ($status === 'Incomplete') {
                $query->where('status', 'Pending');
            } elseif ($status === 'Cancelled') {
                $query->where('status', 'Cancelled');
            }
        }

        if (request('start_date')) {
            $query->whereDate('created_at', '>=', request('start_date'));
        }
        if (request('end_date')) {
            $query->whereDate('created_at', '<=', request('end_date'));
        }

        $orders = $query->latest()->paginate(10);

        return view('seller.orders', compact('orders'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Shipped,Delivered,Cancelled',
        ]);

        $order = Order::whereHas('product', function ($q) {
            $q->where('seller_id', Auth::guard('seller')->id());
        })->findOrFail($id);

        $order->update(['status' => $request->status]);

        return redirect()->route('seller.orders.show', $order->id)
            ->with('success', 'Order status updated successfully!');
    }

    public function export()
    {
        $orders = Order::whereHas('product', function ($q) {
            $q->where('seller_id', Auth::guard('seller')->id());
        })->with('product')
          ->get();

        $filename = "my-orders-" . now()->format('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');

        fputcsv($handle, ['Order ID', 'Product', 'Customer', 'Email', 'Phone', 'Quantity', 'Total', 'Status', 'Date']);

        foreach ($orders as $order) {
            $statusLabel = in_array($order->status, ['Shipped', 'Delivered']) 
                ? 'Completed' 
                : ($order->status == 'Pending' ? 'Incomplete' : $order->status);

            fputcsv($handle, [
                $order->id,
                $order->product?->name ?? 'Deleted Product',
                $order->customer_name,
                $order->customer_email,
                $order->customer_phone,
                $order->quantity,
                $order->total_price,
                $statusLabel,
                $order->created_at->format('M d, Y'),
            ]);
        }

        fclose($handle);

        return response()->stream(
            function () use ($handle) {},
            200,
            [
                "Content-Type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
            ]
        );
    }
}