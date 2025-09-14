<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded shadow p-6">
      <h1 class="text-xl font-semibold mb-4">Đăng nhập Admin</h1>
      @if ($errors->any())
        <div class="bg-red-50 text-red-700 px-3 py-2 rounded mb-3">
          {{ $errors->first() }}
        </div>
      @endif
      <form method="post" action="{{ route('admin.login.submit') }}" class="space-y-3">
        @csrf
        <div>
          <label class="block text-sm">Email</label>
          <input name="email" type="email" required class="w-full border rounded px-3 py-2" value="{{ old('email','admin@gmail.com') }}" />
        </div>
        <div>
          <label class="block text-sm">Mật khẩu</label>
          <input name="password" type="password" required class="w-full border rounded px-3 py-2" value="{{ old('password') }}" />
        </div>
        <div class="flex items-center justify-between">
          <label class="text-sm"><input type="checkbox" name="remember" /> Ghi nhớ</label>
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Đăng nhập</button>
        </div>
      </form>
      <p class="text-xs text-gray-500 mt-4">Tài khoản mẫu: admin@gmail.com / Hai@123</p>
    </div>
  </div>
</body>
</html>

