<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title','Dashboard')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-7xl mx-auto p-4">
    <nav class="flex gap-4 mb-6 items-center">
        <a class="text-blue-600" href="{{ route('admin.products.index') }}">Products</a>
        <a class="text-blue-600" href="{{ route('admin.brands.index') }}">Brands</a>
        <a class="text-blue-600" href="{{ route('admin.categories.index') }}">Categories</a>
        <a class="text-blue-600" href="{{ route('admin.orders.index') }}">Orders</a>
        <!-- buttoon back -->
        
        <form method="post" action="{{ route('admin.logout') }}" class="ml-auto">
            @csrf
            <button class="text-red-600">Logout</button>
        </form>
    </nav>
    @if(session('status'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">{{ session('status') }}</div>
    @endif
    @yield('content')
    <a class="text-green-600 mt-4" href="{{ route('admin.dashboard') }}">Back toDashboard</a>
  </div>
</body>
</html>
