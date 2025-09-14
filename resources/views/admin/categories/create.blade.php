@extends('admin.layout')
@section('title','Create Category')
@section('content')
<h1 class="text-xl font-semibold mb-4">Create Category</h1>
<form method="post" action="{{ route('admin.categories.store') }}" class="bg-white p-4 rounded shadow grid grid-cols-1 md:grid-cols-2 gap-4">
  @csrf
  <div>
    <label class="block text-sm">Name</label>
    <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name') }}"/>
    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>
  <div>
    <label class="block text-sm">Slug (optional)</label>
    <input name="slug" class="w-full border rounded px-3 py-2" value="{{ old('slug') }}"/>
  </div>
  <div>
    <label class="block text-sm">Parent (optional)</label>
    <select name="parent_id" class="w-full border rounded px-3 py-2">
      <option value="">-- None --</option>
      @foreach($parents as $p)
        <option value="{{ $p->id }}">{{ $p->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Active</label>
  </div>
  <div class="md:col-span-2">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
  </div>
</form>
@endsection

