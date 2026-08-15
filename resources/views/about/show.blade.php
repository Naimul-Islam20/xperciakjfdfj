@extends('layouts.app')

@php
    $site = $site ?? ($siteSettings ?? null);
    $siteName = $site?->site_name ?: 'XPERCIAINC';
    $companyName = $site?->company_name ?: 'R.P. Trading Company';
@endphp

@section('title', 'About Us')
@section('meta_description', $siteName.' is a part of '.$companyName.'. Wholesaler of disposable food packaging including plates, boxes, bowls, trays, and more.')

@section('content')
<section class="about-page">
    <div class="container">
        <div class="about-content scroll-reveal">
            <h1 class="about-page-title">About Us</h1>

            <h2 class="about-page-subtitle">{{ $siteName }} is a part of {{ $companyName }}</h2>

            <div class="about-page-body">
                <p>
                    Established in the year 2016, we "{{ $companyName }}" are a leading
                    <strong>Wholesaler</strong> of a wide range of
                    <strong>Disposable Plate, Plastic Box, Disposable Bowl, Disposable Tray, Pasta Tray,</strong>
                    and much more.
                </p>

                <p>
                    Under the valuable guidance of our mentor,
                    <strong>Mr. Rahul Aggarwal</strong>, we are continuously moving towards success in this field.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
