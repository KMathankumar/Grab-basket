<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SellerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $sellerId = Auth::guard('seller')->id();

        // Base query: Orders for products owned by this seller
        $orderQuery = Order::whereHas('product', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->with('product');

        // Apply filters
        if ($request->filled('status')) {
            $orderQuery->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $orderQuery->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $orderQuery->whereDate('created_at', '<=', $request->end_date);
        }

        // Stats
        $totalProducts = Product::where('seller_id', $sellerId)->count();
        $totalOrders = $orderQuery->count();
        $totalSales = $orderQuery->sum('total_price');
        $sellerRevenue = $totalSales;

        // Recent Orders
        $recentOrders = $orderQuery->latest()->paginate(5);

        // Monthly Sales Data for Chart (Last 6 Months)
        $monthlySalesQuery = Order::whereHas('product', function ($q) use ($sellerId) {
                $q->where('seller_id', $sellerId);
            })
            ->whereBetween('created_at', [now()->subMonths(5)->startOfMonth(), now()->endOfMonth()])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_price) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Convert to map: 'Y-m' => total
        $monthlySales = $monthlySalesQuery->mapWithKeys(function ($item) {
            $key = sprintf('%d-%02d', $item->year, $item->month);
            return [$key => $item->total];
        });

        // Fill last 6 months with 0 if no data
        $chartData = [];
        $labels = [];
        $current = now()->subMonths(5);

        for ($i = 0; $i < 6; $i++) {
            $key = $current->format('Y-m');
            $labels[] = $current->format('M');
            $chartData[] = round($monthlySales[$key] ?? 0, 2);
            $current->addMonth();
        }

        return view('seller.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalSales',
            'sellerRevenue',
            'recentOrders',
            'chartData',
            'labels'
        ));
    }
}