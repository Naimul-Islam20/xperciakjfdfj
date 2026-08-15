<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->active()
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->orderBy('sort_order')
            ->limit(5)
            ->get();

        if ($relatedProducts->count() < 5) {
            $extra = Product::query()
                ->active()
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->orderBy('sort_order')
                ->limit(5 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->concat($extra);
        }

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
