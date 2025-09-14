<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandStoreRequest;
use App\Http\Requests\Admin\BrandUpdateRequest;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        $q = request()->string('q')->toString();
        $query = Brand::query()
            ->withCount('products')
            ->withSum(['products as sold_qty' => function($q){
                $q->join('order_items','order_items.product_id','=','products.id')
                  ->join('orders','orders.id','=','order_items.order_id')
                  ->where('orders.status','delivered');
            }], 'order_items.quantity');
        if ($q) {
            $query->where(function($b) use ($q){
                $b->where('name','like',"%$q%")
                  ->orWhere('slug','like',"%$q%");
            });
        }
        $brands = $query->latest()->paginate(20)->appends(request()->query());
        return view('admin.brands.index', compact('brands','q'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(BrandStoreRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        Brand::create($data);
        return redirect()->route('admin.brands.index')->with('status', 'Brand created');
    }

    public function edit(int $id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(BrandUpdateRequest $request, int $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->update($request->validated());
        return redirect()->route('admin.brands.index')->with('status', 'Brand updated');
    }

    public function destroy(int $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('status', 'Brand deleted');
    }
}
