<div class="group bg-card rounded-xl shadow-sm border border-border hover:shadow-lg transition-all duration-300 overflow-hidden">
  <a href="{{ route('products.show', $product->slug) }}" class="block">
    @if($product->thumbnail)
      <div class="aspect-square overflow-hidden">
        <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
      </div>
    @else
      <div class="aspect-square bg-muted flex items-center justify-center">
        <svg class="w-12 h-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
    @endif
    
    <div class="p-4">
      <h3 class="font-medium text-foreground line-clamp-2 mb-2 group-hover:text-primary transition-colors">{{ $product->name }}</h3>
      
      @if($product->brand)
        <p class="text-sm text-muted-foreground mb-2">{{ $product->brand->name }}</p>
      @endif
      
      <div class="flex items-center justify-between mb-3">
        @if($product->sale_price)
          <div class="flex items-center space-x-2">
            <span class="text-lg font-bold text-primary">{{ number_format($product->sale_price, 0, ',', '.') }}₫</span>
            <span class="text-sm text-muted-foreground line-through">{{ number_format($product->price, 0, ',', '.') }}₫</span>
          </div>
          <div class="bg-destructive text-destructive-foreground text-xs px-2 py-1 rounded-full">
            -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
          </div>
        @else
          <span class="text-lg font-bold text-foreground">{{ number_format($product->price, 0, ',', '.') }}₫</span>
        @endif
      </div>
      
      @if($product->capacity_liters)
        <div class="flex items-center text-sm text-muted-foreground mb-2">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
          {{ $product->capacity_liters }}L
        </div>
      @endif
      
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-1">
          @for($i = 1; $i <= 5; $i++)
            <svg class="w-4 h-4 {{ $i <= ($product->energy_rating ?? 4) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
          @endfor
        </div>
        
        @if($product->warranty_months)
          <span class="text-xs text-muted-foreground">BH {{ $product->warranty_months }}T</span>
        @endif
      </div>
    </div>
  </a>
</div>
