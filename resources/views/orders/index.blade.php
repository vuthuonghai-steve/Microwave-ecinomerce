@extends('client.layout')
@section('title','Đơn hàng của tôi')
@section('content')
  <div class="max-w-5xlmax-w-7xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Đơn hàng của tôi</h1>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="min-w-full">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="p-2">Mã</th>
            <th class="p-2">Trạng thái</th>
            <th class="p-2">Thanh toán</th>
            <th class="p-2">Tổng tiền</th>
            <th class="p-2">Ngày</th>
            <th class="p-2">Xem</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $o)
          <tr class="border-t">
            <td class="p-2">{{ $o->code }}</td>
            <td class="p-2">{{ $o->status }}</td>
            <td class="p-2">{{ $o->payment_status }}</td>
            <td class="p-2">{{ number_format($o->grand_total,0,',','.') }}₫</td>
            <td class="p-2">{{ $o->created_at->format('Y-m-d H:i') }}</td>
            <td class="p-2"><a class="text-blue-600" href="{{ route('orders.show', $o->id) }}">Chi tiết</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
  </div>
@endsection
