@extends('admin.layout')
@section('title','Edit Product')
@section('content')
<h1 class="text-xl font-semibold mb-4">Edit Product</h1>
<form method="post" action="{{ route('admin.products.update', $product->id) }}" class="bg-white p-4 rounded shadow grid grid-cols-1 md:grid-cols-2 gap-4">
  @csrf
  @method('PUT')
  <div>
    <label class="block text-sm">Name</label>
    <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $product->name) }}"/>
  </div>
  <div>
    <label class="block text-sm">Slug</label>
    <input name="slug" class="w-full border rounded px-3 py-2" value="{{ old('slug', $product->slug) }}"/>
  </div>
  <div>
    <label class="block text-sm">Brand</label>
    <select name="brand_id" class="w-full border rounded px-3 py-2" required>
      @foreach($brands as $b)
        <option value="{{ $b->id }}" @selected(old('brand_id',$product->brand_id)==$b->id)>{{ $b->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="block text-sm">Category</label>
    <select name="category_id" class="w-full border rounded px-3 py-2" required>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" @selected(old('category_id',$product->category_id)==$c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="block text-sm">Price</label>
    <input name="price" type="number" step="0.01" class="w-full border rounded px-3 py-2" required value="{{ old('price', $product->price) }}"/>
  </div>
  <div>
    <label class="block text-sm">Sale Price</label>
    <input name="sale_price" type="number" step="0.01" class="w-full border rounded px-3 py-2" value="{{ old('sale_price', $product->sale_price) }}"/>
  </div>
  <div>
    <label class="block text-sm">Capacity (L)</label>
    <input name="capacity_liters" type="number" class="w-full border rounded px-3 py-2" required value="{{ old('capacity_liters', $product->capacity_liters) }}"/>
  </div>
  <div>
    <label class="block text-sm">Power (W)</label>
    <input name="power_watt" type="number" class="w-full border rounded px-3 py-2" value="{{ old('power_watt', $product->power_watt) }}"/>
  </div>
  <div class="flex items-center gap-2">
    <label><input type="checkbox" name="inverter" value="1" @checked(old('inverter', $product->inverter))> Inverter</label>
    <label><input type="checkbox" name="has_grill" value="1" @checked(old('has_grill', $product->has_grill))> Có nướng</label>
    <label><input type="checkbox" name="child_lock" value="1" @checked(old('child_lock', $product->child_lock))> Khóa trẻ em</label>
  </div>
  <div>
    <label class="block text-sm">Energy Rating (1-5)</label>
    <input name="energy_rating" type="number" min="1" max="5" class="w-full border rounded px-3 py-2" value="{{ old('energy_rating', $product->energy_rating) }}"/>
  </div>
  <div>
    <label class="block text-sm">Warranty (months)</label>
    <input name="warranty_months" type="number" class="w-full border rounded px-3 py-2" required value="{{ old('warranty_months', $product->warranty_months) }}"/>
  </div>
  <div class="md:col-span-2">
    <label class="block text-sm">Thumbnail URL</label>
    <input name="thumbnail" class="w-full border rounded px-3 py-2" value="{{ old('thumbnail', $product->thumbnail) }}"/>
  </div>
  <div class="md:col-span-2">
    <label class="block text-sm">Description</label>
    <textarea name="description" rows="5" class="w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
  </div>
  <div>
    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Active</label>
  </div>
  <div class="md:col-span-2 flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
  </div>
</form>

@can('delete', $product)
<form method="post" action="{{ route('admin.products.destroy', $product->id) }}" class="mt-4" data-confirm="Bạn có chắc muốn xóa sản phẩm này?">
  @csrf
  @method('DELETE')
  <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded" aria-label="Delete product" title="Delete">Delete</button>
  <p class="text-xs text-gray-500 mt-1">Hành động này không thể hoàn tác.</p>
</form>
@endcan
@endsection
