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

        $sort = $request->string('sort')->toString();
        if ($sort === 'price_asc') $query->orderByRaw('COALESCE(sale_price, price) asc');
        elseif ($sort === 'price_desc') $query->orderByRaw('COALESCE(sale_price, price) desc');
        else $query->latest();

        $products = $query->paginate(12)->appends($request->query());
        $brands = Brand::where('is_active', true)->orderBy('name')->get(['name','slug']);
        $categories = Category::where('is_active', true)->orderBy('name')->get(['name','slug']);

        return view('products.index', compact('products', 'brands', 'categories'));
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

