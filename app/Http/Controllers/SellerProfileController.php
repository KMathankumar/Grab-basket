<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Seller; // Add this import

class SellerProfileController extends Controller
{
    /**
     * Show the seller profile form.
     */
    public function edit()
    {
        $seller = Auth::guard('seller')->user();

        return view('seller.profile', compact('seller'));
    }

    /**
     * Update the seller's profile.
     */
    public function update(Request $request)
    {
        /** @var Seller $seller */
        $seller = Auth::guard('seller')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:sellers,email,' . $seller->id,
            'phone' => 'nullable|string|max:20',
            'shop_name' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Update basic fields
        $data = $request->only('name', 'email', 'phone', 'shop_name');

        // Handle photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($seller->profile_photo && file_exists(public_path('storage/' . $seller->profile_photo))) {
                unlink(public_path('storage/' . $seller->profile_photo));
            }

            $path = $request->file('profile_photo')->store('seller-photos', 'public');
            $data['profile_photo'] = $path;
        }

        $seller->update($data);

        // Update password if provided
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $seller->password)) {
                return back()->withErrors([
                    'current_password' => 'The current password is incorrect.'
                ])->withInput($request->except('current_password', 'new_password', 'new_password_confirmation'));
            }

            $seller->update(['password' => Hash::make($request->new_password)]);
        }

        return back()->with('success', '✅ Profile updated successfully!');
    }
}