<?php

namespace Modules\Catalog\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductVariant;
use Modules\Catalog\Models\Brand;
use Modules\Catalog\Models\Category;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodePrintController extends Controller
{
    public function index()
    {
        $brands = Brand::where('status', 'active')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('catalog::barcode-print.index', compact('brands', 'categories'));
    }

    public function search(Request $request)
    {
        $query = Product::query()->with(['brand', 'categories', 'variants' => function ($q) {
            $q->where('status', 'active');
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $products = $query->where('status', 'active')->limit(50)->get();

        return response()->json($products);
    }

    public function autocomplete(Request $request)
    {
        $search = $request->get('q', '');
        if (strlen($search) < 1) {
            return response()->json([]);
        }

        $products = Product::where('status', 'active')
            ->where('name', 'like', "%{$search}%")
            ->with('brand')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'brand' => $p->brand?->name,
                    'label' => $p->name . ($p->brand ? " ({$p->brand->name})" : ''),
                ];
            });

        return response()->json($products);
    }

    public function variants(Request $request, $productId)
    {
        $product = Product::with(['brand', 'variants' => function ($q) {
            $q->where('status', 'active');
        }])->findOrFail($productId);

        return response()->json($product);
    }

    public function print(Request $request)
    {
        $request->validate([
            'variants' => 'required|array',
            'variants.*.variant_id' => 'required|exists:product_variants,id',
            'variants.*.copies' => 'required|integer|min:1|max:100',
            'paper_size' => 'required|in:letter,a4',
            'label_size' => 'required|in:1x1,1x2,2x2,2x3,3x4',
        ]);

        $variantIds = collect($request->variants)->pluck('variant_id');
        $variants = ProductVariant::whereIn('id', $variantIds)->with('product')->get()->keyBy('id');

        $generator = new BarcodeGeneratorSVG();
        $barcodes = [];

        foreach ($request->variants as $item) {
            $variant = $variants->get($item['variant_id']);
            if (!$variant || !$variant->product) continue;

            $barcodeValue = $variant->barcode ?? $variant->sku ?? (string)$variant->id;
            
            for ($i = 0; $i < $item['copies']; $i++) {
                try {
                    $barcodeSvg = $generator->getBarcode($barcodeValue, $generator::TYPE_CODE_128);
                } catch (\Exception $e) {
                    $barcodeSvg = '<svg><text>Error</text></svg>';
                }
                
                $barcodes[] = [
                    'name' => $variant->product->name . ' - ' . $variant->name,
                    'barcode_value' => $barcodeValue,
                    'sku' => $variant->sku,
                    'barcode_svg' => $barcodeSvg,
                ];
            }
        }

        $paperSize = $request->paper_size;
        $labelSize = $request->label_size;

        return view('catalog::barcode-print.print', compact('barcodes', 'paperSize', 'labelSize'));
    }
}