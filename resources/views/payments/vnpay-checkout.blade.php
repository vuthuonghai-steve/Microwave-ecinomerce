@extends('client.layout')
@section('title','Thanh toan VNPay')
@section('content')
  <div class="max-w-4xlmax-w-7xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Thong tin thanh toan VNPay</h1>

    <div class="grid md:grid-cols-3 gap-6">
      <div class="md:col-span-2 bg-white rounded shadow p-6">
        <form method="POST" action="{{ route('orders.pay', $order->id) }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium mb-1" for="customer_name">Ho ten chu the</label>
            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $user->name) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" for="customer_email">Email lien he</label>
            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', $user->email) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-500" required>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1" for="customer_phone">So dien thoai</label>
            <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', optional($order->shippingAddress)->phone) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-500" required>
          </div>

          <div class="pt-2 border-t">
            <h2 class="text-sm font-semibold mb-2">Thong tin the (sandbox)</h2>
            <div>
              <label class="block text-sm font-medium mb-1" for="card_number">So the</label>
              <input type="text" name="card_number" id="card_number" value="{{ old('card_number') }}" maxlength="19" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-500" placeholder="VD: 9704198526191432198" required>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-3">
              <div>
                <label class="block text-sm font-medium mb-1" for="card_expiry_month">Thang het han</label>
                <input type="number" name="card_expiry_month" id="card_expiry_month" value="{{ old('card_expiry_month', 7) }}" min="1" max="12" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-500" required>
              </div>
              <div>
                <label class="block text-sm font-medium mb-1" for="card_expiry_year">Nam het han</label>
                <input type="number" name="card_expiry_year" id="card_expiry_year" value="{{ old('card_expiry_year', 2015) }}" min="2000" max="2100" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-500" required>
              </div>
            </div>
            <div class="mt-3">
              <label class="block text-sm font-medium mb-1" for="card_cvv">Ma CVV/OTP</label>
              <input type="password" name="card_cvv" id="card_cvv" value="{{ old('card_cvv') }}" maxlength="6" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-indigo-500" placeholder="VD: 123456" required>
            </div>
            <p class="text-xs text-gray-500 mt-2">Chi thu nghiem trong sandbox VNPay. Khong luu tru thong tin the tren he thong.</p>
          </div>

          @if($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded">
              @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
              @endforeach
            </div>
          @endif

          <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300">
            Tiep tuc den cong VNPay
          </button>
          <p class="text-xs text-gray-500">Thong tin nay se duoc gui sang VNPay de xac nhan chu the. Ban se nhap thong tin the tai trang VNPay.</p>
        </form>
      </div>
      <div class="bg-white rounded shadow p-6">
        <h2 class="font-semibold mb-2">Tom tat don hang</h2>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between"><span>Ma don</span><span>{{ $order->code }}</span></div>
          <div class="flex justify-between"><span>Trang thai</span><span>{{ $order->status }}</span></div>
          <div class="flex justify-between"><span>Thanh toan</span><span>{{ $order->payment_status }}</span></div>
          <div class="border-t pt-2 flex justify-between"><span>Tong thanh toan</span><span class="font-semibold">{{ number_format($order->grand_total,0,',','.') }} VND</span></div>
        </div>
        <div class="mt-4 text-xs text-gray-500">
          VNPay se xu ly thanh toan an toan va tra ve ket qua tu dong. Vui long khong dong cua so den khi hoan tat.
        </div>
      </div>
    </div>

    <div class="mt-4">
      <a href="{{ route('orders.show', $order->id) }}" class="text-blue-600">? Quay lai don hang</a>
    </div>
  </div>
@endsection
