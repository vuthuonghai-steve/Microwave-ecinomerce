@extends('client.layout')
@section('title','Thêm địa chỉ')
@section('content')
  <div class="max-w-3xlmax-w-7xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Thêm địa chỉ</h1>
    @if ($errors->any())
      <div class="bg-red-50 text-red-700 px-4 py-2 rounded mb-4">{{ $errors->first() }}</div>
    @endif
    <form method="post" action="{{ route('addresses.store') }}" class="bg-white rounded shadow p-4 grid md:grid-cols-2 gap-4">
      @csrf
      <div>
        <label class="block text-sm">Họ tên</label>
        <input name="full_name" required class="w-full border rounded px-3 py-2" value="{{ old('full_name') }}" />
      </div>
      <div>
        <label class="block text-sm">Số điện thoại</label>
        <input name="phone" required class="w-full border rounded px-3 py-2" value="{{ old('phone') }}" />
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm">Địa chỉ</label>
        <input name="line1" required class="w-full border rounded px-3 py-2" value="{{ old('line1') }}" />
      </div>
      <div>
        <label class="block text-sm">Quận/Huyện</label>
        <input name="district" required class="w-full border rounded px-3 py-2" value="{{ old('district') }}" />
      </div>
      <div>
        <label class="block text-sm">Tỉnh/Thành phố</label>
        <input name="city" required class="w-full border rounded px-3 py-2" value="{{ old('city') }}" />
      </div>
      <div>
        <label class="block text-sm">Quốc gia</label>
        <input name="country_code" class="w-full border rounded px-3 py-2" value="{{ old('country_code','VN') }}" />
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_default" value="1" @checked(old('is_default')) /> <span>Mặc định</span>
      </div>
      <div class="md:col-span-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Lưu</button>
        <a class="ml-3 text-blue-600" href="{{ route('addresses.index') }}">Hủy</a>
      </div>
    </form>
  </div>
@endsection
