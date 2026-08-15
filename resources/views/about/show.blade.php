@extends('layouts.app')

@php
    $site = $site ?? ($siteSettings ?? null);
    $pageTitle = $site?->aboutPageTitle() ?: 'About Us';
    $pageSubtitle = $site?->aboutPageSubtitle() ?: '';
    $metaDescription = trim(preg_replace('/\s+/', ' ', strip_tags($site?->aboutPageBodyHtml() ?? '')) ?? '');
    $metaDescription = \Illuminate\Support\Str::limit($metaDescription, 160);
@endphp

@section('title', $pageTitle)
@section('meta_description', $metaDescription ?: ($pageSubtitle ?: 'About us'))

@section('content')
<section class="about-page">
    <div class="container">
        <div class="about-content scroll-reveal">
            <h1 class="about-page-title">{{ $pageTitle }}</h1>

            @if ($pageSubtitle !== '')
                <h2 class="about-page-subtitle">{{ $pageSubtitle }}</h2>
            @endif

            <div class="about-page-body">
                {!! $site?->aboutPageBodyHtml() !!}
            </div>
        </div>
    </div>
</section>
@endsection
