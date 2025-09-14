<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $products = Product::query()->with(['brand:id,name,slug', 'category:id,name,slug'])
            ->latest()->paginate(15)->appends($request->query());
        return ProductResource::collection($products);
    }

    public function show(int $id)
    {
        $product = Product::with(['brand:id,name,slug', 'category:id,name,slug'])->findOrFail($id);
        return new ProductResource($product);
    }

    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['name']);
        }

        $product = Product::create($data);
        return (new ProductResource($product->load(['brand','category'])))->response()->setStatusCode(201);
    }

    public function update(ProductUpdateRequest $request, int $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();

        if (array_key_exists('name', $data) && !array_key_exists('slug', $data)) {
            // keep current slug unless provided explicitly
        }

        $product->update($data);
        return new ProductResource($product->load(['brand','category']));
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->noContent();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
