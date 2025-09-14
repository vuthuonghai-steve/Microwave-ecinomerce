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
        $q = request()->string('q')->toString();
        $query = Category::query()->with('parent')
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
        $categories = $query->latest()->paginate(20)->appends(request()->query());
        return view('admin.categories.index', compact('categories','q'));
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
