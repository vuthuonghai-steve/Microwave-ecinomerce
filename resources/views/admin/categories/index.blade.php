@extends('admin.layout')
@section('title','Categories')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Categories</h1>
  <a class="bg-blue-600 text-white px-4 py-2 rounded" href="{{ route('admin.categories.create') }}">Create</a>
</div>
<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full">
    <thead class="bg-gray-100 text-left">
      <tr>
        <th class="p-2">ID</th>
        <th class="p-2">Name</th>
        <th class="p-2">Slug</th>
        <th class="p-2">Parent</th>
        <th class="p-2">Active</th>
        <th class="p-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($categories as $c)
        <tr class="border-t">
          <td class="p-2">{{ $c->id }}</td>
          <td class="p-2">{{ $c->name }}</td>
          <td class="p-2">{{ $c->slug }}</td>
          <td class="p-2">{{ $c->parent->name ?? '-' }}</td>
          <td class="p-2">{{ $c->is_active ? 'Yes' : 'No' }}</td>
          <td class="p-2 whitespace-nowrap">
            @can('update', $c)
              <a class="text-blue-600 mr-2" href="{{ route('admin.categories.edit', $c->id) }}">Edit</a>
            @endcan
            @can('delete', $c)
              <form action="{{ route('admin.categories.destroy', $c->id) }}" method="post" class="inline" data-confirm="Bạn có chắc muốn xóa danh mục này?">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600" aria-label="Delete category" title="Delete">Delete</button>
              </form>
            @endcan
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
