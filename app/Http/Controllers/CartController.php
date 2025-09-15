<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function getActiveCart(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId, 'status' => 'active']);
    }

    public function index(Request $request)
    {
        $cart = $this->getActiveCart($request->user()->id)->load('items.product');
        $items = $cart->items;
        $subtotal = 0;
        foreach ($items as $it) {
            $subtotal += $it->price_snapshot * $it->quantity;
        }
        return view('cart.index', [
            'cart' => $cart,
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'quantity' => ['nullable','integer','min:1']
        ]);
        $qty = (int)($data['quantity'] ?? 1);
        $userId = $request->user()->id;
        $product = Product::where('is_active', true)->findOrFail($data['product_id']);
        $stock = ProductStock::firstOrNew(['product_id' => $product->id]);
        $available = max(0, ($stock->stock_on_hand ?? 0) - ($stock->stock_reserved ?? 0));
        if ($available <= 0) {
            return back()->withErrors(['cart' => 'Sản phẩm đã hết hàng.']);
        }
        $cart = $this->getActiveCart($userId);
        $finalPrice = $product->sale_price ?? $product->price;

        $item = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();
        if ($item) {
            $item->update(['quantity' => \DB::raw('quantity + '.(int)$qty)]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'price_snapshot' => $finalPrice,
                'quantity' => $qty,
            ]);
        }

        return redirect()->route('cart.index')->with('status', 'Đã thêm vào giỏ hàng');
    }

    public function updateItem(Request $request, int $id)
    {
        $data = $request->validate(['quantity' => ['required','integer','min:1']]);
        $cart = $this->getActiveCart($request->user()->id);
        $item = $cart->items()->whereKey($id)->with('product')->firstOrFail();
        $stock = ProductStock::firstOrNew(['product_id' => $item->product_id]);
        $available = max(0, ($stock->stock_on_hand ?? 0) - ($stock->stock_reserved ?? 0));
        if ($available < (int)$data['quantity']) {
            return back()->withErrors(['cart' => 'Số lượng vượt quá tồn kho khả dụng.']);
        }
        $item->update(['quantity' => (int)$data['quantity']]);
        return back()->with('status', 'Đã cập nhật số lượng');
    }

    public function removeItem(Request $request, int $id)
    {
        $cart = $this->getActiveCart($request->user()->id);
        $cart->items()->whereKey($id)->delete();
        return back()->with('status', 'Đã xóa sản phẩm khỏi giỏ');
    }

    public function count(Request $request)
    {
        $cart = $this->getActiveCart($request->user()->id)->loadCount('items');
        return response()->json(['count' => $cart->items_count]);
    }
}
