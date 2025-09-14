@php($final = $product->sale_price ?? $product->price)
<a href="{{ route('products.show', $product->slug) }}" class="block bg-white rounded-md shadow hover:shadow-md transition">
  @if($product->thumbnail)
    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-40 object-cover rounded-t-md">
  @endif
  <div class="p-4">
    @if(!empty($product->brand?->name))
      <div class="text-sm text-gray-500">{{ $product->brand->name }}</div>
    @endif
    <div class="font-medium line-clamp-2">{{ $product->name }}</div>
    <div class="mt-1">
      @if($product->sale_price)
        <span class="text-red-600 font-semibold">{{ number_format($final,0,',','.') }}₫</span>
        <span class="text-gray-400 line-through ml-2">{{ number_format($product->price,0,',','.') }}₫</span>
      @else
        <span class="text-gray-900 font-semibold">{{ number_format($final,0,',','.') }}₫</span>
      @endif
    </div>
    <div class="text-xs text-gray-500 mt-1">{{ $product->capacity_liters }}L @if($product->inverter) • Inverter @endif @if($product->has_grill) • Có nướng @endif</div>
  </div>
</a>

