<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'currency',
        'logo',
        'favicon',
        'company_name',
        'about_text',
        'about_page_title',
        'about_page_subtitle',
        'about_page_body',
        'phone',
        'email',
        'address',
        'map_url',
        'gstin',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public static function current(): self
    {
        return once(fn () => static::resolveCurrent());
    }

    private static function resolveCurrent(): self
    {
        return static::query()->firstOrCreate([], [
            'site_name' => 'XPERCIAINC',
            'currency' => 'Rs.',
            'logo' => 'images/logo-mark.svg',
            'favicon' => 'images/logo-mark.svg',
            'company_name' => 'Rp Trading Company',
            'about_text' => 'R.P. Trading Company " are Wholesaler of Disposable Plate, Plastic Box, Disposable Bowl, Disposable Tray, Pasta Tray, and much more.',
            'about_page_title' => 'About Us',
            'about_page_subtitle' => null,
            'about_page_body' => "Established in the year 2016, we \"R.P. Trading Company\" are a leading *Wholesaler* of a wide range of *Disposable Plate, Plastic Box, Disposable Bowl, Disposable Tray, Pasta Tray,* and much more.\n\nUnder the valuable guidance of our mentor, *Mr. Rahul Aggarwal*, we are continuously moving towards success in this field.",
            'phone' => '9211997415',
            'email' => 'Info@Xperciainc.com',
            'address' => 'Basement, Vidhata Complex, Vasundhara Enclave, Delhi, India 110096',
            'map_url' => 'https://maps.google.com/?q=Basement,+Vidhata+Complex,+Vasundhara+Enclave,+Delhi,+India+110096',
            'gstin' => '07AJCPA7351H1ZI',
            'facebook_url' => null,
            'instagram_url' => null,
            'youtube_url' => null,
            'meta_title' => 'Eco-friendly Disposable Packaging',
            'meta_description' => 'xperciainc offers a wide range of disposable food packaging for restaurants, cloud kitchens, catering, and takeaways.',
            'meta_keywords' => 'disposable packaging, food packaging, eco-friendly packaging, meal trays, takeaway containers',
        ]);
    }

    public function currencyLabel(): string
    {
        $currency = trim((string) $this->currency);

        return $currency !== '' ? $currency : 'Rs.';
    }

    public function defaultMetaTitle(): string
    {
        $metaTitle = trim((string) $this->meta_title);

        if ($metaTitle !== '') {
            return $metaTitle;
        }

        return trim((string) $this->site_name) ?: 'xperciainc';
    }

    public function defaultMetaDescription(): string
    {
        $description = trim((string) $this->meta_description);

        if ($description !== '') {
            return $description;
        }

        return trim((string) $this->about_text) ?: 'xperciainc offers a wide range of disposable food packaging.';
    }

    public function logoUrl(): string
    {
        return $this->mediaUrl($this->logo, 'images/logo-mark.svg');
    }

    public function faviconUrl(): string
    {
        return $this->mediaUrl($this->favicon ?: $this->logo, 'images/logo-mark.svg');
    }

    public function ogImageUrl(): ?string
    {
        $logo = trim((string) $this->logo);

        if ($logo === '') {
            return asset('images/logo-mark.svg');
        }

        return $this->logoUrl();
    }

    public function aboutPageTitle(): string
    {
        return trim((string) $this->about_page_title) ?: 'About Us';
    }

    public function aboutPageSubtitle(): string
    {
        $custom = trim((string) $this->about_page_subtitle);

        if ($custom !== '') {
            return $custom;
        }

        $siteName = trim((string) $this->site_name) ?: 'XPERCIAINC';
        $companyName = trim((string) $this->company_name) ?: 'R.P. Trading Company';

        return $siteName.' is a part of '.$companyName;
    }

    public function aboutPageBodyHtml(): string
    {
        $body = trim((string) $this->about_page_body);

        if ($body === '') {
            $companyName = e(trim((string) $this->company_name) ?: 'R.P. Trading Company');

            return '<p>Established in the year 2016, we "'.$companyName.'" are a leading'
                .' <strong>Wholesaler</strong> of a wide range of'
                .' <strong>Disposable Plate, Plastic Box, Disposable Bowl, Disposable Tray, Pasta Tray,</strong>'
                .' and much more.</p>'
                .'<p>Under the valuable guidance of our mentor,'
                .' <strong>Mr. Rahul Aggarwal</strong>,'
                .' we are continuously moving towards success in this field.</p>';
        }

        if (preg_match('/<\/?[a-z][\s\S]*>/i', $body)) {
            return $body;
        }

        $paragraphs = preg_split("/\n\s*\n/", $body) ?: [$body];
        $html = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            $escaped = e($paragraph);
            $withBold = preg_replace('/\*([^*\n]+)\*/', '<strong>$1</strong>', $escaped) ?? $escaped;
            $html .= '<p>'.nl2br($withBold, false).'</p>';
        }

        return $html !== '' ? $html : '<p></p>';
    }

    public function mapsHref(): string
    {
        $url = trim((string) $this->map_url);

        if ($url !== '') {
            return $url;
        }

        $address = trim((string) $this->address);

        if ($address === '') {
            return '#';
        }

        return 'https://maps.google.com/?q='.rawurlencode($address);
    }

    public function mapsEmbedUrl(): ?string
    {
        $address = trim((string) $this->address);
        $mapUrl = trim((string) $this->map_url);

        if ($address === '' && $mapUrl === '') {
            return null;
        }

        $query = $address !== '' ? $address : $mapUrl;

        if (preg_match('/[?&]q=([^&]+)/', $mapUrl, $matches)) {
            $query = urldecode($matches[1]);
        }

        return 'https://maps.google.com/maps?q='.rawurlencode($query).'&z=15&output=embed';
    }

    public function socialLinks(): array
    {
        return array_filter([
            'Facebook' => $this->facebook_url,
            'Instagram' => $this->instagram_url,
            'YouTube' => $this->youtube_url,
        ]);
    }

    private function mediaUrl(?string $path, string $fallback): string
    {
        return app(\App\Services\ProductImageService::class)
            ->url($path, $fallback) ?? asset($fallback);
    }
}

