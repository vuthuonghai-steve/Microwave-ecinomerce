<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-5xl mx-auto p-4">
    <a href="{{ route('products.index') }}" class="text-blue-600">← Quay lại danh sách</a>

    <div class="grid md:grid-cols-2 gap-6 mt-4 bg-white p-4 rounded-md shadow">
        <div>
            @if($product->thumbnail)
                <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-80 object-cover rounded">
            @else
                <div class="w-full h-80 bg-gray-100 rounded"></div>
            @endif
        </div>
        <div>
            <h1 class="text-2xl font-semibold">{{ $product->name }}</h1>
            <div class="text-sm text-gray-500 mt-1">
                {{ $product->brand->name ?? '' }} • {{ $product->category->name ?? '' }}
            </div>
            <div class="mt-3 text-xl">
                @php $final = $product->sale_price ?? $product->price; @endphp
                @if($product->sale_price)
                    <span class="text-red-600 font-semibold">{{ number_format($final,0,',','.') }}₫</span>
                    <span class="text-gray-400 line-through ml-2">{{ number_format($product->price,0,',','.') }}₫</span>
                @else
                    <span class="text-gray-900 font-semibold">{{ number_format($final,0,',','.') }}₫</span>
                @endif
            </div>
            <ul class="mt-4 text-sm text-gray-700 list-disc list-inside">
                <li>Dung tích: {{ $product->capacity_liters }}L</li>
                @if($product->power_watt)<li>Công suất: {{ $product->power_watt }}W</li>@endif
                <li>@if($product->inverter) Có @else Không @endif Inverter</li>
                <li>@if($product->has_grill) Có @else Không @endif chức năng nướng</li>
                @if(!is_null($product->energy_rating))<li>Hiệu suất năng lượng: {{ $product->energy_rating }}/5</li>@endif
                <li>Bảo hành: {{ $product->warranty_months }} tháng</li>
                @if($product->stock)
                    <li>Tồn kho: {{ $product->stock->stock_on_hand - $product->stock->stock_reserved }}</li>
                @endif
            </ul>
            @if($product->description)
                <div class="mt-4 prose max-w-none">{!! nl2br(e($product->description)) !!}</div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
@if(isset($related) && $related->count())
<!DOCTYPE html>
<html lang="vi">
<body>
<div class="max-w-5xl mx-auto p-4">
  <h2 class="text-xl font-semibold mb-3">Sản phẩm liên quan</h2>
  <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6">
    @foreach($related as $product)
      @include('components.product-card', ['product' => $product])
    @endforeach
  </div>
</div>
</body>
</html>
@endif
