@extends('layouts.app')

@section('title', $title)
@section('meta_description', $q !== '' ? 'Search results for '.$q : 'Search products')

@section('content')
<section class="collection-page">
    <div class="container">
        <h1 class="collection-page-title">{{ $title }}</h1>

        @if ($q === '')
            <p class="collection-empty">Type a product name in search to find items.</p>
        @else
            <p class="mb-6 text-sm text-brand-ink/60">{{ $productCount }} {{ $productCount === 1 ? 'result' : 'results' }}</p>

            @include('partials.collection-product-grid', [
                'emptyMessage' => 'No products found for “'.$q.'”.',
            ])

            @if ($products->hasPages())
                <nav class="shop-pagination" aria-label="Pagination">
                    {{ $products->onEachSide(1)->links('pagination.shop') }}
                </nav>
            @endif
        @endif
    </div>
</section>
@endsection
