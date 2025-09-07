<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;
use Illuminate\Support\Facades\Hash;

class SellerProfileController extends Controller
{
    public function edit()
    {
        $seller = Seller::findOrFail(session('seller_id'));
        return view('seller.profile.edit', compact('seller'));
    }

    public function update(Request $request)
    {
        $seller = Seller::findOrFail(session('seller_id'));

        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:sellers,email,' . $seller->id,
            'password' => 'nullable|min:6',
            'shop_name'=> 'required',
        ]);

        $seller->name = $request->name;
        $seller->email = $request->email;
        $seller->shop_name = $request->shop_name;

        if ($request->password) {
            $seller->password = Hash::make($request->password);
        }

        $seller->save();

        return redirect()->route('seller.profile')->with('success', 'Profile updated successfully');
    }
}
