@extends('client.layout')
@section('title','Đặt lại mật khẩu')
@section('content')
  <div class="max-w-7xl mx-auto p-4 flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded shadow p-6">
      <h1 class="text-xl font-semibold mb-4">Đặt lại mật khẩu</h1>
      @if ($errors->any())
        <div class="bg-red-50 text-red-700 px-3 py-2 rounded mb-3">{{ $errors->first() }}</div>
      @endif
      <form method="post" action="{{ route('password.update') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}" />
        <div>
          <label class="block text-sm">Email</label>
          <input name="email" type="email" required class="w-full border rounded px-3 py-2" value="{{ old('email', $email) }}" />
        </div>
        <div>
          <label class="block text-sm">Mật khẩu mới</label>
          <input name="password" type="password" required class="w-full border rounded px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm">Xác nhận mật khẩu</label>
          <input name="password_confirmation" type="password" required class="w-full border rounded px-3 py-2" />
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Đặt lại</button>
      </form>
    </div>
  </div>
@endsection
