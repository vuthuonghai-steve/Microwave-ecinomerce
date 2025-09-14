<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    public function index()
    {
        $rootCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->take(8)
            ->get(['id','name','slug']);
        $categoriesByParent = Category::where('is_active', true)->get(['id','name','slug','parent_id'])->groupBy('parent_id');

        // Best selling: delivered orders sum(order_items.quantity)
        $bestSelling = Product::query()
            ->with('brand:id,name,slug')
            ->select('products.*')
            ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('orders', function($join) {
                $join->on('orders.id', '=', 'order_items.order_id')
                     ->where('orders.status', 'delivered');
            })
            ->groupBy('products.id')
            ->orderByRaw('COALESCE(SUM(order_items.quantity),0) DESC')
            ->take(8)
            ->get();

        $latest = Product::with('brand:id,name,slug')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('rootCategories', 'categoriesByParent', 'bestSelling', 'latest'));
    }
}
