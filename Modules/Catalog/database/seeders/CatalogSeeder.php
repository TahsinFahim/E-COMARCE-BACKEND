<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run()
    {
        DB::table('brands')->insertOrIgnore([
            ['name' => 'Acme', 'slug' => 'acme', 'logo_url' => null, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Contoso', 'slug' => 'contoso', 'logo_url' => null, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('categories')->insertOrIgnore([
            ['parent_id' => null, 'name' => 'Electronics', 'slug' => 'electronics', 'description' => null, 'image_url'=>null, 'sort_order'=>0, 'status'=>'active','created_at'=>now(),'updated_at'=>now()],
            ['parent_id' => null, 'name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'description' => null, 'image_url'=>null, 'sort_order'=>0, 'status'=>'active','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
