@extends('client.layout')
@section('title','Tài khoản')
@section('content')
  <div class="max-w-3xlmax-w-7xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Tài khoản</h1>
    @if(session('status'))
      <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="bg-red-50 text-red-700 px-4 py-2 rounded mb-4">{{ $errors->first() }}</div>
    @endif
    <form method="post" action="{{ route('profile.update') }}" class="bg-white rounded shadow p-4 grid md:grid-cols-2 gap-4">
      @csrf
      @method('PUT')
      <div>
        <label class="block text-sm">Họ tên</label>
        <input name="name" required class="w-full border rounded px-3 py-2" value="{{ old('name', $user->name) }}" />
      </div>
      <div>
        <label class="block text-sm">Email</label>
        <input name="email" type="email" required class="w-full border rounded px-3 py-2" value="{{ old('email', $user->email) }}" />
      </div>
      <div>
        <label class="block text-sm">Mật khẩu mới (tùy chọn)</label>
        <input name="password" type="password" class="w-full border rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm">Xác nhận mật khẩu</label>
        <input name="password_confirmation" type="password" class="w-full border rounded px-3 py-2" />
      </div>
      <div class="md:col-span-2">
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Lưu thay đổi</button>
      </div>
    </form>
  </div>
@endsection

