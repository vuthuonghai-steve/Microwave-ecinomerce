@extends('client.layout')
@section('title','Chi tiết đơn hàng')
@section('content')
  <div class="max-w-5xl mx-auto p-4">
    @if(session('status'))
      <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
    @endif
    <h1 class="text-2xl font-semibold mb-2">Đơn hàng {{ $order->code }}</h1>
    <div class="text-sm text-gray-700 mb-4">Trạng thái: {{ $order->status }} • Thanh toán: {{ $order->payment_status }}</div>

    <div class="grid md:grid-cols-3 gap-6">
      <div class="md:col-span-2 bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Sản phẩm</h2>
        @foreach($order->items as $it)
          <div class="flex items-center justify-between border-b py-3">
            <div>{{ $it->name_snapshot }} × {{ $it->quantity }}</div>
            <div>{{ number_format($it->total,0,',','.') }}₫</div>
          </div>
        @endforeach
        <div class="text-right mt-4">
          <div>Tạm tính: <strong>{{ number_format($order->subtotal,0,',','.') }}₫</strong></div>
          <div>Vận chuyển: <strong>{{ number_format($order->shipping_fee,0,',','.') }}₫</strong></div>
          <div>Tổng: <strong>{{ number_format($order->grand_total,0,',','.') }}₫</strong></div>
        </div>
      </div>
      <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Giao hàng</h2>
        @if($order->shippingAddress)
          <div class="text-sm">{{ $order->shippingAddress->full_name }}</div>
          <div class="text-sm">{{ $order->shippingAddress->phone }}</div>
          <div class="text-sm">{{ $order->shippingAddress->line1 }}, {{ $order->shippingAddress->district }}, {{ $order->shippingAddress->city }}</div>
        @endif
      </div>
    </div>

    <div class="mt-4"><a class="text-blue-600" href="{{ route('orders.index') }}">← Quay lại danh sách</a></div>
  </div>
@endsection
