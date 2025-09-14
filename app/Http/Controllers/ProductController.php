<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->where('is_active', true);

        if ($q = $request->string('q')->toString()) {
            $query->where(function (Builder $qB) use ($q) {
                $qB->where('name', 'like', "%$q%")
                   ->orWhere('description', 'like', "%$q%");
            });
        }
        if ($cat = $request->string('category')->toString()) {
            $query->whereHas('category', fn (Builder $b) => $b->where('slug', $cat));
        }
        if ($brand = $request->string('brand')->toString()) {
            $query->whereHas('brand', fn (Builder $b) => $b->where('slug', $brand));
        }
        if ($request->filled('min_price')) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [(float)$request->input('min_price')]);
        }
        if ($request->filled('max_price')) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [(float)$request->input('max_price')]);
        }
        if (($inverter = $request->input('inverter')) !== null) {
            $query->where('inverter', filter_var($inverter, FILTER_VALIDATE_BOOLEAN));
        }
        if (($hasGrill = $request->input('has_grill')) !== null) {
            $query->where('has_grill', filter_var($hasGrill, FILTER_VALIDATE_BOOLEAN));
        }
        if (($childLock = $request->input('child_lock')) !== null) {
            $query->where('child_lock', filter_var($childLock, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('min_capacity')) {
            $query->where('capacity_liters', '>=', (int) $request->input('min_capacity'));
        }
        if ($request->filled('max_capacity')) {
            $query->where('capacity_liters', '<=', (int) $request->input('max_capacity'));
        }
        if ($request->filled('min_power')) {
            $query->where('power_watt', '>=', (int) $request->input('min_power'));
        }
        if ($request->filled('max_power')) {
            $query->where('power_watt', '<=', (int) $request->input('max_power'));
        }
        if ($request->filled('min_energy_rating')) {
            $query->where('energy_rating', '>=', (int) $request->input('min_energy_rating'));
        }

        $sort = $request->string('sort')->toString();
        if ($sort === 'price_asc') $query->orderByRaw('COALESCE(sale_price, price) asc');
        elseif ($sort === 'price_desc') $query->orderByRaw('COALESCE(sale_price, price) desc');
        elseif ($sort === 'best_selling') {
            $query->select('products.*')
                ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('orders', function($join) {
                    $join->on('orders.id', '=', 'order_items.order_id')
                         ->where('orders.status', 'delivered');
                })
                ->groupBy('products.id')
                ->orderByRaw('COALESCE(SUM(order_items.quantity),0) DESC');
        } else $query->latest();

        $products = $query->paginate(12)->appends($request->query());
        $brands = Brand::where('is_active', true)->orderBy('name')->get(['id','name','slug']);
        // Build category tree
        $rootCategories = Category::whereNull('parent_id')->where('is_active', true)->orderBy('name')->get(['id','name','slug']);
        $categoriesByParent = Category::where('is_active', true)->get(['id','name','slug','parent_id'])->groupBy('parent_id');

        return view('products.index', [
            'products' => $products,
            'brands' => $brands,
            'rootCategories' => $rootCategories,
            'categoriesByParent' => $categoriesByParent,
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['brand:id,name,slug', 'category:id,name,slug', 'stock'])
            ->firstOrFail();

        return view('products.show', compact('product'));
    }
}
