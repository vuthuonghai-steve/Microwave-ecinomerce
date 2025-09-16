@extends('client.layout')
@section('title','Quên mật khẩu')
@section('content')
  <div class="mx-auto p-4 flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded shadow p-6">
      <h1 class="text-xl font-semibold mb-4">Quên mật khẩu</h1>
      @if(session('status'))
        <div class="bg-green-100 text-green-800 px-3 py-2 rounded mb-3">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="bg-red-50 text-red-700 px-3 py-2 rounded mb-3">{{ $errors->first() }}</div>
      @endif
      <form method="post" action="{{ route('password.email') }}" class="space-y-3">
        @csrf
        <div>
          <label class="block text-sm">Email</label>
          <input name="email" type="email" required class="w-full border rounded px-3 py-2" value="{{ old('email') }}" />
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Gửi liên kết đặt lại mật khẩu</button>
      </form>
    </div>
  </div>
@endsection
