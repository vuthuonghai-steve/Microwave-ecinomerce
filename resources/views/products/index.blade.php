<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-7xl mx-auto p-4">
    <h1 class="text-2xl font-semibold mb-4">Danh sách sản phẩm</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <aside class="md:col-span-1">
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
      </aside>
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

        <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($products as $p)
                <a href="{{ route('products.show', $p->slug) }}" class="block bg-white rounded-md shadow hover:shadow-md transition">
                    @if($p->thumbnail)
                        <img src="{{ $p->thumbnail }}" alt="{{ $p->name }}" class="w-full h-40 object-cover rounded-t-md">
                    @endif
                    <div class="p-4">
                        <div class="text-sm text-gray-500">{{ $p->brand->name ?? '' }}</div>
                        <div class="font-medium">{{ $p->name }}</div>
                        <div class="mt-1">
                            @php $final = $p->sale_price ?? $p->price; @endphp
                            @if($p->sale_price)
                                <span class="text-red-600 font-semibold">{{ number_format($final,0,',','.') }}₫</span>
                                <span class="text-gray-400 line-through ml-2">{{ number_format($p->price,0,',','.') }}₫</span>
                            @else
                                <span class="text-gray-900 font-semibold">{{ number_format($final,0,',','.') }}₫</span>
                            @endif
                        </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $p->capacity_liters }}L • @if($p->inverter) Inverter • @endif @if($p->has_grill) Có nướng @endif</div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center text-gray-500">Không có sản phẩm phù hợp.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $products->links() }}</div>
      </div>
    </div>
</div>
</body>
</html>
