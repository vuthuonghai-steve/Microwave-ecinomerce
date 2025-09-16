@extends('client.layout')
@section('title','Yêu thích')
@section('content')
  <div class="mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Danh sách yêu thích</h1>
      <a class="text-blue-600" href="{{ route('products.index') }}">Tiếp tục mua sắm</a>
    </div>
    <div class="grid sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
      @forelse($items as $it)
        <div class="relative">
          @include('components.product-card', ['product' => $it->product])
          <form method="post" action="{{ route('wishlist.items.remove', $it->id) }}" class="absolute top-2 right-2">
            @csrf
            <button class="bg-white/90 text-red-600 rounded-full w-8 h-8 flex items-center justify-center shadow" title="Xóa">×</button>
          </form>
        </div>
      @empty
        <div class="col-span-full text-center text-gray-500">Chưa có sản phẩm yêu thích.</div>
      @endforelse
    </div>
  </div>
@endsection

