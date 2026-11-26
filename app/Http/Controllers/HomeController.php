<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Review;
use App\Models\StoreSetting;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->get();
        $featuredProducts = Product::with('category', 'approvedReviews')
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        $activeCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
            })
            ->take(2)
            ->get();

        $testimonials = Review::with('product', 'user')
            ->where('is_approved', true)
            ->where('rating', '>=', 4)
            ->latest()
            ->take(4)
            ->get();

        $storeSettings = [
            'name' => StoreSetting::get('store_name', 'RASA KOPI'),
            'tagline' => StoreSetting::get('store_tagline', 'Cita Rasa Kopi Nusantara Terbaik'),
            'phone' => StoreSetting::get('store_phone', '0812-3456-7890'),
            'whatsapp' => StoreSetting::get('store_whatsapp', '6281234567890'),
            'address' => StoreSetting::get('store_address', 'Jl. Kopi Kenikmatan No. 18, Senopati, Jakarta Selatan'),
            'hours' => StoreSetting::get('store_hours', 'Senin - Minggu: 08:00 - 22:00 WIB'),
        ];

        return view('pages.home', compact('categories', 'featuredProducts', 'activeCoupons', 'testimonials', 'storeSettings'));
    }

    public function about(): View
    {
        $storeSettings = [
            'name' => StoreSetting::get('store_name', 'RASA KOPI'),
            'tagline' => StoreSetting::get('store_tagline', 'Cita Rasa Kopi Nusantara Terbaik'),
            'phone' => StoreSetting::get('store_phone', '0812-3456-7890'),
            'email' => StoreSetting::get('store_email', 'halo@rasakopi.com'),
            'address' => StoreSetting::get('store_address', 'Jl. Kopi Kenikmatan No. 18, Senopati, Jakarta Selatan'),
            'hours' => StoreSetting::get('store_hours', 'Senin - Minggu: 08:00 - 22:00 WIB'),
            'story' => StoreSetting::get('about_story', 'RASA KOPI lahir dari kecintaan kami terhadap kekayaan biji kopi Indonesia.'),
            'vision' => StoreSetting::get('about_vision', 'Menjadi jaringan kedai kopi terdepan di Indonesia yang mengusung nilai keaslian dan keberlanjutan.'),
            'mission' => StoreSetting::get('about_mission', "1. Menyajikan racikan kopi bermutu tinggi.\n2. Memberikan pelayanan ramah.\n3. Menyediakan ruang temu nyaman."),
        ];

        return view('pages.about', compact('storeSettings'));
    }
}
