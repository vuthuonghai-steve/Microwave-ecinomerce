<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title','Microwave Shop')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
  <header class="bg-white shadow sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">
      <a href="{{ route('home') }}" class="font-semibold text-lg">Microwave</a>
      <a class="text-blue-600" href="{{ route('products.index') }}">Sản phẩm</a>
      @auth
        <a class="text-blue-600" href="{{ route('profile.edit') }}">Tài khoản</a>
        <a class="text-blue-600" href="{{ route('orders.index') }}">Đơn hàng</a>
        <a class="text-blue-600" href="{{ route('addresses.index') }}">Địa chỉ</a>
      @endauth
      <a class="ml-auto text-blue-600" href="{{ route('wishlist.index') }}">Yêu thích (<span id="navWishCount">0</span>)</a>
      <a class="text-blue-600" href="{{ route('cart.index') }}">Giỏ hàng (<span id="navCartCount">0</span>)</a>
      @auth
        <form method="post" action="{{ route('logout') }}">
          @csrf
          <button class="text-red-600">Đăng xuất</button>
        </form>
      @else
        <a class="text-blue-600" href="{{ route('login') }}">Đăng nhập</a>
        <a class="text-blue-600" href="{{ route('register') }}">Đăng ký</a>
      @endauth
    </div>
  </header>
  <main class="min-h-screen">
    @auth
      @if(!auth()->user()->hasVerifiedEmail())
        <div class="max-w-7xl mx-auto px-4 mt-4">
          <div class="bg-yellow-50 text-yellow-800 px-4 py-2 rounded">
            Tài khoản của bạn chưa xác thực email. <a class="underline" href="{{ route('verification.notice') }}">Xác thực ngay</a>.
          </div>
        </div>
      @endif
    @endauth
    @if(session('status'))
      <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
      </div>
    @endif
    @yield('content')
  </main>
  @stack('scripts')
</body>
</html>
