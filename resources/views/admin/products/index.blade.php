@extends('admin.layout')
@section('title','Products')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Products</h1>
  <a class="bg-blue-600 text-white px-4 py-2 rounded" href="{{ route('admin.products.create') }}">Create</a>
  </div>

  <form method="get" class="bg-white rounded shadow p-4 mb-4 grid md:grid-cols-6 gap-3">
    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name/slug" class="border rounded px-3 py-2 md:col-span-2" />
    <select name="brand_id" class="border rounded px-3 py-2">
      <option value="">Brand</option>
      @foreach($brands as $b)
        <option value="{{ $b->id }}" @selected(($brandId ?? null)==$b->id)>{{ $b->name }}</option>
      @endforeach
    </select>
    <select name="category_id" class="border rounded px-3 py-2">
      <option value="">Category</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected(($categoryId ?? null)==$c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
    <select name="active" class="border rounded px-3 py-2">
      <option value="">Status</option>
      <option value="1" @selected(($active ?? '')==='1')>Active</option>
      <option value="0" @selected(($active ?? '')==='0')>Inactive</option>
    </select>
    <select name="sort" class="border rounded px-3 py-2">
      <option value="latest" @selected(($sort ?? '')==='latest')>Latest</option>
      <option value="price_asc" @selected(($sort ?? '')==='price_asc')>Price ↑</option>
      <option value="price_desc" @selected(($sort ?? '')==='price_desc')>Price ↓</option>
      <option value="best_selling" @selected(($sort ?? '')==='best_selling')>Best selling</option>
    </select>
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
  </form>
  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="p-2">ID</th>
          <th class="p-2">Name</th>
          <th class="p-2">Brand</th>
          <th class="p-2">Category</th>
          <th class="p-2">Price</th>
          <th class="p-2">Active</th>
          <th class="p-2">Sold</th>
          <th class="p-2">Stock</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($products as $p)
          <tr class="border-t">
            <td class="p-2">{{ $p->id }}</td>
            <td class="p-2">{{ $p->name }}</td>
            <td class="p-2">{{ $p->brand->name ?? '' }}</td>
            <td class="p-2">{{ $p->category->name ?? '' }}</td>
            <td class="p-2">{{ number_format($p->sale_price ?? $p->price,0,',','.') }}₫</td>
            <td class="p-2">
              @if($p->is_active)
                <span class="inline-block text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Active</span>
              @else
                <span class="inline-block text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">Inactive</span>
              @endif
            </td>
            <td class="p-2">{{ (int) ($p->sold_qty ?? 0) }}</td>
            <td class="p-2">{{ $p->stock ? max(0, ($p->stock->stock_on_hand - $p->stock->stock_reserved)) : 0 }}</td>
            <td class="p-2 whitespace-nowrap">
              @can('update', $p)
                <a class="text-blue-600 mr-2" href="{{ route('admin.products.edit', $p->id) }}">Edit</a>
              @endcan
              @can('delete', $p)
                <form action="{{ route('admin.products.destroy', $p->id) }}" method="post" class="inline" data-confirm="Bạn có chắc muốn xóa sản phẩm này?">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-red-600" aria-label="Delete product" title="Delete">Delete</button>
                </form>
              @endcan
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-4">{{ $products->links() }}</div>
@endsection
