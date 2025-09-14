@extends('admin.layout')
@section('title','Edit Brand')
@section('content')
<h1 class="text-xl font-semibold mb-4">Edit Brand</h1>
<form method="post" action="{{ route('admin.brands.update', $brand->id) }}" class="bg-white p-4 rounded shadow grid grid-cols-1 gap-4">
  @csrf
  @method('PUT')
  <div>
    <label class="block text-sm">Name</label>
    <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $brand->name) }}"/>
  </div>
  <div>
    <label class="block text-sm">Slug</label>
    <input name="slug" class="w-full border rounded px-3 py-2" value="{{ old('slug', $brand->slug) }}"/>
  </div>
  <div>
    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $brand->is_active))> Active</label>
  </div>
  <div class="flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
  </div>
</form>

@can('delete', $brand)
<form method="post" action="{{ route('admin.brands.destroy', $brand->id) }}" class="mt-4" data-confirm="Bạn có chắc muốn xóa thương hiệu này?">
  @csrf
  @method('DELETE')
  <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded" aria-label="Delete brand" title="Delete">Delete</button>
</form>
@endcan
@endsection
