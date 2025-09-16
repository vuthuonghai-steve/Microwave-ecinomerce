@extends('client.layout')
@section('title','Đơn hàng của tôi')
@section('content')
  <div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Đơn hàng của tôi</h1>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="min-w-full">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="p-2">Mã</th>
            <th class="p-2">Trạng thái ĐH</th>
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
            <td class="p-2">
              @switch($o->status)
                @case('pending')
                  <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Chờ xử lý</span>
                  @break
                @case('processing')
                  <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-200 rounded-full">Đang xử lý</span>
                  @break
                @case('shipped')
                  <span class="px-2 py-1 text-xs font-semibold text-purple-800 bg-purple-200 rounded-full">Đang giao</span>
                  @break
                @case('completed')
                  <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Hoàn thành</span>
                  @break
                @case('cancelled')
                  <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Đã hủy</span>
                  @break
                @default
                  {{ $o->status }}
              @endswitch
            </td>
            <td class="p-2">
              @switch($o->payment_status)
                @case('paid')
                  <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Đã thanh toán</span>
                  @break
                @case('unpaid')
                  <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Chưa thanh toán</span>
                  @break
                @case('failed')
                  <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Thất bại</span>
                  @break
                @default
                  {{ $o->payment_status }}
              @endswitch
            </td>
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
