@extends('client.layout')
@section('title','Thanh toán')
@section('content')
  <div class="max-w-7xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Thanh toán</h1>
    @if ($errors->any())
      <div class="bg-red-50 text-red-700 px-4 py-2 rounded mb-4">{{ $errors->first() }}</div>
    @endif
    <div class="grid md:grid-cols-3 gap-6">
      <div class="md:col-span-2 bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Giỏ hàng</h2>
        @php $subtotal = 0; @endphp
        @foreach($cart->items as $it)
          @php $subtotal += $it->price_snapshot * $it->quantity; @endphp
          <div class="flex items-center justify-between border-b py-3">
            <div>{{ $it->product->name }} × {{ $it->quantity }}</div>
            <div>{{ number_format($it->price_snapshot * $it->quantity,0,',','.') }}₫</div>
          </div>
        @endforeach
      </div>
      <div class="bg-white rounded shadow p-4">
        <form method="post" action="{{ route('checkout.store') }}">
          @csrf
          <h2 class="font-semibold mb-2">Địa chỉ giao hàng</h2>
          <select name="shipping_address_id" class="w-full border rounded px-3 py-2 mb-3" required>
            @foreach($addresses as $addr)
              <option value="{{ $addr->id }}">{{ $addr->full_name }} - {{ $addr->line1 }}, {{ $addr->district }}, {{ $addr->city }}</option>
            @endforeach
          </select>
          <div class="text-sm text-gray-600 mb-2">Phí vận chuyển: 30.000₫</div>
          <div class="font-medium mb-3">Tạm tính: {{ number_format($subtotal,0,',','.') }}₫</div>
          <h2 class="font-semibold mb-2">Phương thức thanh toán</h2>
          <div class="mb-3 space-y-2">
            <label class="flex items-center p-3 border rounded-lg hover:border-gray-400">
              <input type="radio" name="payment_method" value="cod" checked class="mr-2 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
              Thanh toán khi nhận hàng (COD)
            </label>
            <label class="flex items-center p-3 border rounded-lg hover:border-gray-400">
              <input type="radio" name="payment_method" value="vnpay" class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
              Thanh toán qua VNPay
            </label>
          </div>
          <button class="w-full bg-green-600 text-white px-4 py-2 rounded mt-4">Đặt hàng</button>
        </form>
      </div>
    </div>
  </div>
@endsection
