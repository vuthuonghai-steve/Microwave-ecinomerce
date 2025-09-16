@extends('client.layout')
@section('title','Trang chủ')
@section('content')
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-2xl p-8 mb-12">
      <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-foreground mb-4 text-balance">
          Lò Vi Sóng Chính Hãng
          <span class="text-primary">Chất Lượng Cao</span>
        </h1>
        <p class="text-xl text-muted-foreground mb-8 text-pretty">
          Khám phá bộ sưu tập lò vi sóng hiện đại với công nghệ tiên tiến, 
          giá cả hợp lý và dịch vụ bảo hành tốt nhất.
        </p>
        
        <!-- Enhanced Search Form -->
        <form method="get" action="{{ route('products.index') }}" class="bg-card rounded-xl p-6 shadow-lg border border-border">
          <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="md:col-span-6">
              <input 
                type="text" 
                name="q" 
                placeholder="Tìm kiếm sản phẩm..." 
                class="w-full bg-input border border-border rounded-lg px-4 py-3 text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
              />
            </div>
            <div class="md:col-span-2">
              <input 
                type="number" 
                name="min_price" 
                placeholder="Giá từ" 
                class="w-full bg-input border border-border rounded-lg px-4 py-3 text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
              />
            </div>
            <div class="md:col-span-2">
              <input 
                type="number" 
                name="max_price" 
                placeholder="Giá đến" 
                class="w-full bg-input border border-border rounded-lg px-4 py-3 text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent transition-all"
              />
            </div>
            <div class="md:col-span-2">
              <button class="w-full bg-primary text-primary-foreground px-6 py-3 rounded-lg font-medium hover:bg-primary/90 transition-colors shadow-sm">
                Tìm kiếm
              </button>
            </div>
          </div>
        </form>
      </div>
       <!-- Featured Categories -->
      <div class="mb-12">
        <div class="flex items-center justify-between mb-8">
          
          <a class="inline-flex items-center text-primary hover:text-primary/80 transition-colors font-medium" href="{{ route('products.index') }}">
            Xem tất cả
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        </div>
        <!-- <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          @foreach($rootCategories as $c)
            <div class="group bg-card rounded-xl p-6 shadow-sm border border-border hover:shadow-md transition-all duration-300 hover:border-primary/20">
              <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors">
                  <a href="{{ route('products.index', ['category' => $c->slug]) }}">{{ $c->name }}</a>
                </h3>
                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                  <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                  </svg>
                </div>
              </div>
              @php $children = $categoriesByParent->get($c->id) ?? collect(); @endphp
              @if($children->isNotEmpty())
                <ul class="space-y-2">
                  @foreach($children->take(4) as $child)
                    <li>
                      <a class="text-sm text-muted-foreground hover:text-primary transition-colors" 
                        href="{{ route('products.index', ['category' => $child->slug]) }}">
                        {{ $child->name }}
                      </a>
                    </li>
                  @endforeach
                  @if($children->count() > 4)
                    <li class="text-sm text-muted-foreground">+{{ $children->count() - 4 }} khác</li>
                  @endif
                </ul>
              @endif
            </div>
          @endforeach
        </div> -->
      </div>

    </section>

   
    <!-- Best Selling Products -->
    <section class="mb-12">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h2 class="text-3xl font-bold text-foreground mb-2">Sản Phẩm Bán Chạy</h2>
          <p class="text-muted-foreground">Những sản phẩm được khách hàng yêu thích nhất</p>
        </div>
        <a class="inline-flex items-center text-primary hover:text-primary/80 transition-colors font-medium" href="{{ route('products.index', ['sort' => 'best_selling']) }}">
          Xem thêm
          <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      </div>
      <div id="bestGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($bestSelling as $product)
          @include('components.product-card', ['product' => $product])
        @endforeach
      </div>
    </section>

    <!-- Latest Products -->
    <section class="mb-12">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h2 class="text-3xl font-bold text-foreground mb-2">Sản Phẩm Mới</h2>
          <p class="text-muted-foreground">Cập nhật những mẫu lò vi sóng mới nhất</p>
        </div>
        <a class="inline-flex items-center text-primary hover:text-primary/80 transition-colors font-medium" href="{{ route('products.index') }}">
          Xem thêm
          <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      </div>
      <div id="latestGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($latest as $product)
          @include('components.product-card', ['product' => $product])
        @endforeach
      </div>
    </section>

    <!-- Features Section -->
    <section class="bg-card rounded-2xl p-8 border border-border">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-foreground mb-2">Tại Sao Chọn MicroStore?</h2>
        <p class="text-muted-foreground">Những lý do khách hàng tin tưởng chúng tôi</p>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center">
          <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-foreground mb-2">Chính Hãng 100%</h3>
          <p class="text-muted-foreground">Tất cả sản phẩm đều có tem chính hãng và bảo hành từ nhà sản xuất</p>
        </div>
        <div class="text-center">
          <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-foreground mb-2">Giá Tốt Nhất</h3>
          <p class="text-muted-foreground">Cam kết giá cả cạnh tranh và nhiều chương trình khuyến mãi hấp dẫn</p>
        </div>
        <div class="text-center">
          <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-foreground mb-2">Giao Hàng Nhanh</h3>
          <p class="text-muted-foreground">Giao hàng toàn quốc, nhanh chóng và an toàn đến tay khách hàng</p>
        </div>
      </div>
    </section>
  </div>

@push('scripts')
  <script>
    (async function(){
      try {
        const res = await fetch('/api/featured');
        if(!res.ok) return;
        const data = await res.json();
        const makeCard = (p) => {
          const finalPrice = p.sale_price ?? p.price;
          return `
          <div class="group bg-card rounded-xl shadow-sm border border-border hover:shadow-lg transition-all duration-300 overflow-hidden">
            <a href="/products/${p.slug}" class="block">
              ${p.thumbnail ? `
                <div class="aspect-square overflow-hidden">
                  <img src="${p.thumbnail}" alt="${p.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
              ` : `
                <div class="aspect-square bg-muted flex items-center justify-center">
                  <svg class="w-12 h-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
              `}
              <div class="p-4">
                <h3 class="font-medium text-foreground line-clamp-2 mb-2 group-hover:text-primary transition-colors">${p.name}</h3>
                <div class="flex items-center justify-between">
                  ${p.sale_price ? `
                    <div class="flex items-center space-x-2">
                      <span class="text-lg font-bold text-primary">${Number(finalPrice).toLocaleString('vi-VN')}₫</span>
                      <span class="text-sm text-muted-foreground line-through">${Number(p.price).toLocaleString('vi-VN')}₫</span>
                    </div>
                  ` : `
                    <span class="text-lg font-bold text-foreground">${Number(finalPrice).toLocaleString('vi-VN')}₫</span>
                  `}
                </div>
              </div>
            </a>
          </div>`;
        };
        const bestEl = document.getElementById('bestGrid');
        const latestEl = document.getElementById('latestGrid');
        if (bestEl && Array.isArray(data.best_selling)) {
          bestEl.innerHTML = data.best_selling.map(makeCard).join('');
        }
        if (latestEl && Array.isArray(data.latest)) {
          latestEl.innerHTML = data.latest.map(makeCard).join('');
        }
      } catch (e) {
        console.error('Failed to load featured products:', e);
      }
    })();
  </script>
@endpush
@endsection
