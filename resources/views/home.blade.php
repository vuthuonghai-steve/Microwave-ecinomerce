@extends('client.layout')
@section('title','Trang chủ')
@section('content')
  <div class="max-w-7xl mx-auto p-4">
    <header class="bg-white rounded shadow p-6 mb-6">
      <h1 class="text-2xl font-semibold">Microwave E-commerce</h1>
      <p class="text-gray-600">Mua sắm lò vi sóng chính hãng, giá tốt.</p>
      <form method="get" action="{{ route('products.index') }}" class="mt-4 grid md:grid-cols-6 gap-3">
        <input type="text" name="q" placeholder="Tìm sản phẩm..." class="md:col-span-3 border rounded px-3 py-2" />
        <input type="number" name="min_price" placeholder="Giá từ" class="border rounded px-3 py-2" />
        <input type="number" name="max_price" placeholder="Giá đến" class="border rounded px-3 py-2" />
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Tìm kiếm</button>
      </form>
    </header>

    <section class="mb-8">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold">Danh mục nổi bật</h2>
        <a class="text-blue-600" href="{{ route('products.index') }}">Xem tất cả</a>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4">
        @foreach($rootCategories as $c)
          <div class="bg-white rounded shadow p-3">
            <a href="{{ route('products.index', ['category' => $c->slug]) }}" class="font-medium text-blue-600">{{ $c->name }}</a>
            @php $children = $categoriesByParent->get($c->id) ?? collect(); @endphp
            @if($children->isNotEmpty())
              <ul class="mt-2 text-sm list-disc list-inside text-gray-700">
                @foreach($children->take(6) as $child)
                  <li><a class="text-blue-600" href="{{ route('products.index', ['category' => $child->slug]) }}">{{ $child->name }}</a></li>
                @endforeach
              </ul>
            @endif
          </div>
        @endforeach
      </div>
    </section>

    <section class="mb-8">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold">Bán chạy</h2>
        <a class="text-blue-600" href="{{ route('products.index', ['sort' => 'best_selling']) }}">Xem thêm</a>
      </div>
      <div id="bestGrid" class="grid sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
        @foreach($bestSelling as $product)
          @include('components.product-card', ['product' => $product])
        @endforeach
      </div>
    </section>

    <section class="mb-8">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold">Mới về</h2>
        <a class="text-blue-600" href="{{ route('products.index') }}">Xem thêm</a>
      </div>
      <div id="latestGrid" class="grid sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
        @foreach($latest as $product)
          @include('components.product-card', ['product' => $product])
        @endforeach
      </div>
    </section>
  </div>
@endsection

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
          <a href="/products/${p.slug}" class="block bg-white rounded-md shadow hover:shadow-md transition">
            ${p.thumbnail ? `<img src="${p.thumbnail}" alt="${p.name}" class="w-full h-40 object-cover rounded-t-md">` : ''}
            <div class="p-4">
              <div class="font-medium line-clamp-2">${p.name}</div>
              <div class="mt-1">
                ${p.sale_price ? `<span class="text-red-600 font-semibold">${Number(finalPrice).toLocaleString('vi-VN')}₫</span>
                <span class="text-gray-400 line-through ml-2">${Number(p.price).toLocaleString('vi-VN')}₫</span>`
                : `<span class="text-gray-900 font-semibold">${Number(finalPrice).toLocaleString('vi-VN')}₫</span>`}
              </div>
            </div>
          </a>`;
        };
        const bestEl = document.getElementById('bestGrid');
        const latestEl = document.getElementById('latestGrid');
        if (bestEl && Array.isArray(data.best_selling)) {
          bestEl.innerHTML = data.best_selling.map(makeCard).join('');
        }
        if (latestEl && Array.isArray(data.latest)) {
          latestEl.innerHTML = data.latest.map(makeCard).join('');
        }
      } catch (e) { /* silent */ }
    })();
  </script>
@endpush
