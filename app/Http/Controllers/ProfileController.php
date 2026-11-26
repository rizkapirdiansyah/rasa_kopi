<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $addresses = $user->addresses()->latest()->get();
        $recentOrders = $user->orders()->latest()->take(5)->get();

        return view('pages.profile.show', compact('user', 'addresses', 'recentOrders'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', 'string'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'current_password.required_with' => 'Masukkan password saat ini untuk mengganti password.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($request->filled('password')) {
            if (! Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->save();

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();

        if ($request->boolean('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            'label' => $validated['label'],
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address_line' => $validated['address_line'],
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'Alamat baru berhasil ditambahkan!');
    }

    public function deleteAddress(int $id): RedirectResponse
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        $address->delete();

        return back()->with('success', 'Alamat berhasil dihapus.');
    }
}
