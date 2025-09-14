<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::orderBy('name')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }
        Category::create($data);
        return redirect()->route('admin.categories.index')->with('status', 'Category created');
    }

    public function edit(int $id)
    {
        $category = Category::findOrFail($id);
        $parents = Category::where('id','<>',$id)->orderBy('name')->get();
        return view('admin.categories.edit', compact('category','parents'));
    }

    public function update(CategoryUpdateRequest $request, int $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());
        return redirect()->route('admin.categories.index')->with('status', 'Category updated');
    }

    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.categories.index')->with('status', 'Category deleted');
    }
}

