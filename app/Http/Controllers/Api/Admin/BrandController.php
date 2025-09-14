<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Http\Requests\Admin\BrandStoreRequest;
use App\Http\Requests\Admin\BrandUpdateRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        return Brand::query()->latest()->paginate(20)->appends($request->query());
    }

    public function show(int $id)
    {
        return Brand::findOrFail($id);
    }

    public function store(BrandStoreRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) $data['slug'] = $this->uniqueSlug($data['name']);
        $brand = Brand::create($data);
        return response()->json($brand, 201);
    }

    public function update(BrandUpdateRequest $request, int $id)
    {
        $brand = Brand::findOrFail($id);
        $data = $request->validated();
        $brand->update($data);
        return $brand;
    }

    public function destroy(int $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        return response()->noContent();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name); $slug=$base; $i=1;
        while (Brand::where('slug',$slug)->exists()) { $slug = $base.'-'.$i++; }
        return $slug;
    }
}
