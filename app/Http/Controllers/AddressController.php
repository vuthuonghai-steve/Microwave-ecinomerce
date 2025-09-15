<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $addresses = Address::where('user_id', $request->user()->id)->latest()->paginate(10);
        return view('addresses.index', compact('addresses'));
    }

    public function create()
    {
        return view('addresses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'phone' => ['required','string','max:50'],
            'line1' => ['required','string','max:255'],
            'district' => ['required','string','max:255'],
            'city' => ['required','string','max:255'],
            'country_code' => ['nullable','string','size:2'],
            'is_default' => ['nullable','boolean'],
        ]);
        $data['user_id'] = $request->user()->id;
        $data['country_code'] = $data['country_code'] ?? 'VN';
        $data['is_default'] = (bool)($data['is_default'] ?? false);
        if ($data['is_default']) {
            Address::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }
        Address::create($data);
        return redirect()->route('addresses.index')->with('status','Đã thêm địa chỉ');
    }

    public function edit(Request $request, int $id)
    {
        $address = Address::where('user_id', $request->user()->id)->findOrFail($id);
        return view('addresses.edit', compact('address'));
    }

    public function update(Request $request, int $id)
    {
        $address = Address::where('user_id', $request->user()->id)->findOrFail($id);
        $data = $request->validate([
            'full_name' => ['required','string','max:255'],
            'phone' => ['required','string','max:50'],
            'line1' => ['required','string','max:255'],
            'district' => ['required','string','max:255'],
            'city' => ['required','string','max:255'],
            'country_code' => ['nullable','string','size:2'],
            'is_default' => ['nullable','boolean'],
        ]);
        $data['country_code'] = $data['country_code'] ?? 'VN';
        $data['is_default'] = (bool)($data['is_default'] ?? false);
        if ($data['is_default']) {
            Address::where('user_id', $request->user()->id)->where('id','<>',$address->id)->update(['is_default' => false]);
        }
        $address->update($data);
        return redirect()->route('addresses.index')->with('status','Đã cập nhật địa chỉ');
    }

    public function destroy(Request $request, int $id)
    {
        $address = Address::where('user_id', $request->user()->id)->findOrFail($id);
        $address->delete();
        return back()->with('status','Đã xóa địa chỉ');
    }
}

