<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function getWishlist(int $userId): Wishlist
    {
        return Wishlist::firstOrCreate(['user_id' => $userId]);
    }

    public function index(Request $request)
    {
        $wishlist = $this->getWishlist($request->user()->id)->load('items.product.brand');
        return view('wishlist.index', ['items' => $wishlist->items]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
        ]);
        $wishlist = $this->getWishlist($request->user()->id);
        WishlistItem::firstOrCreate([
            'wishlist_id' => $wishlist->id,
            'product_id' => $data['product_id'],
        ]);
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đã thêm vào yêu thích']);
        }
        return back()->with('status','Đã thêm vào yêu thích');
    }

    public function remove(Request $request, int $id)
    {
        $wishlist = $this->getWishlist($request->user()->id);
        $wishlist->items()->whereKey($id)->delete();
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Đã xóa']);
        }
        return back()->with('status','Đã xóa khỏi yêu thích');
    }

    public function count(Request $request)
    {
        $wishlist = $this->getWishlist($request->user()->id)->loadCount('items');
        return response()->json(['count' => $wishlist->items_count]);
    }
}

