@extends('client.layout')
@section('title','Chi tiết đơn hàng')
@section('content')
  <div class="max-w-5xl mx-auto p-4">
    @if(session('status'))
      <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">{{ $errors->first() }}</div>
    @endif
    <h1 class="text-2xl font-semibold mb-2">Đơn hàng {{ $order->code }}</h1>
    <div class="text-sm text-gray-700 mb-4">
      <span>Trạng thái: 
        @switch($order->status)
          @case('pending') <span class="font-semibold text-yellow-600">Chờ xử lý</span> @break
          @case('processing') <span class="font-semibold text-blue-600">Đang xử lý</span> @break
          @case('shipped') <span class="font-semibold text-purple-600">Đang giao</span> @break
          @case('completed') <span class="font-semibold text-green-600">Hoàn thành</span> @break
          @case('cancelled') <span class="font-semibold text-gray-600">Đã hủy</span> @break
          @default {{ $order->status }}
        @endswitch
      </span>
      <span class="mx-2">•</span>
      <span>Thanh toán: 
        @switch($order->payment_status)
          @case('paid') <span class="font-semibold text-green-600">Đã thanh toán</span> @break
          @case('unpaid') <span class="font-semibold text-yellow-600">Chưa thanh toán</span> @break
          @case('failed') <span class="font-semibold text-red-600">Thất bại</span> @break
          @default {{ $order->payment_status }}
        @endswitch
      </span>
    </div>

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

        <h2 class="font-semibold mb-2 mt-4">Phương thức thanh toán</h2>
        <div class="text-sm">
          @switch($order->payment_method)
            @case('cod')
              Thanh toán khi nhận hàng (COD)
              @break
            @case('vnpay')
              Thanh toán qua VNPay
              @break
            @default
              {{ $order->payment_method }}
          @endswitch
        </div>
      </div>
    </div>

    <div class="mt-4"><a class="text-blue-600" href="{{ route('orders.index') }}">← Quay lại danh sách</a></div>
  </div>
@endsection
