@extends('client.layout')
@section('title','Đăng ký')
@section('content')
  <div class="mx-auto p-4 flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded shadow p-6">
      <h1 class="text-xl font-semibold mb-4">Tạo tài khoản</h1>
      @if ($errors->any())
        <div class="bg-red-50 text-red-700 px-3 py-2 rounded mb-3">{{ $errors->first() }}</div>
      @endif
      <form method="post" action="{{ route('register') }}" class="space-y-3">
        @csrf
        <div>
          <label class="block text-sm">Họ tên</label>
          <input name="name" required class="w-full border rounded px-3 py-2" value="{{ old('name') }}" />
        </div>
        <div>
          <label class="block text-sm">Email</label>
          <input name="email" type="email" required class="w-full border rounded px-3 py-2" value="{{ old('email') }}" />
        </div>
        <div>
          <label class="block text-sm">Mật khẩu</label>
          <input name="password" type="password" required class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm">Xác nhận mật khẩu</label>
          <input name="password_confirmation" type="password" required class="w-full border rounded px-3 py-2" />
        </div>
        <div class="flex items-center gap-3">
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Đăng ký</button>
          <a class="text-sm text-blue-600" href="{{ route('login') }}">Đã có tài khoản? Đăng nhập</a>
        </div>
      </form>
    </div>
  </div>
@endsection
