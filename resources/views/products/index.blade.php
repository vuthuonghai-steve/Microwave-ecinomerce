@extends('client.layout')
@section('title','Danh sách sản phẩm')
@section('content')
<div class="mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Danh sách sản phẩm</h1>

    <div class="flex items-center justify-center">
      <!-- <aside class="md:col-span-1">
        <div class="bg-white p-4 rounded shadow">
          <h2 class="font-semibold mb-2">Danh mục</h2>
          <ul class="space-y-1">
            @foreach($rootCategories as $root)
              <li>
                <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $root->slug])) }}" class="text-blue-600">{{ $root->name }}</a>
                @php $children = $categoriesByParent->get($root->id) ?? collect(); @endphp
                @if($children->isNotEmpty())
                  <ul class="pl-4 list-disc text-sm mt-1">
                    @foreach($children as $child)
                      <li><a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $child->slug])) }}" class="text-blue-600">{{ $child->name }}</a></li>
                    @endforeach
                  </ul>
                @endif
              </li>
            @endforeach
          </ul>
        </div>
      </aside> -->
      <div class="md:col-span-3">
        <form method="get" class="grid md:grid-cols-6 gap-3 bg-white p-4 rounded-md shadow mb-6">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm..." class="col-span-2 border rounded px-3 py-2">

            <select name="brand" class="border rounded px-3 py-2">
                <option value="">Thương hiệu</option>
                @foreach($brands as $b)
                    <option value="{{ $b->slug }}" @selected(request('brand')===$b->slug)>{{ $b->name }}</option>
                @endforeach
            </select>

            <input type="number" step="0.01" name="min_price" value="{{ request('min_price') }}" placeholder="Giá từ" class="border rounded px-3 py-2">
            <input type="number" step="0.01" name="max_price" value="{{ request('max_price') }}" placeholder="Giá đến" class="border rounded px-3 py-2">

            <input type="number" name="min_capacity" value="{{ request('min_capacity') }}" placeholder="Dung tích từ (L)" class="border rounded px-3 py-2">
            <input type="number" name="max_capacity" value="{{ request('max_capacity') }}" placeholder="Dung tích đến (L)" class="border rounded px-3 py-2">

            <input type="number" name="min_power" value="{{ request('min_power') }}" placeholder="Công suất từ (W)" class="border rounded px-3 py-2">
            <input type="number" name="max_power" value="{{ request('max_power') }}" placeholder="Công suất đến (W)" class="border rounded px-3 py-2">

            <input type="number" name="min_energy_rating" min="1" max="5" value="{{ request('min_energy_rating') }}" placeholder="Hiệu suất từ (1-5)" class="border rounded px-3 py-2">

            <div class="md:col-span-6 flex items-center gap-3">
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="inverter" value="1" @checked(request('inverter'))> Inverter</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="has_grill" value="1" @checked(request('has_grill'))> Có nướng</label>
                <label class="inline-flex items-center gap-2"><input type="checkbox" name="child_lock" value="1" @checked(request('child_lock'))> Khóa trẻ em</label>
                <select name="sort" class="border rounded px-3 py-2 ml-auto">
                    <option value="latest" @selected(request('sort')==='latest')>Mới nhất</option>
                    <option value="price_asc" @selected(request('sort')==='price_asc')>Giá tăng dần</option>
                    <option value="price_desc" @selected(request('sort')==='price_desc')>Giá giảm dần</option>
                    <option value="best_selling" @selected(request('sort')==='best_selling')>Bán chạy</option>
                </select>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Lọc</button>
            </div>
        </form>

        <div id="productGrid" class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($products as $p)
                @include('components.product-card', ['product' => $p])
            @empty
                <div class="col-span-full text-center text-gray-500">Không có sản phẩm phù hợp.</div>
            @endforelse
        </div>

        <div class="mt-6 text-center">
          <button id="loadMore" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">Tải thêm</button>
        </div>

        <button id="scrollTopBtn" title="Lên đầu trang" class="hidden fixed bottom-6 right-6 bg-blue-600 text-white rounded-full w-12 h-12 shadow-lg">↑</button>
      </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
  (function(){
    const grid = document.getElementById('productGrid');
    const loadBtn = document.getElementById('loadMore');
    if(!grid || !loadBtn) return;
    let page = {{ $products->currentPage() }};
    const lastPage = {{ $products->lastPage() }};
    const baseQuery = new URLSearchParams(window.location.search);
    const buildCard = (p) => {
      const finalPrice = p.sale_price ?? p.price;
      return `
        <a href="/products/${p.slug}" class="block bg-white rounded-md shadow hover:shadow-md transition">
          ${p.thumbnail ? `<img src="${p.thumbnail}" alt="${p.name}" class="w-full h-40 object-cover rounded-t-md">` : ''}
          <div class="p-4">
            <div class="font-medium line-clamp-2">${p.name}</div>
            <div class="mt-1">
              ${p.sale_price ? `<span class=\"text-red-600 font-semibold\">${Number(finalPrice).toLocaleString('vi-VN')}₫</span>
              <span class=\"text-gray-400 line-through ml-2\">${Number(p.price).toLocaleString('vi-VN')}₫</span>`
              : `<span class=\"text-gray-900 font-semibold\">${Number(finalPrice).toLocaleString('vi-VN')}₫</span>`}
            </div>
          </div>
        </a>`;
    };
    const fetchNext = async () => {
      if (page >= lastPage) { loadBtn.disabled = true; loadBtn.textContent = 'Hết sản phẩm'; return; }
      page += 1;
      const params = new URLSearchParams(baseQuery);
      params.set('page', page);
      const res = await fetch('/api/products?' + params.toString());
      const json = await res.json();
      const items = json.data || [];
      const html = items.map(buildCard).join('');
      grid.insertAdjacentHTML('beforeend', html);
      if (page >= (json.meta?.last_page || lastPage)) { loadBtn.disabled = true; loadBtn.textContent = 'Hết sản phẩm'; }
      const topBtn = document.getElementById('scrollTopBtn');
      if (topBtn) topBtn.classList.remove('hidden');
    };
    loadBtn.addEventListener('click', fetchNext);
    window.addEventListener('scroll', () => {
      if (loadBtn.disabled) return;
      const nearBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 200;
      if (nearBottom) fetchNext();
    });
    const topBtn = document.getElementById('scrollTopBtn');
    if (topBtn) {
      topBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
      window.addEventListener('scroll', () => {
        if (window.scrollY > 400) topBtn.classList.remove('hidden'); else topBtn.classList.add('hidden');
      });
    }
  })();
</script>
@endpush
