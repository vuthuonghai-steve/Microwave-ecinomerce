<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    protected function resolveUserId(Request $request): int
    {
        $id = (int) auth()->id();
        if (!$id) {
            throw ValidationException::withMessages(['user' => 'Unauthenticated']);
        }
        return $id;
    }

    protected function getActiveCart(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId, 'status' => 'active']);
    }

    public function show(Request $request)
    {
        $userId = $this->resolveUserId($request);
        $cart = $this->getActiveCart($userId)->load(['items.product:id,name,slug,price,sale_price,thumbnail']);

        $items = $cart->items->map(function (CartItem $it) {
            $final = $it->price_snapshot;
            return [
                'id' => $it->id,
                'product' => [
                    'id' => $it->product->id,
                    'name' => $it->product->name,
                    'slug' => $it->product->slug,
                    'thumbnail' => $it->product->thumbnail,
                ],
                'price_snapshot' => (float)$it->price_snapshot,
                'quantity' => $it->quantity,
                'total' => (float)($final * $it->quantity),
            ];
        })->values();

        $subtotal = $items->sum('total');
        $shipping = 0; // at cart stage
        $grand = $subtotal + $shipping;

        return response()->json([
            'data' => [
                'items' => $items,
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'grand_total' => $grand,
            ],
        ]);
    }

    public function addItem(Request $request)
    {
        $userId = $this->resolveUserId($request);
        $validated = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'quantity' => ['nullable','integer','min:1'],
        ]);

        $qty = (int)($validated['quantity'] ?? 1);
        $product = Product::where('is_active', true)->findOrFail($validated['product_id']);

        // Check stock available per CO-02
        $stock = ProductStock::firstOrNew(['product_id' => $product->id]);
        $available = max(0, ($stock->stock_on_hand ?? 0) - ($stock->stock_reserved ?? 0));
        if ($available <= 0) {
            throw ValidationException::withMessages(['product_id' => 'Product out of stock']);
        }

        $cart = $this->getActiveCart($userId);

        $finalPrice = $product->sale_price ?? $product->price;

        $item = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();
        if ($item) {
            // Increase quantity; keep original price_snapshot per CO-03
            $item->update(['quantity' => DB::raw('quantity + '.(int)$qty)]);
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'price_snapshot' => $finalPrice,
                'quantity' => $qty,
            ]);
        }

        return response()->json(['message' => 'Added to cart', 'item_id' => $item->id], 201);
    }

    public function updateItem(Request $request, int $itemId)
    {
        $userId = $this->resolveUserId($request);
        $validated = $request->validate([
            'quantity' => ['required','integer','min:1']
        ]);

        $cart = $this->getActiveCart($userId);
        $item = $cart->items()->whereKey($itemId)->with('product')->firstOrFail();

        // Validate stock availability for new quantity
        $stock = ProductStock::firstOrNew(['product_id' => $item->product_id]);
        $available = max(0, ($stock->stock_on_hand ?? 0) - ($stock->stock_reserved ?? 0));
        if ($available < (int)$validated['quantity']) {
            throw ValidationException::withMessages(['quantity' => 'Insufficient stock for requested quantity']);
        }

        $item->update(['quantity' => (int)$validated['quantity']]);
        return response()->json(['message' => 'Updated']);
    }

    public function removeItem(Request $request, int $itemId)
    {
        $userId = $this->resolveUserId($request);
        $cart = $this->getActiveCart($userId);
        $deleted = $cart->items()->whereKey($itemId)->delete();
        return response()->json(['message' => $deleted ? 'Removed' : 'Not found']);
    }
}
