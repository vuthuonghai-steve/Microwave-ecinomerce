@extends('client.layout')
@section('title','Giỏ hàng')
@section('content')
  <div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Giỏ hàng</h1>
    @if(session('status'))
      <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="bg-red-50 text-red-700 px-4 py-2 rounded mb-4">{{ $errors->first() }}</div>
    @endif
    <div class="bg-white rounded shadow p-4">
      @forelse($items as $it)
        <div class="flex items-center justify-between border-b py-3">
          <div class="flex items-center gap-3">
            @if($it->product->thumbnail)
              <img src="{{ $it->product->thumbnail }}" class="w-16 h-16 object-cover rounded" />
            @endif
            <div>
              <div class="font-medium">{{ $it->product->name }}</div>
              <div class="text-sm text-gray-600">{{ number_format($it->price_snapshot,0,',','.') }}₫</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <form method="post" action="{{ route('cart.items.update', $it->id) }}" class="flex items-center gap-2">
              @csrf
              <input type="number" name="quantity" min="1" value="{{ $it->quantity }}" class="w-20 border rounded px-2 py-1" />
              <button class="bg-gray-200 px-3 py-1 rounded">Cập nhật</button>
            </form>
            <form method="post" action="{{ route('cart.items.remove', $it->id) }}">
              @csrf
              <button class="text-red-600">Xóa</button>
            </form>
          </div>
        </div>
      @empty
        <div class="text-center text-gray-500">Giỏ hàng trống.</div>
      @endforelse
      <div class="text-right mt-4">
        <div>Tạm tính: <strong>{{ number_format($subtotal,0,',','.') }}₫</strong></div>
        <a href="{{ route('checkout.create') }}" class="inline-block mt-3 bg-blue-600 text-white px-4 py-2 rounded {{ $items->count() ? '' : 'pointer-events-none opacity-50' }}">Thanh toán</a>
      </div>
    </div>
  </div>
@endsection
