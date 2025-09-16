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
        <th class="p-2">Items</th>
        <th class="p-2">Qty</th>
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
          <td class="p-2">{{ $o->items_count }}</td>
          <td class="p-2">{{ (int) ($o->total_qty ?? 0) }}</td>
          <td class="p-2">{{ number_format($o->grand_total,0,',','.') }}₫</td>
          <td class="p-2">{{ $o->created_at->format('Y-m-d H:i') }}</td>
          <td class="p-2 space-x-3">
            <a class="text-blue-600 underline" href="{{ route('admin.orders.show', $o->id) }}">View</a>
            <button type="button"
              class="text-indigo-600 underline"
              data-action="open-status-modal"
              data-order-id="{{ $o->id }}"
              data-order-status="{{ $o->status }}"
              data-payment-status="{{ $o->payment_status }}">
              Cập nhật
            </button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
 
<!-- Quick Update Status Modal -->
<div id="orderStatusModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" data-action="close-status-modal"></div>
  <div class="relative bg-white rounded shadow-lg w-full max-w-md mx-auto mt-24 p-4">
    <h2 class="text-lg font-semibold mb-2">Cập nhật trạng thái đơn</h2>
    <form id="orderStatusForm" method="post" action="#" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm mb-1">Trạng thái hiện tại</label>
        <div id="curOrderStatus" class="text-sm text-gray-700">-</div>
      </div>
      <div>
        <label class="block text-sm mb-1">Chuyển sang</label>
        <select name="status" id="orderStatusSelect" class="w-full border rounded px-2 py-1"></select>
        <p class="text-xs text-gray-500 mt-1">Chỉ hiển thị các trạng thái chuyển hợp lệ.</p>
      </div>
      <div>
        <label class="block text-sm mb-1">Thanh toán</label>
        <select name="payment_status" id="paymentStatusSelect" class="w-full border rounded px-2 py-1">
          <option value="">-- giữ nguyên --</option>
          <option value="paid">paid</option>
        </select>
        <p class="text-xs text-gray-500 mt-1">Chỉ được đánh dấu "paid" khi đơn ở trạng thái delivered.</p>
      </div>
      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button" class="px-3 py-2 rounded border" data-action="close-status-modal">Đóng</button>
        <button type="submit" class="px-3 py-2 rounded bg-indigo-600 text-white">Cập nhật</button>
      </div>
    </form>
  </div>
  
</div>
@push('scripts')
<script>
  (function(){
    const map = {
      pending: ['processing','cancelled'],
      processing: ['packed','cancelled'],
      packed: ['shipping','cancelled'],
      shipping: ['delivered','cancelled'],
      delivered: [],
      cancelled: []
    };
    const modal = document.getElementById('orderStatusModal');
    const form = document.getElementById('orderStatusForm');
    const statusSelect = document.getElementById('orderStatusSelect');
    const paymentSelect = document.getElementById('paymentStatusSelect');
    const curText = document.getElementById('curOrderStatus');

    function openModal(orderId, status, payment) {
      // Set form action
      form.action = `{{ url('/admin/orders') }}/${orderId}/status`;
      // Current status
      curText.textContent = `${status} / ${payment}`;
      // Populate allowed next statuses
      statusSelect.innerHTML = '';
      const keep = document.createElement('option');
      keep.value = '';
      keep.textContent = '-- giữ nguyên --';
      statusSelect.appendChild(keep);
      (map[status] || []).forEach(s => {
        const opt = document.createElement('option');
        opt.value = s; opt.textContent = s; statusSelect.appendChild(opt);
      });
      // Enable payment only if delivered
      const isDelivered = status === 'delivered';
      paymentSelect.disabled = !isDelivered;
      if (!isDelivered) paymentSelect.value = '';
      // Show modal
      modal.classList.remove('hidden');
    }

    function closeModal(){ modal.classList.add('hidden'); }

    document.addEventListener('click', (e) => {
      const t = e.target;
      if (t.matches('[data-action="open-status-modal"]')) {
        e.preventDefault();
        const id = t.getAttribute('data-order-id');
        const st = t.getAttribute('data-order-status');
        const pay = t.getAttribute('data-payment-status') || 'unpaid';
        openModal(id, st, pay);
      }
      if (t.matches('[data-action="close-status-modal"]')) {
        e.preventDefault();
        closeModal();
      }
    });
    // Close on ESC
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
  })();
</script>
@endpush
@endsection
