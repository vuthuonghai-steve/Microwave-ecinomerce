@extends('client.layout')
@section('title','Địa chỉ của tôi')
@section('content')
  <div class="max-w-5xlmax-w-7xl mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-semibold">Địa chỉ của tôi</h1>
      <a class="bg-blue-600 text-white px-4 py-2 rounded" href="{{ route('addresses.create') }}">Thêm địa chỉ</a>
    </div>
    @if(session('status'))
      <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
    @endif
    <div class="bg-white rounded shadow p-4">
      @forelse($addresses as $a)
        <div class="border-b py-3 flex items-center justify-between">
          <div>
            <div class="font-medium">{{ $a->full_name }} — {{ $a->phone }}</div>
            <div class="text-sm text-gray-700">{{ $a->line1 }}, {{ $a->district }}, {{ $a->city }}</div>
            @if($a->is_default)
              <span class="inline-block text-xs bg-green-100 text-green-800 px-2 py-1 rounded mt-1">Mặc định</span>
            @endif
          </div>
          <div class="flex items-center gap-3">
            <a class="text-blue-600" href="{{ route('addresses.edit', $a->id) }}">Sửa</a>
            <form method="post" action="{{ route('addresses.destroy', $a->id) }}" onsubmit="return confirm('Xóa địa chỉ này?')">
              @csrf
              @method('DELETE')
              <button class="text-red-600">Xóa</button>
            </form>
          </div>
        </div>
      @empty
        <div class="text-center text-gray-500">Chưa có địa chỉ nào.</div>
      @endforelse
    </div>
    <div class="mt-4">{{ $addresses->links() }}</div>
  </div>
@endsection
