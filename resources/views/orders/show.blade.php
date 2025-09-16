@extends('client.layout')
@section('title','Chi tiet don hang')
@section('content')
  <div class="max-w-5xlmax-w-7xl mx-auto p-4">
    @if(session('status'))
      <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
    @endif

    @if($errors->any())
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
        @foreach($errors->all() as $err)
          <div>{{ $err }}</div>
        @endforeach
      </div>
    @endif

    <h1 class="text-2xl font-semibold mb-2">Don hang {{ $order->code }}</h1>
    <div class="text-sm text-gray-700 mb-2">Trang thai: {{ $order->status }} � Thanh toan: {{ $order->payment_status }}</div>
    @if($order->paid_at)
      <div class="text-xs text-gray-500 mb-4">Da thanh toan luc {{ optional($order->paid_at)->format('d/m/Y H:i') }}</div>
    @endif

    <div class="grid md:grid-cols-3 gap-6">
      <div class="md:col-span-2 bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">San pham</h2>
        @foreach($order->items as $it)
          <div class="flex items-center justify-between border-b py-3">
            <div>{{ $it->name_snapshot }} x {{ $it->quantity }}</div>
            <div>{{ number_format($it->total,0,',','.') }} VND</div>
          </div>
        @endforeach
        <div class="text-right mt-4">
          <div>Tam tinh: <strong>{{ number_format($order->subtotal,0,',','.') }} VND</strong></div>
          <div>Van chuyen: <strong>{{ number_format($order->shipping_fee,0,',','.') }} VND</strong></div>
          <div>Tong: <strong>{{ number_format($order->grand_total,0,',','.') }} VND</strong></div>
        </div>
      </div>
      <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Giao hang</h2>
        @if($order->shippingAddress)
          <div class="text-sm">{{ $order->shippingAddress->full_name }}</div>
          <div class="text-sm">{{ $order->shippingAddress->phone }}</div>
          <div class="text-sm">{{ $order->shippingAddress->line1 }}, {{ $order->shippingAddress->district }}, {{ $order->shippingAddress->city }}</div>
        @endif
      </div>
    </div>

    @if($order->status === 'pending' && $order->payment_status === 'unpaid')
      <div class="mt-6">
        <a href="{{ route('orders.pay.form', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300">Thanh toan VNPay</a>
        <p class="text-xs text-gray-500 mt-2">Ban se duoc dieu huong sang cong VNPay sau khi xac nhan thong tin thanh toan.</p>
      </div>
    @endif

    <div class="mt-4"><a class="text-blue-600" href="{{ route('orders.index') }}">? Quay lai danh sach</a></div>
  </div>
@endsection
