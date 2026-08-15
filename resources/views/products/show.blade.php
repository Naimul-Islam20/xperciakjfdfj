@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', $product->metaDescription())

@section('content')
@php
    $gallery = $product->galleryUrls();
    $packs = $product->packOptions();
    $hasMultiplePricing = $product->isMultiplePricing() && count($packs) > 0;
    $firstPack = $packs[0] ?? null;
    $initialPrice = $hasMultiplePricing
        ? (float) ($firstPack['price'] ?? $product->price_from)
        : (float) $product->price_from;
@endphp

<section class="product-detail" data-product-detail>
    <div class="container">
        <div class="product-detail-grid">
            <div class="product-gallery">
                <div class="product-gallery-main">
                    <button type="button" class="product-zoom-btn" aria-label="Zoom image" data-product-zoom>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                            <circle cx="11" cy="11" r="6.5"/>
                            <path d="M16.5 16.5L21 21" stroke-linecap="round"/>
                            <path d="M11 8.5v5M8.5 11h5" stroke-linecap="round"/>
                        </svg>
                    </button>

                    @if (count($gallery))
                        <img
                            src="{{ $gallery[0] }}"
                            alt="{{ $product->name }}"
                            data-product-main-image
                        >
                    @else
                        <div class="product-gallery-placeholder" data-product-main-image aria-hidden="true"></div>
                    @endif
                </div>

                @if (count($gallery) > 1)
                    <div class="product-thumbs" role="list">
                        @foreach ($gallery as $index => $url)
                            <button
                                type="button"
                                class="product-thumb {{ $index === 0 ? 'is-active' : '' }}"
                                data-product-thumb
                                data-image="{{ $url }}"
                                aria-label="View image {{ $index + 1 }}"
                            >
                                <img src="{{ $url }}" alt="" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="product-info">
                <p class="product-brand">{{ $siteSettings?->site_name ?: 'XPERCIAINC' }}</p>
                <h1 class="product-title">{{ $product->name }}</h1>
                <p class="product-price" data-product-price>{{ $product->formattedPrice($initialPrice) }}</p>

                @if ($hasMultiplePricing)
                    <div class="product-packs" role="group" aria-label="Pack quantity">
                        @foreach ($packs as $index => $pack)
                            <button
                                type="button"
                                class="product-pack-btn {{ $index === 0 ? 'is-active' : '' }}"
                                data-product-pack
                                data-price-label="{{ $product->formattedPrice((float) $pack['price']) }}"
                            >
                                {{ $pack['label'] }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="product-long-desc">
                    {!! $product->defaultDescriptionHtml() !!}
                </div>
            </div>
        </div>
    </div>
</section>

<section class="related-products-section">
    <div class="container">
        <h2 class="related-products-heading">You may also like</h2>

        <div class="related-products-grid">
            @forelse ($relatedProducts ?? [] as $related)
                <a href="{{ route('products.show', $related->slug) }}" class="product-card">
                    <div class="product-card-media">
                        @if ($related->imageUrl())
                            <img src="{{ $related->imageUrl() }}" alt="{{ $related->name }}" loading="lazy">
                        @else
                            <div class="product-card-placeholder" aria-hidden="true"></div>
                        @endif
                    </div>
                    <h3 class="product-card-title">{{ $related->name }}</h3>
                    <p class="product-card-price">{{ $related->formattedPriceFrom() }}</p>
                </a>
            @empty
                <p class="text-sm text-brand-ink/55">No related products yet.</p>
            @endforelse
        </div>
    </div>
</section>

<div class="product-lightbox" data-product-lightbox hidden>
    <button type="button" class="product-lightbox-close" data-product-lightbox-close aria-label="Close zoom">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
        </svg>
    </button>
    <div class="product-lightbox-inner">
        <img src="" alt="{{ $product->name }}" data-product-lightbox-image>
        <div class="product-gallery-placeholder product-lightbox-placeholder" data-product-lightbox-placeholder hidden aria-hidden="true"></div>
    </div>
</div>
@endsection
