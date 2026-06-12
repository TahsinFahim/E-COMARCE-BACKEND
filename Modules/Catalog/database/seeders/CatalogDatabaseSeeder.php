<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\Brand;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductVariant;
use Modules\Catalog\Models\ProductImage;
use Modules\Catalog\Models\ProductCategory;

class CatalogDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Brands =====
        $brands = [
            ['name' => 'Nike', 'slug' => 'nike', 'status' => 'active'],
            ['name' => 'Adidas', 'slug' => 'adidas', 'status' => 'active'],
            ['name' => 'Apple', 'slug' => 'apple', 'status' => 'active'],
            ['name' => 'Samsung', 'slug' => 'samsung', 'status' => 'active'],
            ['name' => 'Sony', 'slug' => 'sony', 'status' => 'active'],
            ['name' => 'Local Brand', 'slug' => 'local-brand', 'status' => 'active'],
        ];

        foreach ($brands as $brandData) {
            Brand::firstOrCreate(['slug' => $brandData['slug']], $brandData);
        }

        // ===== Categories =====
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and accessories', 'status' => 'active', 'sort_order' => 1],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Apparel and fashion items', 'status' => 'active', 'sort_order' => 2],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports equipment and gear', 'status' => 'active', 'sort_order' => 3],
            ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'description' => 'Home appliances and kitchenware', 'status' => 'active', 'sort_order' => 4],
            ['name' => 'Books', 'slug' => 'books', 'description' => 'Books and educational materials', 'status' => 'active', 'sort_order' => 5],
            // Sub-categories
            ['name' => 'Smartphones', 'slug' => 'smartphones', 'description' => 'Mobile phones and accessories', 'status' => 'active', 'sort_order' => 1, 'parent_id' => null],
            ['name' => 'Laptops', 'slug' => 'laptops', 'description' => 'Laptop computers', 'status' => 'active', 'sort_order' => 2, 'parent_id' => null],
            ['name' => 'Men', 'slug' => 'men', 'description' => 'Men\'s clothing', 'status' => 'active', 'sort_order' => 1, 'parent_id' => null],
            ['name' => 'Women', 'slug' => 'women', 'description' => 'Women\'s clothing', 'status' => 'active', 'sort_order' => 2, 'parent_id' => null],
        ];

        $categoryIds = [];
        foreach ($categories as $catData) {
            $category = Category::firstOrCreate(['slug' => $catData['slug']], $catData);
            $categoryIds[$catData['slug']] = $category->id;
        }

        // Set parent IDs for sub-categories
        $electronicsId = $categoryIds['electronics'] ?? null;
        $clothingId = $categoryIds['clothing'] ?? null;
        if ($electronicsId) {
            Category::where('slug', 'smartphones')->update(['parent_id' => $electronicsId]);
            Category::where('slug', 'laptops')->update(['parent_id' => $electronicsId]);
        }
        if ($clothingId) {
            Category::where('slug', 'men')->update(['parent_id' => $clothingId]);
            Category::where('slug', 'women')->update(['parent_id' => $clothingId]);
        }

        // ===== Products =====
        $nike = Brand::where('slug', 'nike')->first();
        $apple = Brand::where('slug', 'apple')->first();
        $samsung = Brand::where('slug', 'samsung')->first();
        $local = Brand::where('slug', 'local-brand')->first();

        $products = [
            [
                'brand_id' => $apple?->id,
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'short_description' => 'Apple iPhone 15 Pro Max 256GB',
                'description' => 'The most powerful iPhone ever. A17 Pro chip, 48MP camera system, titanium design.',
                'product_type' => 'physical',
                'status' => 'active',
                'visibility' => 'public',
                'categories' => ['electronics', 'smartphones'],
            ],
            [
                'brand_id' => $samsung?->id,
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'short_description' => 'Samsung Galaxy S24 Ultra 512GB',
                'description' => 'Galaxy AI is here. Built with titanium, Galaxy S24 Ultra features a flat display.',
                'product_type' => 'physical',
                'status' => 'active',
                'visibility' => 'public',
                'categories' => ['electronics', 'smartphones'],
            ],
            [
                'brand_id' => $apple?->id,
                'name' => 'MacBook Pro M3',
                'slug' => 'macbook-pro-m3',
                'short_description' => 'Apple MacBook Pro with M3 chip',
                'description' => 'Supercharged by M3 chip. Stunning Liquid Retina XDR display.',
                'product_type' => 'physical',
                'status' => 'active',
                'visibility' => 'public',
                'categories' => ['electronics', 'laptops'],
            ],
            [
                'brand_id' => $nike?->id,
                'name' => 'Nike Air Max 270',
                'slug' => 'nike-air-max-270',
                'short_description' => 'Nike Air Max 270 Running Shoes',
                'description' => 'The Nike Air Max 270 delivers visible cushioning under the heel.',
                'product_type' => 'physical',
                'status' => 'active',
                'visibility' => 'public',
                'categories' => ['sports', 'clothing', 'men'],
            ],
            [
                'brand_id' => $local?->id,
                'name' => 'Handmade Cotton Kurta',
                'slug' => 'handmade-cotton-kurta',
                'short_description' => 'Traditional handwoven cotton kurta',
                'description' => 'Premium quality handwoven cotton kurta, perfect for casual and festive wear.',
                'product_type' => 'physical',
                'status' => 'active',
                'visibility' => 'public',
                'categories' => ['clothing', 'men'],
            ],
            [
                'brand_id' => $local?->id,
                'name' => 'Banglar Muslin Saree',
                'slug' => 'banglar-muslin-saree',
                'short_description' => 'Traditional Bengali Muslin Saree',
                'description' => 'Exquisite handwoven muslin saree from Bangladesh.',
                'product_type' => 'physical',
                'status' => 'active',
                'visibility' => 'public',
                'categories' => ['clothing', 'women'],
            ],
        ];

        foreach ($products as $productData) {
            $categoriesSlugs = $productData['categories'] ?? [];
            unset($productData['categories']);

            $product = Product::firstOrCreate(
                ['slug' => $productData['slug']],
                array_merge($productData, ['published_at' => now()])
            );

            // Attach categories
            if (!empty($categoriesSlugs)) {
                $catIds = Category::whereIn('slug', $categoriesSlugs)->pluck('id')->toArray();
                $product->categories()->syncWithoutDetaching($catIds);
            }

            // Create variants for each product
            $variantNames = ['Default'];
            if (in_array($productData['slug'], ['iphone-15-pro-max', 'samsung-galaxy-s24-ultra'])) {
                $variantNames = ['256GB', '512GB', '1TB'];
            } elseif (in_array($productData['slug'], ['macbook-pro-m3'])) {
                $variantNames = ['14" M3', '16" M3 Pro', '16" M3 Max'];
            } elseif (in_array($productData['slug'], ['nike-air-max-270'])) {
                $variantNames = ['US 8', 'US 9', 'US 10', 'US 11'];
            } elseif (in_array($productData['slug'], ['handmade-cotton-kurta'])) {
                $variantNames = ['S', 'M', 'L', 'XL'];
            } elseif (in_array($productData['slug'], ['banglar-muslin-saree'])) {
                $variantNames = ['6 Yard', '5.5 Yard'];
            }

            foreach ($variantNames as $i => $variantName) {
                $basePrice = match ($productData['slug']) {
                    'iphone-15-pro-max' => 149999,
                    'samsung-galaxy-s24-ultra' => 139999,
                    'macbook-pro-m3' => 199999,
                    'nike-air-max-270' => 15999,
                    'handmade-cotton-kurta' => 2499,
                    'banglar-muslin-saree' => 8999,
                    default => 10000,
                };

                $priceMultiplier = match ($variantName) {
                    '512GB' => 1.15,
                    '1TB' => 1.3,
                    '16" M3 Pro' => 1.35,
                    '16" M3 Max' => 1.6,
                    default => 1,
                };

                ProductVariant::firstOrCreate(
                    [
                        'product_id' => $product->id,
                        'sku' => strtoupper(str_replace([' ', '"', "'"], '-', $productData['slug'])) . '-' . ($i + 1),
                    ],
                    [
                        'name' => $variantName,
                        'sale_price' => (int) round($basePrice * $priceMultiplier),
                        'cost_price' => (int) round($basePrice * $priceMultiplier * 0.7),
                        'status' => 'active',
                        'track_inventory' => true,
                    ]
                );
            }
        }

        // ===== Product Images =====
        $products = Product::all();
        foreach ($products as $product) {
            ProductImage::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'image_url' => "https://via.placeholder.com/600x600?text=" . urlencode($product->name),
                ],
                [
                    'alt_text' => $product->name,
                    'sort_order' => 0,
                ]
            );
        }

        $this->command->info('Catalog module seeded successfully!');
    }
}