<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OrderItem;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $outlets = Outlet::all();
        $selectedOutletId = $request->input('outlet_id');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now();

        $query = Order::where('status', 'completed')
                      ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        if ($selectedOutletId) {
            $query->whereHas('orderItems.product.category', function ($q) use ($selectedOutletId) {
                $q->where('outlet_id', $selectedOutletId);
            });
        }
        
        $orders = $query->with('orderItems.product')->latest()->get();
        $totalRevenue = $orders->sum('total_price');
        $totalOrders = $orders->count();

        $bestSellerQuery = OrderItem::whereHas('order', function($q) use ($startDate, $endDate) {
            $q->where('status', 'completed')->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        });

        if ($selectedOutletId) {
            $bestSellerQuery->whereHas('product.category', function($q) use ($selectedOutletId){
                $q->where('outlet_id', $selectedOutletId);
            });
        }
        
        $bestSellingProducts = $bestSellerQuery
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product') 
            ->limit(5) 
            ->get();

        $topCustomersQuery = Order::where('status', 'completed')
                                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);

        if ($selectedOutletId) {
            $topCustomersQuery->whereHas('orderItems.product.category', function ($q) use ($selectedOutletId) {
                $q->where('outlet_id', $selectedOutletId);
            });
        }

        $topCustomers = $topCustomersQuery
            ->select('customer_name', DB::raw('COUNT(*) as total_orders'))
            ->groupBy('customer_name')
            ->orderByDesc('total_orders')
            ->limit(5) 
            ->get();

        return view('admin.sales_report.index', compact(
            'orders', 
            'totalRevenue', 
            'totalOrders', 
            'startDate', 
            'endDate',
            'outlets',
            'selectedOutletId',
            'bestSellingProducts', 
            'topCustomers'        
        ));
    }
}