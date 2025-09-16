<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title','Dashboard')</title>
    @vite(['resources/css/app.css','resources/js/app.js','resources/css/globals.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-background text-foreground font-sans antialiased">
<div class="flex h-screen">
  <!-- Enhanced Sidebar -->
  <div class="flex h-screen w-64 flex-col bg-sidebar border-r border-sidebar-border">
    <!-- Logo Section -->
    <div class="flex items-center justify-center h-16 border-b border-sidebar-border">
      <div class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-sidebar-primary rounded-lg flex items-center justify-center">
          <svg class="w-5 h-5 text-sidebar-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
        </div>
        <span class="text-lg font-bold text-sidebar-foreground">Admin Panel</span>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2">
      <a href="{{ route('admin.dashboard') }}" 
         class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21l8-8-8-8"/>
        </svg>
        <span>Dashboard</span>
      </a>

      <a href="{{ route('admin.reports.index') }}" 
         class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <span>Báo cáo</span>
      </a>

      <a href="{{ route('admin.products.index') }}" 
         class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <span>Sản phẩm</span>
      </a>

      <a href="{{ route('admin.orders.index') }}" 
         class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <span>Đơn hàng</span>
      </a>

      <a href="{{ route('admin.categories.index') }}" 
         class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <span>Danh mục</span>
      </a>

      <a href="{{ route('admin.brands.index') }}" 
         class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground transition-colors {{ request()->routeIs('admin.brands.*') ? 'bg-sidebar-primary text-sidebar-primary-foreground' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <span>Thương hiệu</span>
      </a>
    </nav>

    <!-- User Section -->
    <div class="border-t border-sidebar-border p-4">
      <div class="flex items-center space-x-3 mb-3">
        <div class="w-8 h-8 bg-sidebar-accent rounded-full flex items-center justify-center">
          <span class="text-sm font-medium text-sidebar-accent-foreground">A</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-sidebar-foreground truncate">Admin User</p>
          <p class="text-xs text-muted-foreground truncate">admin@example.com</p>
        </div>
      </div>
      
      <form method="post" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="flex items-center space-x-2 w-full px-3 py-2 text-sm text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground rounded-lg transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          <span>Đăng xuất</span>
        </button>
      </form>
    </div>
  </div>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col overflow-hidden">
    <!-- Header -->
    <header class="bg-card border-b border-border px-6 py-4">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-foreground">@yield('title', 'Dashboard')</h1>
        <div class="flex items-center space-x-4">
          <button class="p-2 text-muted-foreground hover:text-foreground transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5z"/>
            </svg>
          </button>
          <button class="p-2 text-muted-foreground hover:text-foreground transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </button>
        </div>
      </div>
    </header>

    <!-- Content Area -->
    <main class="flex-1 overflow-y-auto p-6">
      @if(session('status'))
        <div class="bg-primary/10 border border-primary/20 text-primary px-4 py-3 rounded-lg mb-6 flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ session('status') }}
        </div>
      @endif

      @yield('content')
    </main>
  </div>
</div>

@stack('scripts')
</body>
</html>
