<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return view('profile.index', [
            'user' => $user,
            'orders' => $user->orders()->latest()->take(10)->get(),
            'addresses' => $user->addresses()->latest()->get(),
            'paymentMethods' => $user->paymentMethods()->latest()->get(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:30',
            'dob' => 'nullable|date',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $user->name = trim($request->input('first_name') . ' ' . $request->input('last_name'));
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->dob = $request->input('dob');

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Your profile has been updated successfully.');
    }

    public function deletePicture()
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            if (Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->update(['profile_picture' => null]);
        }

        return redirect()->route('profile')->with('success', 'Profile picture removed successfully.');
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        $address = Auth::user()->addresses()->create($request->only([
            'label',
            'line1',
            'line2',
            'city',
            'state',
            'postal_code',
            'country',
        ]));

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        }

        return redirect()->route('profile')->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'label' => 'required|string|max:50',
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        $address->update($request->only([
            'label',
            'line1',
            'line2',
            'city',
            'state',
            'postal_code',
            'country',
        ]));

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        }

        return redirect()->route('profile')->with('success', 'Address updated successfully.');
    }

    public function deleteAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return redirect()->route('profile')->with('success', 'Address removed successfully.');
    }

    public function storePaymentMethod(Request $request)
    {
        $request->validate([
            'card_brand' => 'required|string|max:50',
            'card_holder_name' => 'required|string|max:100',
            'card_number' => 'required|digits_between:12,19',
            'expiry_month' => 'required|integer|min:1|max:12',
            'expiry_year' => 'required|integer|min:' . date('Y') . '|max:' . (date('Y') + 20),
            'is_default' => 'nullable|boolean',
        ]);

        $payment = Auth::user()->paymentMethods()->create([
            'card_brand' => $request->input('card_brand'),
            'card_holder_name' => $request->input('card_holder_name'),
            'last_four' => substr($request->input('card_number'), -4),
            'expiry_month' => $request->input('expiry_month'),
            'expiry_year' => $request->input('expiry_year'),
            'is_default' => $request->boolean('is_default'),
        ]);

        if ($payment->is_default) {
            Auth::user()->paymentMethods()->where('id', '!=', $payment->id)->update(['is_default' => false]);
        }

        return redirect()->route('profile')->with('success', 'Payment method added successfully.');
    }

    public function updatePaymentMethod(Request $request, PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'card_brand' => 'required|string|max:50',
            'card_holder_name' => 'required|string|max:100',
            'expiry_month' => 'required|integer|min:1|max:12',
            'expiry_year' => 'required|integer|min:' . date('Y') . '|max:' . (date('Y') + 20),
            'is_default' => 'nullable|boolean',
        ]);

        $paymentMethod->update([
            'card_brand' => $request->input('card_brand'),
            'card_holder_name' => $request->input('card_holder_name'),
            'expiry_month' => $request->input('expiry_month'),
            'expiry_year' => $request->input('expiry_year'),
            'is_default' => $request->boolean('is_default'),
        ]);

        if ($paymentMethod->is_default) {
            Auth::user()->paymentMethods()->where('id', '!=', $paymentMethod->id)->update(['is_default' => false]);
        }

        return redirect()->route('profile')->with('success', 'Payment method updated successfully.');
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403);
        }

        $paymentMethod->delete();

        return redirect()->route('profile')->with('success', 'Payment method removed successfully.');
    }

    public function reorder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $newOrder = Auth::user()->orders()->create([
            'order_number' => Str::upper('ORD-' . Str::random(8)),
            'customer_name' => Auth::user()->name,
            'customer_email' => Auth::user()->email,
            'customer_phone' => Auth::user()->phone ?? 'N/A',
            'order_type' => 'pickup',
            'fulfillment_date' => now()->toDateString(),
            'total' => $order->total_amount,
            'subtotal' => $order->total_amount,
            'status' => 'pending',
            'payment_status' => 'pending',
            'special_instructions' => 'Reorder of ' . $order->order_number,
        ]);

        return redirect()->route('profile')->with('success', 'Your reorder has been placed. Order ' . $newOrder->order_number . ' created.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // If user has a password set (not just Google login), require current password
        $hasPassword = !empty($user->password);

        $rules = [
            'password' => 'required|string|min:8|confirmed',
        ];

        if ($hasPassword && !$user->google_id) {
            $rules['current_password'] = ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The provided password does not match your current password.');
                }
            }];
        }

        $request->validate($rules);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile')->with('success', 'Password updated successfully.');
    }
}
