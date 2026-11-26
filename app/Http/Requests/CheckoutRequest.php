<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_type' => ['required', 'in:pickup,delivery'],
            'shipping_address' => ['required_if:shipping_type,delivery', 'nullable', 'string', 'max:500'],
            'shipping_city' => ['required_if:shipping_type,delivery', 'nullable', 'string', 'max:100'],
            'pickup_time' => ['required_if:shipping_type,pickup', 'nullable', 'string', 'max:100'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'customer_notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_email.required' => 'Email wajib diisi.',
            'customer_email.email' => 'Format email tidak valid.',
            'customer_phone.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'shipping_type.required' => 'Pilih metode pengiriman.',
            'shipping_address.required_if' => 'Alamat pengiriman wajib diisi untuk opsi Pengiriman Kurir.',
            'shipping_city.required_if' => 'Kota pengiriman wajib diisi untuk opsi Pengiriman Kurir.',
            'pickup_time.required_if' => 'Perkiraan waktu pengambilan wajib dipilih untuk opsi Ambil di Toko.',
            'payment_method_id.required' => 'Pilih salah satu metode pembayaran.',
            'payment_method_id.exists' => 'Metode pembayaran yang dipilih tidak valid.',
        ];
    }
}
