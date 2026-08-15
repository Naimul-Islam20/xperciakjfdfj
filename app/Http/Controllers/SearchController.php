<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    private const PER_PAGE = 30;

    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();

        $query = Product::query()->active();

        if ($q !== '') {
            $like = '%'.$q.'%';

            $query->where(function ($builder) use ($like) {
                $builder->where('name', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhere('brand', 'like', $like)
                    ->orWhere('short_description', 'like', $like)
                    ->orWhereHas('category', function ($category) use ($like) {
                        $category->where('name', 'like', $like)
                            ->orWhere('slug', 'like', $like);
                    });
            });
        }

        $products = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $title = $q !== '' ? 'Search results for “'.$q.'”' : 'Search';

        return view('search.index', [
            'title' => $title,
            'q' => $q,
            'products' => $products,
            'productCount' => $products->total(),
        ]);
    }
}
