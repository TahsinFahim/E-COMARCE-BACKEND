<?php

namespace Modules\Frontend\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomepageCtaSeeder extends Seeder
{
    public function run()
    {
        DB::table('homepage_ctas')->insertOrIgnore([
            [
                'title'             => 'Summer Deals For You',
                'subtitle'          => 'Discounts on top products',
                'description'       => 'Shop our biggest sale of the year on electronics, fashion, and home essentials. Limited time offer!',
                'image'             => 'https://via.placeholder.com/800x500?text=Summer+Deals',
                'button_text'       => 'Shop Now',
                'button_link'       => '/sale/summer',
                'background_color'  => '#fee2e2',
                'text_color'        => '#991b1b',
                'button_color'      => '#dc2626',
                'button_text_color' => '#ffffff',
                'sort_order'        => 0,
                'status'            => 'active',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'title'             => 'New Styles Arrive Daily',
                'subtitle'          => 'Fresh products every week',
                'description'       => 'Be the first to explore our newest collection. Fresh styles, top brands, and exclusive items.',
                'image'             => 'https://via.placeholder.com/800x500?text=New+Arrivals',
                'button_text'       => 'Explore',
                'button_link'       => '/new-arrivals',
                'background_color'  => '#dbeafe',
                'text_color'        => '#1e40af',
                'button_color'      => '#1e3a8a',
                'button_text_color' => '#ffffff',
                'sort_order'        => 1,
                'status'            => 'active',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'title'             => 'Fast Delivery For Orders',
                'subtitle'          => 'Free shipping over $50',
                'description'       => 'Enjoy free standard shipping on all orders above $50. No code needed — automatically applied at checkout.',
                'image'             => 'https://via.placeholder.com/800x500?text=Fast+Shipping',
                'button_text'       => 'Start Shopping',
                'button_link'       => '/products',
                'background_color'  => '#ecfdf5',
                'text_color'        => '#065f46',
                'button_color'      => '#059669',
                'button_text_color' => '#ffffff',
                'sort_order'        => 2,
                'status'            => 'active',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}