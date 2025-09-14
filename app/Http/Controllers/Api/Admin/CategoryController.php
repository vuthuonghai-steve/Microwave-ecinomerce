<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $cats = Category::query()->latest()->paginate(20)->appends($request->query());
        return CategoryResource::collection($cats);
    }

    public function show(int $id)
    {
        return new CategoryResource(Category::findOrFail($id));
    }

    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) $data['slug'] = $this->uniqueSlug($data['name']);
        $cat = Category::create($data);
        return (new CategoryResource($cat))->response()->setStatusCode(201);
    }

    public function update(CategoryUpdateRequest $request, int $id)
    {
        $cat = Category::findOrFail($id);
        $data = $request->validated();
        $cat->update($data);
        return new CategoryResource($cat);
    }

    public function destroy(int $id)
    {
        $cat = Category::findOrFail($id);
        $cat->delete();
        return response()->noContent();
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name); $slug = $base; $i=1;
        while (Category::where('slug',$slug)->exists()) { $slug = $base.'-'.$i++; }
        return $slug;
    }
}
