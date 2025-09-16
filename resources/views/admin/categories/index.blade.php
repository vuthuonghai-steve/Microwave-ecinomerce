@extends('admin.layout')
@section('title','Categories')
@section('content')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-xl font-semibold">Categories</h1>
  <a class="bg-blue-600 text-white px-4 py-2 rounded" href="{{ route('admin.categories.create') }}">Create</a>
</div>
<form method="get" class="bg-white rounded shadow p-4 mb-4 grid grid-cols-1 md:grid-cols-6 gap-3">
  <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search name/slug" class="border rounded px-3 py-2 md:col-span-2" />
  <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full">
    <thead class="bg-gray-100 text-left">
      <tr>
        <th class="p-2">ID</th>
        <th class="p-2">Name</th>
        <th class="p-2">Slug</th>
        <th class="p-2">Parent</th>
        <th class="p-2">Active</th>
        <th class="p-2">Products</th>
        <th class="p-2">Sold</th>
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
          <td class="p-2">{{ $c->products_count }}</td>
          <td class="p-2">{{ (int) ($c->sold_qty ?? 0) }}</td>
          <td class="p-2 whitespace-nowrap">
            <div class="inline-flex">
              {{-- Nút edit dễ thương màu hồng --}}
              @can('update', $b)
                <a href="{{ route('admin.categories.edit', $b->id) }}"
                  class="-ml-px border border-pink-200 px-3 py-2 text-pink-600 transition-colors 
                          hover:bg-pink-50 hover:text-pink-700 focus:z-10 focus:ring-2 focus:ring-pink-400 
                          focus:ring-offset-2 focus:ring-offset-white focus:outline-none rounded-l-lg
                          shadow-sm hover:shadow-md transform hover:scale-105">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 
                            19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 
                            4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                  </svg>
                </a>
              @endcan

              {{-- Nút delete dễ thương màu hồng --}}
              @can('delete', $b)
                <form action="{{ route('admin.categories.destroy', $b->id) }}" method="post" class="inline -ml-px"
                      data-confirm="Bạn có chắc muốn xóa thương hiệu này? 🥺">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                    class="rounded-r-lg border border-pink-200 px-3 py-2 text-pink-500 transition-colors 
                          hover:bg-pink-50 hover:text-pink-700 focus:z-10 focus:ring-2 focus:ring-pink-400 
                          focus:ring-offset-2 focus:ring-offset-white focus:outline-none
                          shadow-sm hover:shadow-md transform hover:scale-105"
                    aria-label="Delete brand" title="Delete">
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
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
