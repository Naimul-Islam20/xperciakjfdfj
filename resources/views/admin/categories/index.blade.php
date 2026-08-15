@extends('admin.layouts.app')

@section('title', 'Categories')
@section('heading', 'Categories')
@section('subheading', 'Top-level categories for menu and catalog')

@section('content')
    <div data-admin-bulk-wrap>
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex items-center gap-2">
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search..."
                           class="w-36 rounded-lg border border-brand-ink/15 px-3 py-2 text-sm sm:w-44">
                    <button type="submit" class="rounded-lg border border-brand-ink/15 px-3 py-2 text-sm">Search</button>
                </form>
                @include('admin.partials.select-toggle')
            </div>
            <a href="{{ route('admin.categories.create') }}" class="hidden justify-center rounded-lg bg-brand-ink px-4 py-2 text-sm font-medium text-white sm:inline-flex">
                Add Category
            </a>
        </div>

        <a href="{{ route('admin.categories.create') }}"
           class="admin-fab fixed right-4 bottom-4 z-40 inline-flex items-center gap-2 rounded-full bg-brand-ink px-4 py-3 text-sm font-medium text-white shadow-lg sm:hidden">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
            </svg>
            Add Category
        </a>

        <form method="POST" action="{{ route('admin.categories.bulk-destroy') }}" data-admin-bulk>
            @csrf
            @method('DELETE')

            <div class="mb-20 overflow-hidden rounded-xl border border-brand-ink/10 bg-white sm:mb-0">
                @include('admin.partials.bulk-bar', ['confirm' => 'Delete selected categories?'])

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-brand-mist/50 text-left text-brand-ink/60">
                            <tr>
                                <th class="admin-select-col px-5 py-3">
                                    <input type="checkbox" class="admin-bulk-check" data-admin-bulk-all aria-label="Select all">
                                </th>
                                <th class="px-5 py-3 font-medium">Image</th>
                                <th class="px-5 py-3 font-medium">Name</th>
                                <th class="px-5 py-3 font-medium">Subcategories</th>
                                <th class="px-5 py-3 font-medium">Products</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                                <th class="px-5 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr class="border-t border-brand-ink/5">
                                    <td class="admin-select-col px-5 py-3">
                                        <input type="checkbox" class="admin-bulk-check" name="ids[]" value="{{ $category->id }}" data-admin-bulk-item aria-label="Select {{ $category->name }}">
                                    </td>
                                    <td class="px-5 py-3">
                                        @if ($category->image)
                                            <img src="{{ $category->imageUrl() }}" alt="" class="h-12 w-12 rounded-lg object-cover">
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-mist text-[10px] text-brand-ink/40">No img</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="max-w-40 truncate font-medium" title="{{ $category->name }}">{{ $category->name }}</div>
                                    </td>
                                    <td class="px-5 py-3">{{ $category->children_count }}</td>
                                    <td class="px-5 py-3">{{ $category->products_count }}</td>
                                    <td class="px-5 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="mr-3 text-brand-ink/70 hover:text-brand-ink">Edit</a>
                                        <button type="submit" form="category-delete-{{ $category->id }}" class="text-red-600 hover:text-red-800"
                                                onclick="return confirm('Delete this category?')">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-brand-ink/60">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($categories->hasPages())
                    <div class="border-t border-brand-ink/10 px-5 py-4">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>

    @foreach ($categories as $category)
        <form id="category-delete-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
