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
              <div class="inline-flex">
              {{-- Nút edit --}}
              @can('update', $p)
                <a href="{{ route('admin.products.edit', $p->id) }}"
                  class="-ml-px border border-gray-200 px-3 py-2 text-gray-700 transition-colors 
                          hover:bg-gray-50 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-blue-500 
                          focus:ring-offset-2 focus:ring-offset-white focus:outline-none">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 
                            19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 
                            4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                  </svg>
                </a>
              @endcan

              {{-- Nút delete --}}
              @can('delete', $p)
                <form action="{{ route('admin.products.destroy', $p->id) }}" method="post" class="inline -ml-px"
                      data-confirm="Bạn có chắc muốn xóa sản phẩm này?">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                    class="rounded-r-sm border border-gray-200 px-3 py-2 text-gray-700 transition-colors 
                          hover:bg-gray-50 hover:text-red-600 focus:z-10 focus:ring-2 focus:ring-blue-500 
                          focus:ring-offset-2 focus:ring-offset-white focus:outline-none"
                    aria-label="Delete product" title="Delete">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="size-5">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 
                              1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 
                              1-2.244 2.077H8.084a2.25 2.25 0 0 
                              1-2.244-2.077L4.772 5.79m14.456 
                              0a48.108 48.108 0 0 0-3.478-.397m-12 
                              .562c.34-.059.68-.114 
                              1.022-.165m0 0a48.11 48.11 0 0 
                              1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 
                              51.964 0 0 0-3.32 0c-1.18.037-2.09 
                              1.022-2.09 2.201v.916m7.5 0a48.667 
                              48.667 0 0 0-7.5 0"/>
                    </svg>
                  </button>
                </form>
              @endcan
            </div>
          </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="mt-4">{{ $products->links() }}</div>
@endsection
