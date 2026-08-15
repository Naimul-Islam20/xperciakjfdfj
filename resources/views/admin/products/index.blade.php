@extends('admin.layouts.app')

@section('title', 'Products')
@section('heading', 'Products')
@section('subheading', 'Manage catalog products')

@section('content')
    <div data-admin-bulk-wrap>
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex items-center gap-2">
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search..."
                           class="w-36 rounded-lg border border-brand-ink/15 px-3 py-2 text-sm sm:w-44">
                    <button type="submit" class="rounded-lg border border-brand-ink/15 px-3 py-2 text-sm">Search</button>
                </form>
                @include('admin.partials.select-toggle')
            </div>
            <a href="{{ route('admin.products.create') }}" class="inline-flex justify-center rounded-lg bg-brand-ink px-4 py-2 text-sm font-medium text-white">
                Add Product
            </a>
        </div>

        <form method="POST" action="{{ route('admin.products.bulk-destroy') }}" data-admin-bulk>
            @csrf
            @method('DELETE')

            <div class="overflow-hidden rounded-xl border border-brand-ink/10 bg-white">
                @include('admin.partials.bulk-bar', ['confirm' => 'Delete selected products?'])

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-sm">
                        <thead class="bg-brand-mist/50 text-left text-brand-ink/60">
                            <tr>
                                <th class="admin-select-col px-5 py-3">
                                    <input type="checkbox" class="admin-bulk-check" data-admin-bulk-all aria-label="Select all">
                                </th>
                                <th class="px-5 py-3 font-medium">Image</th>
                                <th class="px-5 py-3 font-medium">Name</th>
                                <th class="px-5 py-3 font-medium">Category</th>
                                <th class="px-5 py-3 font-medium">Price</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                                <th class="px-5 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr class="border-t border-brand-ink/5">
                                    <td class="admin-select-col px-5 py-3">
                                        <input type="checkbox" class="admin-bulk-check" name="ids[]" value="{{ $product->id }}" data-admin-bulk-item aria-label="Select {{ $product->name }}">
                                    </td>
                                    <td class="px-5 py-3">
                                        @if ($product->image)
                                            <img src="{{ $product->imageUrl() }}" alt="" class="h-12 w-12 rounded-lg object-cover">
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-mist text-[10px] text-brand-ink/40">No img</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="max-w-40 truncate font-medium" title="{{ $product->name }}">{{ $product->name }}</div>
                                        <div class="max-w-40 truncate text-xs text-brand-ink/50" title="{{ $product->slug }}">{{ $product->slug }}</div>
                                    </td>
                                    <td class="px-5 py-3">{{ $product->category?->name ?? '—' }}</td>
                                    <td class="px-5 py-3">{{ $product->formattedPriceFrom() }}</td>
                                    <td class="px-5 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="mr-3 text-brand-ink/70 hover:text-brand-ink">Edit</a>
                                        <button type="submit" form="product-delete-{{ $product->id }}" class="text-red-600 hover:text-red-800"
                                                onclick="return confirm('Delete this product?')">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-brand-ink/60">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($products->hasPages())
                    <div class="border-t border-brand-ink/10 px-5 py-4">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>

    @foreach ($products as $product)
        <form id="product-delete-{{ $product->id }}" action="{{ route('admin.products.destroy', $product) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
