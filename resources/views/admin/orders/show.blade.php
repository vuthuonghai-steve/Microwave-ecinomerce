@extends('admin.layout')
@section('title','Order #'.$order->id)
@section('content')
<h1 class="text-xl font-semibold mb-4">Order {{ $order->code }}</h1>
@if(session('status'))
  <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
@endif
<div class="grid md:grid-cols-3 gap-4">
  <div class="md:col-span-2 bg-white rounded shadow p-4">
    <h2 class="font-semibold mb-2">Items</h2>
    <table class="min-w-full">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="p-2">Product</th>
          <th class="p-2">Price</th>
          <th class="p-2">Qty</th>
          <th class="p-2">Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $it)
        <tr class="border-t">
          <td class="p-2">{{ $it->name_snapshot }}</td>
          <td class="p-2">{{ number_format($it->price_snapshot,0,',','.') }}₫</td>
          <td class="p-2">{{ $it->quantity }}</td>
          <td class="p-2">{{ number_format($it->total,0,',','.') }}₫</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="mt-4 text-right">
      <div>Subtotal: <strong>{{ number_format($order->subtotal,0,',','.') }}₫</strong></div>
      <div>Shipping: <strong>{{ number_format($order->shipping_fee,0,',','.') }}₫</strong></div>
      <div>Grand: <strong>{{ number_format($order->grand_total,0,',','.') }}₫</strong></div>
    </div>
  </div>
  <div class="bg-white rounded shadow p-4">
    <h2 class="font-semibold mb-2">Status & Payment</h2>
    <div class="mb-2">Current: {{ $order->status }} / {{ $order->payment_status }}</div>
    @if ($errors->any())
      <div class="bg-red-50 text-red-700 px-3 py-2 rounded mb-2">{{ $errors->first() }}</div>
    @endif
    <form method="post" action="{{ route('admin.orders.status', $order->id) }}" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm">Update status</label>
        <select name="status" class="w-full border rounded px-2 py-1">
          <option value="">-- keep --</option>
          @foreach($allowedStatuses as $s)
            <option value="{{ $s }}">{{ $s }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm">Mark payment as</label>
        <select name="payment_status" class="w-full border rounded px-2 py-1">
          <option value="">-- keep --</option>
          <option value="paid">paid</option>
        </select>
      </div>
      <button class="bg-blue-600 text-white px-3 py-2 rounded">Update</button>
    </form>

    <h2 class="font-semibold mt-6 mb-2">Shipping</h2>
    @if($shipment)
      <div class="text-sm mb-2">Tracking: <strong>{{ $shipment->tracking_code }}</strong> ({{ $shipment->status }})</div>
    @endif
    <form method="post" action="{{ route('admin.orders.push_shipping', $order->id) }}" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm">Carrier</label>
        <select name="carrier_code" class="w-full border rounded px-2 py-1">
          @foreach($carriers as $c)
            <option value="{{ $c['code'] }}">{{ $c['name'] }}</option>
          @endforeach
        </select>
      </div>
      <button class="bg-indigo-600 text-white px-3 py-2 rounded">Push to shipping</button>
    </form>
  </div>
  <div class="md:col-span-3 bg-white rounded shadow p-4">
    <h2 class="font-semibold mb-2">Shipment Events</h2>
    @php $events = optional($shipment)->events()->orderBy('occurred_at')->get() ?? collect(); @endphp
    @if($events->isEmpty())
      <div class="text-sm text-gray-500">No events.</div>
    @else
      <ul class="text-sm">
        @foreach($events as $e)
          <li class="border-t py-1">{{ $e->occurred_at }} — <strong>{{ $e->status }}</strong> — {{ $e->message }}</li>
        @endforeach
      </ul>
    @endif
  </div>
</div>
@endsection
