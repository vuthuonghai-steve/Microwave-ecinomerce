@extends('admin.layout')
@section('title','Edit Category')
@section('content')
<h1 class="text-xl font-semibold mb-4">Edit Category</h1>
<form method="post" action="{{ route('admin.categories.update', $category->id) }}" class="bg-white p-4 rounded shadow grid grid-cols-1 md:grid-cols-2 gap-4">
  @csrf
  @method('PUT')
  <div>
    <label class="block text-sm">Name</label>
    <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $category->name) }}"/>
  </div>
  <div>
    <label class="block text-sm">Slug</label>
    <input name="slug" class="w-full border rounded px-3 py-2" value="{{ old('slug', $category->slug) }}"/>
  </div>
  <div>
    <label class="block text-sm">Parent</label>
    <select name="parent_id" class="w-full border rounded px-3 py-2">
      <option value="">-- None --</option>
      @foreach($parents as $p)
        <option value="{{ $p->id }}" @selected(old('parent_id', $category->parent_id)==$p->id)>{{ $p->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active))> Active</label>
  </div>
  <div class="md:col-span-2 flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
  </div>
</form>

@can('delete', $category)
<form method="post" action="{{ route('admin.categories.destroy', $category->id) }}" class="mt-4" data-confirm="Bạn có chắc muốn xóa danh mục này?">
  @csrf
  @method('DELETE')
  <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded" aria-label="Delete category" title="Delete">Delete</button>
</form>
@endcan
@endsection
