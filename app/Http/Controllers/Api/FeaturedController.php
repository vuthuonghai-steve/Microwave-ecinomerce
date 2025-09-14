<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class FeaturedController extends Controller
{
    public function index()
    {
        $payload = Cache::remember('featured_homepage', 300, function () {
            $categories = Category::whereNull('parent_id')->where('is_active', true)
                ->orderBy('name')->take(8)->get(['id','name','slug']);

            $best = Product::query()
                ->select('products.id','products.name','products.slug','products.price','products.sale_price','products.thumbnail')
                ->leftJoin('order_items','order_items.product_id','=','products.id')
                ->leftJoin('orders', function($join){
                    $join->on('orders.id','=','order_items.order_id')->where('orders.status','delivered');
                })
                ->groupBy('products.id','products.name','products.slug','products.price','products.sale_price','products.thumbnail')
                ->orderByRaw('COALESCE(SUM(order_items.quantity),0) DESC')
                ->take(8)->get();

            $latest = Product::query()->where('is_active', true)
                ->orderByDesc('id')
                ->take(8)
                ->get(['id','name','slug','price','sale_price','thumbnail']);

            return [
                'categories' => $categories,
                'best_selling' => $best,
                'latest' => $latest,
            ];
        });

        return response()->json($payload);
    }
}
