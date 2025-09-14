@extends('admin.layout')
@section('title','Orders')
@section('content')
<h1 class="text-xl font-semibold mb-4">Orders</h1>
<div class="bg-white rounded shadow overflow-x-auto">
  <table class="min-w-full">
    <thead class="bg-gray-100 text-left">
      <tr>
        <th class="p-2">ID</th>
        <th class="p-2">Code</th>
        <th class="p-2">Customer</th>
        <th class="p-2">Status</th>
        <th class="p-2">Payment</th>
        <th class="p-2">Grand Total</th>
        <th class="p-2">Created</th>
        <th class="p-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($orders as $o)
        <tr class="border-t">
          <td class="p-2">{{ $o->id }}</td>
          <td class="p-2">{{ $o->code }}</td>
          <td class="p-2">{{ $o->user->name ?? '-' }}</td>
          <td class="p-2">{{ $o->status }}</td>
          <td class="p-2">{{ $o->payment_status }}</td>
          <td class="p-2">{{ number_format($o->grand_total,0,',','.') }}₫</td>
          <td class="p-2">{{ $o->created_at->format('Y-m-d H:i') }}</td>
          <td class="p-2"><a class="text-blue-600" href="{{ route('admin.orders.show', $o->id) }}">View</a></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection

