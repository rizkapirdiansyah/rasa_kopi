<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\PaymentMethod;
use App\Models\StoreSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Admin if not exists
        User::firstOrCreate(
            ['email' => 'admin@rasakopi.com'],
            [
                'name' => 'Admin Rasa Kopi',
                'role' => 'admin',
                'phone' => '081234567890',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Create Default Customer if not exists
        User::firstOrCreate(
            ['email' => 'budi@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'role' => 'pelanggan',
                'phone' => '081298765432',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 3. Store Settings
        $settings = [
            'store_name' => 'RASA KOPI',
            'store_tagline' => 'Cita Rasa Kopi Asli Nusantara & Pengalaman Kopi Terbaik',
            'store_phone' => '0812-3456-7890',
            'store_whatsapp' => '6281234567890',
            'store_email' => 'halo@rasakopi.com',
            'store_address' => 'Jl. Kopi Kenikmatan No. 18, Senopati, Jakarta Selatan 12190',
            'store_hours' => 'Senin - Minggu: 08:00 - 22:00 WIB',
            'shipping_flat_rate' => '10000',
            'about_story' => 'RASA KOPI lahir dari kecintaan kami terhadap kekayaan biji kopi Indonesia. Dari lereng Gayo, dataran tinggi Toraja, hingga kaki Gunung Ijen, kami memilih hanya biji kopi pilihan terbaik untuk disajikan dengan penuh presisi dan kehangatan.',
            'about_vision' => 'Menjadi jaringan kedai kopi terdepan di Indonesia yang mengusung nilai keaslian, keberlanjutan, dan kenyamanan untuk setiap penikmat kopi.',
            'about_mission' => "1. Menyajikan racikan kopi bermutu tinggi dari petani lokal nusantara.\n2. Memberikan pelayanan yang ramah, hangat, dan personal.\n3. Menyediakan ruang temu kreatif yang nyaman dan higienis.",
        ];

        foreach ($settings as $key => $val) {
            StoreSetting::set($key, $val);
        }

        // 4. Payment Methods
        $paymentMethods = [
            [
                'code' => 'bca',
                'name' => 'Transfer Bank BCA',
                'account_number' => '8830192837',
                'account_name' => 'PT RASA KOPI NUSANTARA',
                'instructions' => 'Transfer ke rekening BCA di atas sesuai total belanja. Sertakan nomor pesanan pada berita transfer jika memungkinkan.',
                'icon' => 'bi-credit-card-2-front',
                'is_active' => true,
            ],
            [
                'code' => 'mandiri',
                'name' => 'Transfer Bank Mandiri',
                'account_number' => '137001928374',
                'account_name' => 'PT RASA KOPI NUSANTARA',
                'instructions' => 'Transfer ke rekening Mandiri di atas sesuai total belanja. Simpan bukti transfer untuk diunggah.',
                'icon' => 'bi-bank',
                'is_active' => true,
            ],
            [
                'code' => 'bri',
                'name' => 'Transfer Bank BRI',
                'account_number' => '012301002938531',
                'account_name' => 'PT RASA KOPI NUSANTARA',
                'instructions' => 'Transfer ke rekening BRI di atas sesuai total belanja.',
                'icon' => 'bi-wallet2',
                'is_active' => true,
            ],
            [
                'code' => 'cash',
                'name' => 'Tunai di Toko (Pick-up / COD)',
                'account_number' => null,
                'account_name' => null,
                'instructions' => 'Bayar tunai di kasir saat mengambil pesanan kopi Anda di gerai Rasa Kopi.',
                'icon' => 'bi-cash-coin',
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $pm) {
            PaymentMethod::updateOrCreate(['code' => $pm['code']], $pm);
        }

        // 5. Coupons
        Coupon::updateOrCreate(
            ['code' => 'NGOPIHEMAT'],
            [
                'name' => 'Promo Ngopi Hemat Rp 10.000',
                'type' => 'fixed',
                'value' => 10000,
                'min_order_amount' => 35000,
                'max_discount' => null,
                'usage_limit' => 500,
                'used_count' => 0,
                'start_date' => Carbon::now()->subDay(),
                'end_date' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'KOPI20'],
            [
                'name' => 'Diskon 20% Pelanggan Setia',
                'type' => 'percent',
                'value' => 20,
                'min_order_amount' => 40000,
                'max_discount' => 25000,
                'usage_limit' => 200,
                'used_count' => 0,
                'start_date' => Carbon::now()->subDay(),
                'end_date' => Carbon::now()->addMonths(6),
                'is_active' => true,
            ]
        );
    }
}
