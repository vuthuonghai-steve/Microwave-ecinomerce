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
        $products = Product::with(['brand','category'])->latest()->paginate(20);
        return view('admin.products.index', compact('products'));
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

