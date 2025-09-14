@extends('admin.layout')
@section('title','Products')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Products</h1>
  <a class="bg-blue-600 text-white px-4 py-2 rounded" href="{{ route('admin.products.create') }}">Create</a>
  </div>
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
            <td class="p-2">{{ $p->is_active ? 'Yes' : 'No' }}</td>
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
