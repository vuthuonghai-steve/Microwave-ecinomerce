<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
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

        if ($catSlug = $request->string('category')->toString()) {
            $query->whereHas('category', function (Builder $qB) use ($catSlug) {
                $qB->where('slug', $catSlug)->where('is_active', true);
            });
        }

        if ($brandSlug = $request->string('brand')->toString()) {
            $query->whereHas('brand', function (Builder $qB) use ($brandSlug) {
                $qB->where('slug', $brandSlug)->where('is_active', true);
            });
        }

        // Price range on final price = COALESCE(sale_price, price)
        if ($request->filled('min_price')) {
            $min = (float) $request->input('min_price');
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$min]);
        }
        if ($request->filled('max_price')) {
            $max = (float) $request->input('max_price');
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$max]);
        }

        if ($request->boolean('inverter', null) !== null) {
            $query->where('inverter', $request->boolean('inverter'));
        }
        if ($request->boolean('has_grill', null) !== null) {
            $query->where('has_grill', $request->boolean('has_grill'));
        }

        // rating filter omitted until reviews/avg available

        $sort = $request->string('sort')->toString();
        if ($sort === 'price_asc') {
            $query->orderByRaw('COALESCE(sale_price, price) asc');
        } elseif ($sort === 'price_desc') {
            $query->orderByRaw('COALESCE(sale_price, price) desc');
        } else { // latest default
            $query->latest();
        }

        $products = $query->paginate(12)->appends($request->query());

        return ProductResource::collection($products);
    }

    public function show(string $slug)
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with(['brand:id,name,slug', 'category:id,name,slug', 'stock:product_id,stock_on_hand,stock_reserved'])
            ->firstOrFail();

        return new ProductResource($product);
    }
}

