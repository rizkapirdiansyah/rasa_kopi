<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'store_name' => StoreSetting::get('store_name', 'RASA KOPI'),
            'store_tagline' => StoreSetting::get('store_tagline', 'Cita Rasa Kopi Nusantara Terbaik'),
            'store_phone' => StoreSetting::get('store_phone', '0812-3456-7890'),
            'store_whatsapp' => StoreSetting::get('store_whatsapp', '6281234567890'),
            'store_email' => StoreSetting::get('store_email', 'halo@rasakopi.com'),
            'store_address' => StoreSetting::get('store_address', 'Jl. Kopi Kenikmatan No. 18, Senopati, Jakarta Selatan'),
            'store_hours' => StoreSetting::get('store_hours', 'Senin - Minggu: 08:00 - 22:00 WIB'),
            'shipping_flat_rate' => StoreSetting::get('shipping_flat_rate', '10000'),
            'about_story' => StoreSetting::get('about_story', ''),
            'about_vision' => StoreSetting::get('about_vision', ''),
            'about_mission' => StoreSetting::get('about_mission', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:100'],
            'store_tagline' => ['required', 'string', 'max:255'],
            'store_phone' => ['required', 'string', 'max:50'],
            'store_whatsapp' => ['required', 'string', 'max:50'],
            'store_email' => ['required', 'email', 'max:100'],
            'store_address' => ['required', 'string', 'max:500'],
            'store_hours' => ['required', 'string', 'max:255'],
            'shipping_flat_rate' => ['required', 'numeric', 'min:0'],
            'about_story' => ['nullable', 'string'],
            'about_vision' => ['nullable', 'string'],
            'about_mission' => ['nullable', 'string'],
        ]);

        foreach ($validated as $key => $value) {
            StoreSetting::set($key, (string) $value);
        }

        return back()->with('success', 'Pengaturan toko berhasil diperbarui!');
    }
}
