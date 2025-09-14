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

    <form method="get" class="grid md:grid-cols-6 gap-3 bg-white p-4 rounded-md shadow mb-6">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm kiếm..." class="col-span-2 border rounded px-3 py-2">

        <select name="category" class="border rounded px-3 py-2">
            <option value="">Danh mục</option>
            @foreach($categories as $c)
                <option value="{{ $c->slug }}" @selected(request('category')===$c->slug)>{{ $c->name }}</option>
            @endforeach
        </select>

        <select name="brand" class="border rounded px-3 py-2">
            <option value="">Thương hiệu</option>
            @foreach($brands as $b)
                <option value="{{ $b->slug }}" @selected(request('brand')===$b->slug)>{{ $b->name }}</option>
            @endforeach
        </select>

        <input type="number" step="0.01" name="min_price" value="{{ request('min_price') }}" placeholder="Giá từ" class="border rounded px-3 py-2">
        <input type="number" step="0.01" name="max_price" value="{{ request('max_price') }}" placeholder="Giá đến" class="border rounded px-3 py-2">

        <div class="md:col-span-6 flex items-center gap-3">
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="inverter" value="1" @checked(request('inverter'))> Inverter</label>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="has_grill" value="1" @checked(request('has_grill'))> Có nướng</label>
            <select name="sort" class="border rounded px-3 py-2 ml-auto">
                <option value="latest" @selected(request('sort')==='latest')>Mới nhất</option>
                <option value="price_asc" @selected(request('sort')==='price_asc')>Giá tăng dần</option>
                <option value="price_desc" @selected(request('sort')==='price_desc')>Giá giảm dần</option>
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
</body>
</html>

