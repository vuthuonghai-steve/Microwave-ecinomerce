@extends('admin.layout')
@section('title','Brands')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Brands</h1>
  <a class="bg-blue-600 text-white px-4 py-2 rounded" href="{{ route('admin.brands.create') }}">Create</a>
</div>
<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full">
    <thead class="bg-gray-100 text-left">
      <tr>
        <th class="p-2">ID</th>
        <th class="p-2">Name</th>
        <th class="p-2">Slug</th>
        <th class="p-2">Active</th>
        <th class="p-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($brands as $b)
        <tr class="border-t">
          <td class="p-2">{{ $b->id }}</td>
          <td class="p-2">{{ $b->name }}</td>
          <td class="p-2">{{ $b->slug }}</td>
          <td class="p-2">{{ $b->is_active ? 'Yes' : 'No' }}</td>
          <td class="p-2 whitespace-nowrap">
            @can('update', $b)
              <a class="text-blue-600 mr-2" href="{{ route('admin.brands.edit', $b->id) }}">Edit</a>
            @endcan
            @can('delete', $b)
              <form action="{{ route('admin.brands.destroy', $b->id) }}" method="post" class="inline" data-confirm="Bạn có chắc muốn xóa thương hiệu này?">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600" aria-label="Delete brand" title="Delete">Delete</button>
              </form>
            @endcan
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $brands->links() }}</div>
@endsection
