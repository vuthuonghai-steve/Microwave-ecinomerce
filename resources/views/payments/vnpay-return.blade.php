@extends('client.layout')
@section('title','Trang thanh toan VNPay')
@section('content')
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Trang thai giao dich VNPay</h1>
    <div class="bg-white rounded shadow p-4">
      <div class="text-lg font-medium {{ $isSuccess ? 'text-green-600' : 'text-red-600' }}">{{ $message }}</div>
      <p class="text-sm text-gray-600 mt-2">
        VNPay tra ve ma trang thai: <strong>{{ $payload['vnp_ResponseCode'] ?? 'N/A' }}</strong> - <strong>{{ $payload['vnp_TransactionStatus'] ?? 'N/A' }}</strong>.
      </p>
      <p class="text-sm text-gray-600">Chu ky hop le: <strong>{{ $signatureValid ? 'Co' : 'Khong' }}</strong>.</p>
      <div class="mt-4">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Xem don hang cua toi</a>
      </div>
    </div>

    <details class="mt-6 bg-white rounded shadow p-4">
      <summary class="cursor-pointer font-semibold">Chi tiet phan hoi</summary>
      <pre class="mt-3 text-xs bg-gray-100 p-3 rounded overflow-x-auto">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </details>
  </div>
@endsection
