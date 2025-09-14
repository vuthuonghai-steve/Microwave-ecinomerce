<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AddressController extends Controller
{
    protected function resolveUserId(Request $request): int
    {
        $id = (int) auth()->id();
        if (!$id) {
            throw ValidationException::withMessages(['user' => 'Unauthenticated']);
        }
        return $id;
    }

    public function index(Request $request)
    {
        $userId = $this->resolveUserId($request);
        return Address::where('user_id', $userId)->orderByDesc('is_default')->get();
    }
}
