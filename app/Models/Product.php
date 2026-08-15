<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    public const PRICING_MODE_SINGLE = 'single';
    public const PRICING_MODE_MULTIPLE = 'multiple';

    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'slug',
        'image',
        'short_description',
        'description',
        'gallery',
        'pricing_mode',
        'pack_options',
        'price_from',
        'currency',
        'is_best_seller',
        'is_top_selling',
        'is_hinged_box',
        'is_trending',
        'is_meal_trays',
        'is_round_containers',
        'is_rectangular_container',
        'is_cornstarch_product',
        'is_aluminium_foil_container',
        'is_bagasse_tableware',
        'is_biodegradable_products',
        'is_bagasse_takeaway_container',
        'is_paper_products',
        'is_new_arrivals',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_from' => 'decimal:2',
            'gallery' => 'array',
            'pack_options' => 'array',
            'is_best_seller' => 'boolean',
            'is_top_selling' => 'boolean',
            'is_hinged_box' => 'boolean',
            'is_trending' => 'boolean',
            'is_meal_trays' => 'boolean',
            'is_round_containers' => 'boolean',
            'is_rectangular_container' => 'boolean',
            'is_cornstarch_product' => 'boolean',
            'is_aluminium_foil_container' => 'boolean',
            'is_bagasse_tableware' => 'boolean',
            'is_biodegradable_products' => 'boolean',
            'is_bagasse_takeaway_container' => 'boolean',
            'is_paper_products' => 'boolean',
            'is_new_arrivals' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function homeSections(): BelongsToMany
    {
        return $this->belongsToMany(HomeSection::class)->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBestSellers($query)
    {
        return $query->active()
            ->where('is_best_seller', true)
            ->orderBy('sort_order');
    }

    public function scopeTopSelling($query)
    {
        return $query->active()
            ->where('is_top_selling', true)
            ->orderBy('sort_order');
    }

    public function scopeHingedBox($query)
    {
        return $query->active()
            ->where('is_hinged_box', true)
            ->orderBy('sort_order');
    }

    public function scopeTrending($query)
    {
        return $query->active()
            ->where('is_trending', true)
            ->orderBy('sort_order');
    }

    public function scopeMealTrays($query)
    {
        return $query->active()
            ->where('is_meal_trays', true)
            ->orderBy('sort_order');
    }

    public function scopeRoundContainers($query)
    {
        return $query->active()
            ->where('is_round_containers', true)
            ->orderBy('sort_order');
    }

    public function scopeRectangularContainer($query)
    {
        return $query->active()
            ->where('is_rectangular_container', true)
            ->orderBy('sort_order');
    }

    public function scopeCornstarchProduct($query)
    {
        return $query->active()
            ->where('is_cornstarch_product', true)
            ->orderBy('sort_order');
    }

    public function scopeAluminiumFoilContainer($query)
    {
        return $query->active()
            ->where('is_aluminium_foil_container', true)
            ->orderBy('sort_order');
    }

    public function scopeBagasseTableware($query)
    {
        return $query->active()
            ->where('is_bagasse_tableware', true)
            ->orderBy('sort_order');
    }

    public function scopeBiodegradableProducts($query)
    {
        return $query->active()
            ->where('is_biodegradable_products', true)
            ->orderBy('sort_order');
    }

    public function scopeBagasseTakeawayContainer($query)
    {
        return $query->active()
            ->where('is_bagasse_takeaway_container', true)
            ->orderBy('sort_order');
    }

    public function scopePaperProducts($query)
    {
        return $query->active()
            ->where('is_paper_products', true)
            ->orderBy('sort_order');
    }

    public function scopeNewArrivals($query)
    {
        return $query->active()
            ->where('is_new_arrivals', true)
            ->orderBy('sort_order');
    }

    public function imageUrl(): ?string
    {
        return app(\App\Services\ProductImageService::class)
            ->url($this->image, 'images/item-1.webp');
    }

    public function galleryUrls(): array
    {
        $imageService = app(\App\Services\ProductImageService::class);
        $urls = [$this->imageUrl()];

        foreach ($this->gallery ?? [] as $path) {
            $url = $imageService->url($path);

            if ($url && ! in_array($url, $urls, true)) {
                $urls[] = $url;
            }
        }

        return array_values(array_filter($urls));
    }

    public function galleryPathUrl(string $path): ?string
    {
        return app(\App\Services\ProductImageService::class)->url($path);
    }

    public function packOptions(): array
    {
        if ($this->isSinglePricing()) {
            return [];
        }

        $normalized = collect($this->pack_options ?? [])
            ->map(function ($option) {
                if (! is_array($option)) {
                    return null;
                }

                $label = trim((string) ($option['label'] ?? ''));
                $price = $option['price'] ?? null;

                if ($label !== '' && is_numeric($price)) {
                    return [
                        'label' => $label,
                        'price' => round((float) $price, 2),
                    ];
                }

                $pcs = $option['pcs'] ?? null;
                $unitPrice = $option['unit_price'] ?? null;

                if (is_numeric($pcs) && is_numeric($unitPrice)) {
                    return [
                        'label' => (int) $pcs.' Pcs',
                        'price' => round((float) $pcs * (float) $unitPrice, 2),
                    ];
                }

                return null;
            })
            ->filter()
            ->values()
            ->all();

        if ($normalized !== []) {
            return $normalized;
        }

        $base = max(1, (float) $this->price_from / 25);

        return [
            ['label' => '25 Pcs', 'price' => round($base * 25, 2)],
            ['label' => '100 Pcs', 'price' => round($base * 100 * 0.94, 2)],
            ['label' => '300 Pcs', 'price' => round($base * 300 * 0.89, 2)],
            ['label' => '500 Pcs', 'price' => round($base * 500 * 0.87, 2)],
        ];
    }

    public function isSinglePricing(): bool
    {
        return ($this->pricing_mode ?: self::PRICING_MODE_MULTIPLE) === self::PRICING_MODE_SINGLE;
    }

    public function isMultiplePricing(): bool
    {
        return ! $this->isSinglePricing();
    }

    public function formattedPrice(float $amount): string
    {
        return $this->displayCurrency().' '.number_format($amount, 2);
    }

    public function formattedPriceFrom(): string
    {
        if ($this->isSinglePricing()) {
            return $this->formattedPrice((float) $this->price_from);
        }

        return 'From '.$this->displayCurrency().' '.number_format((float) $this->price_from, 2);
    }

    public function displayCurrency(): string
    {
        return SiteSetting::current()->currencyLabel();
    }

    public function metaDescription(): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $this->description)) ?? '');
        $text = trim(preg_replace('/\*([^*\n]+)\*/', '$1', $text) ?? '');

        if ($text !== '') {
            return \Illuminate\Support\Str::limit($text, 160);
        }

        return $this->name.' from XPERCIAINC';
    }

    public function defaultDescriptionHtml(): string
    {
        $description = trim((string) $this->description);

        if ($description === '') {
            return '<p><strong>'.e($this->name).'</strong></p>'
                .'<p>Compact. Convenient. Classy.</p>'
                .'<p>Perfect for restaurants, cloud kitchens, catering, and takeaways. Durable build with a secure lid for mess-free packing and delivery.</p>';
        }

        // Legacy HTML descriptions from older seed/content.
        if (preg_match('/<\/?[a-z][\s\S]*>/i', $description)) {
            return $description;
        }

        $escaped = e($description);
        $withBold = preg_replace('/\*([^*\n]+)\*/', '<strong>$1</strong>', $escaped) ?? $escaped;

        return '<p>'.nl2br($withBold, false).'</p>';
    }
}
