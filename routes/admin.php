<?php

use App\Http\Controllers\Admin\AboutPageController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeCollectionItemController;
use App\Http\Controllers\Admin\HomeHeroSlideController;
use App\Http\Controllers\Admin\HomePageController;
use App\Http\Controllers\Admin\HomeSectionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SiteInfoController;
use App\Http\Controllers\Admin\SubCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::middleware('admin')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('products', ProductController::class)->except(['show']);
        Route::delete('products-bulk', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::delete('categories-bulk', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');
        Route::resource('subcategories', SubCategoryController::class)->except(['show']);
        Route::delete('subcategories-bulk', [SubCategoryController::class, 'bulkDestroy'])->name('subcategories.bulk-destroy');
        Route::resource('admins', AdminUserController::class)->except(['show']);
        Route::delete('admins-bulk', [AdminUserController::class, 'bulkDestroy'])->name('admins.bulk-destroy');
        Route::resource('contacts', ContactMessageController::class)->only(['index', 'show', 'destroy']);
        Route::delete('contacts-bulk', [ContactMessageController::class, 'bulkDestroy'])->name('contacts.bulk-destroy');

        Route::get('home-page', [HomePageController::class, 'index'])->name('home-page.index');
        Route::put('home-page/settings', [HomePageController::class, 'updateSettings'])->name('home-page.settings.update');
        Route::get('home-page/collections-settings', [HomePageController::class, 'editCollectionsSettings'])->name('home-page.collections-settings.edit');
        Route::put('home-page/collections-settings', [HomePageController::class, 'updateCollectionsSettings'])->name('home-page.collections-settings.update');
        Route::resource('home-hero-slides', HomeHeroSlideController::class)->except(['show', 'index']);
        Route::delete('home-hero-slides-bulk', [HomeHeroSlideController::class, 'bulkDestroy'])->name('home-hero-slides.bulk-destroy');
        Route::resource('home-sections', HomeSectionController::class)->except(['show', 'index']);
        Route::delete('home-sections-bulk', [HomeSectionController::class, 'bulkDestroy'])->name('home-sections.bulk-destroy');
        Route::resource('home-collection-items', HomeCollectionItemController::class)->except(['show', 'index']);
        Route::delete('home-collection-items-bulk', [HomeCollectionItemController::class, 'bulkDestroy'])->name('home-collection-items.bulk-destroy');

        Route::get('site-info', [SiteInfoController::class, 'index'])->name('site-info.index');
        Route::put('site-info', [SiteInfoController::class, 'update'])->name('site-info.update');

        Route::get('about', [AboutPageController::class, 'index'])->name('about.index');
        Route::put('about', [AboutPageController::class, 'update'])->name('about.update');
    });
});
