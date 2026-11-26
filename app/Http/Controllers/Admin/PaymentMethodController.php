<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(): View
    {
        $methods = PaymentMethod::all();
        return view('admin.payment-methods.index', compact('methods'));
    }

    public function edit(PaymentMethod $paymentMethod): View
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'account_name' => ['nullable', 'string', 'max:100'],
            'instructions' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Metode pembayaran berhasil diperbarui!');
    }

    public function toggle(PaymentMethod $paymentMethod): RedirectResponse
    {
        $paymentMethod->is_active = ! $paymentMethod->is_active;
        $paymentMethod->save();

        $status = $paymentMethod->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Metode pembayaran {$paymentMethod->name} berhasil {$status}.");
    }
}
