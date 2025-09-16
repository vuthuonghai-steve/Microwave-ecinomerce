<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Microwave E-commerce')</title>
    @vite(['resources/css/app.css','resources/js/app.js','resources/css/globals.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-background text-foreground font-sans antialiased">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-card/95 backdrop-blur supports-[backdrop-filter]:bg-card/60 border-b border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-foreground">MicroStore</span>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-foreground hover:text-primary transition-colors">Trang chủ</a>
                    <a href="{{ route('products.index') }}" class="text-foreground hover:text-primary transition-colors">Sản phẩm</a>
                    <a href="#" class="text-foreground hover:text-primary transition-colors">Thương hiệu</a>
                    <a href="#" class="text-foreground hover:text-primary transition-colors">Hỗ trợ</a>
                </nav>

                <!-- User Actions -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-foreground hover:text-primary transition-colors">
                                <div class="w-8 h-8 bg-muted rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-card rounded-lg shadow-lg border border-border opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <div class="py-2">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-foreground hover:bg-muted transition-colors">Hồ sơ</a>
                                    <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-foreground hover:bg-muted transition-colors">Đơn hàng</a>
                                    <a href="{{ route('addresses.index') }}" class="block px-4 py-2 text-sm text-foreground hover:bg-muted transition-colors">Địa chỉ</a>
                                    <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-sm text-foreground hover:bg-muted transition-colors">Yêu thích</a>
                                    <hr class="my-2 border-border">
                                    <form method="post" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-destructive hover:bg-muted transition-colors">Đăng xuất</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-foreground hover:text-primary transition-colors">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="bg-primary text-primary-foreground px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">Đăng ký</a>
                    @endauth
                    
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-foreground hover:text-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0h8"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-primary text-primary-foreground text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-card border-t border-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-foreground">MicroStore</span>
                    </div>
                    <p class="text-muted-foreground mb-4">Chuyên cung cấp lò vi sóng chính hãng, chất lượng cao với giá cả hợp lý. Đảm bảo uy tín và dịch vụ tốt nhất cho khách hàng.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-muted-foreground hover:text-primary transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-muted-foreground hover:text-primary transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-foreground mb-4">Sản phẩm</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Lò vi sóng</a></li>
                        <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Phụ kiện</a></li>
                        <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Bảo hành</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-foreground mb-4">Hỗ trợ</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Liên hệ</a></li>
                        <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">FAQ</a></li>
                        <li><a href="#" class="text-muted-foreground hover:text-primary transition-colors">Chính sách</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-border mt-8 pt-8 text-center text-muted-foreground">
                <p>&copy; 2024 MicroStore. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
