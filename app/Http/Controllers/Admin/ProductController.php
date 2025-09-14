<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $q = request()->string('q')->toString();
        $brandId = request()->integer('brand_id') ?: null;
        $categoryId = request()->integer('category_id') ?: null;
        $active = request()->has('active') ? request()->input('active') : null; // '1','0' or null

        $query = Product::query()->with(['brand','category'])
            ->withSum(['orderItems as sold_qty' => function($q){
                $q->join('orders','orders.id','=','order_items.order_id')
                  ->where('orders.status','delivered');
            }], 'quantity')
            ->with(['stock' => function($q){ $q->select('product_id','stock_on_hand','stock_reserved'); }]);
        if ($q) {
            $query->where(function($b) use ($q){
                $b->where('name','like',"%$q%")
                  ->orWhere('slug','like',"%$q%");
            });
        }
        if ($brandId) { $query->where('brand_id', $brandId); }
        if ($categoryId) { $query->where('category_id', $categoryId); }

        if ($active !== null && $active !== '') {
            $query->where('is_active', (bool) ((int) $active));
        }

        $sort = request()->string('sort')->toString();
        if ($sort === 'price_asc') {
            $query->orderByRaw('COALESCE(sale_price, price) asc');
        } elseif ($sort === 'price_desc') {
            $query->orderByRaw('COALESCE(sale_price, price) desc');
        } elseif ($sort === 'best_selling') {
            $query->select('products.*')
                ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
                ->leftJoin('orders', function($join) {
                    $join->on('orders.id', '=', 'order_items.order_id')
                         ->where('orders.status', 'delivered');
                })
                ->groupBy('products.id')
                ->orderByRaw('COALESCE(SUM(order_items.quantity),0) DESC');
        } else {
            $query->latest();
        }

        $products = $query->paginate(20)->appends(request()->query());

        $brands = \App\Models\Brand::orderBy('name')->get(['id','name']);
        $categories = \App\Models\Category::orderBy('name')->get(['id','name']);
        return view('admin.products.index', compact('products','brands','categories','q','brandId','categoryId','sort','active'));
    }

    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('brands','categories'));
    }

    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']).'-'.\Illuminate\Support\Str::random(4);
        }
        Product::create($data);
        return redirect()->route('admin.products.index')->with('status', 'Product created');
    }

    public function edit(int $id)
    {
        $product = Product::findOrFail($id);
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.products.edit', compact('product','brands','categories'));
    }

    public function update(ProductUpdateRequest $request, int $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->validated());
        return redirect()->route('admin.products.index')->with('status', 'Product updated');
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products.index')->with('status', 'Product deleted');
    }
}
