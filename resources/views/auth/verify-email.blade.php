<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Xác thực email</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style> body { background: #f8fafc; } </style>
  </head>
<body class="text-gray-900">
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded shadow p-6">
      <h1 class="text-xl font-semibold mb-2">Xác thực email</h1>
      <p class="text-sm text-gray-600 mb-4">
        Vui lòng kiểm tra hộp thư và nhấp vào liên kết xác thực để hoàn tất.
      </p>
      @if (session('status'))
        <div class="bg-green-50 text-green-700 px-3 py-2 rounded mb-3">{{ session('status') }}</div>
      @endif
      <div class="space-y-3">
        <form method="post" action="{{ route('verification.send') }}" class="inline">
          @csrf
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Gửi lại email xác thực</button>
        </form>
        <form method="post" action="{{ route('logout') }}" class="inline">
          @csrf
          <button class="text-gray-600 hover:underline">Đăng xuất</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>

