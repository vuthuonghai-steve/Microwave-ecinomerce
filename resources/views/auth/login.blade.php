<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  </head>
<body class="bg-gray-100 text-gray-900">
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded shadow p-6">
      <h1 class="text-xl font-semibold mb-4">Đăng nhập</h1>
      @if(session('status'))
        <div class="bg-green-100 text-green-800 px-3 py-2 rounded mb-3">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="bg-red-50 text-red-700 px-3 py-2 rounded mb-3">{{ $errors->first() }}</div>
      @endif
      <form method="post" action="{{ route('login') }}" class="space-y-3">
        @csrf
        <div>
          <label class="block text-sm">Email</label>
          <input name="email" type="email" required class="w-full border rounded px-3 py-2" value="{{ old('email') }}" />
        </div>
        <div>
          <label class="block text-sm">Mật khẩu</label>
          <input name="password" type="password" required class="w-full border rounded px-3 py-2" />
        </div>
        <div class="flex items-center justify-between">
          <label class="text-sm"><input type="checkbox" name="remember" /> Ghi nhớ</label>
          <a class="text-blue-600 text-sm" href="{{ route('password.request') }}">Quên mật khẩu?</a>
        </div>
        <div class="flex items-center gap-3">
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Đăng nhập</button>
          <a class="text-sm text-blue-600" href="{{ route('register') }}">Tạo tài khoản</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

