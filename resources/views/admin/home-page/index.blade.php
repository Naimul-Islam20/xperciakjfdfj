@extends('admin.layouts.app')

@section('title', 'Home Page')
@section('heading', 'Home Page')
@section('subheading', 'Manage hero, collections, and product sections')

@section('content')
    <div class="space-y-6">
        <section class="rounded-xl border border-brand-ink/10 bg-white p-4 sm:p-5" data-admin-bulk-wrap>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <h2 class="font-display text-lg font-semibold">Hero Slides</h2>
                    <p class="mt-1 text-sm text-brand-ink/60">Each slide has its own image, button text, and button link.</p>
                </div>
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    @include('admin.partials.select-toggle')
                    <a href="{{ route('admin.home-hero-slides.create') }}" class="inline-flex justify-center rounded-lg bg-brand-ink px-4 py-2 text-sm font-medium text-white">Add Slide</a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.home-hero-slides.bulk-destroy') }}" data-admin-bulk class="-mx-4 sm:-mx-5">
                @csrf
                @method('DELETE')
                @include('admin.partials.bulk-bar', ['confirm' => 'Delete selected slides?'])

                <div class="overflow-x-auto px-4 sm:px-5">
                    <table class="w-full min-w-[820px] text-sm">
                        <thead class="bg-brand-mist/50 text-left text-brand-ink/60">
                            <tr>
                                <th class="admin-select-col px-4 py-3">
                                    <input type="checkbox" class="admin-bulk-check" data-admin-bulk-all aria-label="Select all slides">
                                </th>
                                <th class="px-4 py-3 font-medium">Order</th>
                                <th class="px-4 py-3 font-medium">Image</th>
                                <th class="px-4 py-3 font-medium">Button</th>
                                <th class="px-4 py-3 font-medium">Link</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($heroSlides as $slide)
                                <tr class="border-t border-brand-ink/5">
                                    <td class="admin-select-col px-4 py-3">
                                        <input type="checkbox" class="admin-bulk-check" name="ids[]" value="{{ $slide->id }}" data-admin-bulk-item aria-label="Select slide {{ $slide->id }}">
                                    </td>
                                    <td class="px-4 py-3">{{ $slide->sort_order }}</td>
                                    <td class="px-4 py-3">
                                        <img src="{{ $slide->imageUrl() }}" alt="" class="h-12 w-24 rounded object-cover">
                                    </td>
                                    <td class="px-4 py-3">{{ $slide->buttonLabel() }}</td>
                                    <td class="px-4 py-3">{{ $slide->button_link ?: '/shop' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs {{ $slide->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $slide->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.home-hero-slides.edit', $slide) }}" class="mr-3 hover:underline">Edit</a>
                                        <button type="submit" form="slide-delete-{{ $slide->id }}" class="text-red-600"
                                                onclick="return confirm('Delete this slide?')">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-8 text-center text-brand-ink/60">No hero slides yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </section>

        <section class="rounded-xl border border-brand-ink/10 bg-white p-4 sm:p-5" data-admin-bulk-wrap>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <h2 class="font-display text-lg font-semibold">Collections</h2>
                    <p class="mt-1 text-sm text-brand-ink/60">
                        Homepage Collections cards. Layout:
                        {{ $settings->collectionsColumns() }} columns × {{ $settings->collectionsRows() }} rows
                        (max {{ $settings->collectionsLimit() }} cards).
                    </p>
                </div>
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    @include('admin.partials.select-toggle')
                    <a href="{{ route('admin.home-page.collections-settings.edit') }}" class="inline-flex justify-center rounded-lg border border-brand-ink/15 px-4 py-2 text-sm font-medium hover:bg-brand-mist">Edit</a>
                    <a href="{{ route('admin.home-collection-items.create') }}" class="inline-flex justify-center rounded-lg bg-brand-ink px-4 py-2 text-sm font-medium text-white">Add Collection</a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.home-collection-items.bulk-destroy') }}" data-admin-bulk class="-mx-4 sm:-mx-5">
                @csrf
                @method('DELETE')
                @include('admin.partials.bulk-bar', ['confirm' => 'Remove selected collection items?'])

                <div class="overflow-x-auto px-4 sm:px-5">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-brand-mist/50 text-left text-brand-ink/60">
                            <tr>
                                <th class="admin-select-col px-4 py-3">
                                    <input type="checkbox" class="admin-bulk-check" data-admin-bulk-all aria-label="Select all collections">
                                </th>
                                <th class="px-4 py-3 font-medium">Order</th>
                                <th class="px-4 py-3 font-medium">Preview</th>
                                <th class="px-4 py-3 font-medium">Name</th>
                                <th class="px-4 py-3 font-medium">Type</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($collectionItems as $item)
                                <tr class="border-t border-brand-ink/5">
                                    <td class="admin-select-col px-4 py-3">
                                        <input type="checkbox" class="admin-bulk-check" name="ids[]" value="{{ $item->id }}" data-admin-bulk-item aria-label="Select {{ $item->displayName() }}">
                                    </td>
                                    <td class="px-4 py-3">{{ $item->sort_order }}</td>
                                    <td class="px-4 py-3">
                                        @if ($item->category?->imageUrl())
                                            <img src="{{ $item->category->imageUrl() }}" alt="" class="h-12 w-16 rounded object-cover">
                                        @else
                                            <div class="flex h-12 w-16 items-center justify-center rounded bg-brand-mist text-[10px] text-brand-ink/40">No image</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-40 truncate font-medium" title="{{ $item->displayName() }}">{{ $item->displayName() }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $item->typeLabel() }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.home-collection-items.edit', $item) }}" class="mr-3 hover:underline">Edit</a>
                                        <button type="submit" form="collection-delete-{{ $item->id }}" class="text-red-600"
                                                onclick="return confirm('Remove this collection item?')">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-8 text-center text-brand-ink/60">No collection items yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </section>

        <section class="rounded-xl border border-brand-ink/10 bg-white p-4 sm:p-5" data-admin-bulk-wrap>
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <h2 class="font-display text-lg font-semibold">Product Sections</h2>
                    <p class="mt-1 text-sm text-brand-ink/60">
                        Add category-based sections (newest products first) or flag-based sections (from product Home Section Flags).
                    </p>
                </div>
                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    @include('admin.partials.select-toggle')
                    <a href="{{ route('admin.home-sections.create') }}" class="inline-flex justify-center rounded-lg bg-brand-ink px-4 py-2 text-sm font-medium text-white">Add Section</a>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.home-sections.bulk-destroy') }}" data-admin-bulk class="-mx-4 sm:-mx-5">
                @csrf
                @method('DELETE')
                @include('admin.partials.bulk-bar', ['confirm' => 'Delete selected sections?'])

                <div class="overflow-x-auto px-4 sm:px-5">
                    <table class="w-full min-w-[860px] text-sm">
                        <thead class="bg-brand-mist/50 text-left text-brand-ink/60">
                            <tr>
                                <th class="admin-select-col px-4 py-3">
                                    <input type="checkbox" class="admin-bulk-check" data-admin-bulk-all aria-label="Select all sections">
                                </th>
                                <th class="px-4 py-3 font-medium">Order</th>
                                <th class="px-4 py-3 font-medium">Title</th>
                                <th class="px-4 py-3 font-medium">Type</th>
                                <th class="px-4 py-3 font-medium">Source</th>
                                <th class="px-4 py-3 font-medium">Limit</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sections as $section)
                                <tr class="border-t border-brand-ink/5">
                                    <td class="admin-select-col px-4 py-3">
                                        <input type="checkbox" class="admin-bulk-check" name="ids[]" value="{{ $section->id }}" data-admin-bulk-item aria-label="Select {{ $section->title }}">
                                    </td>
                                    <td class="px-4 py-3">{{ $section->sort_order }}</td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-40 truncate font-medium" title="{{ $section->title }}">{{ $section->title }}</div>
                                    </td>
                                    <td class="px-4 py-3 capitalize">{{ $section->type }}</td>
                                    <td class="px-4 py-3">
                                        @if ($section->isCategoryType())
                                            Category: {{ $section->category?->name ?? '—' }}
                                        @elseif ($section->isSubcategoryType())
                                            SubCategory: {{ $section->category?->parent?->name ? $section->category->parent->name.' › ' : '' }}{{ $section->category?->name ?? '—' }}
                                        @else
                                            Flag (product checkboxes)
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $section->product_limit }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs {{ $section->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $section->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.home-sections.edit', $section) }}" class="mr-3 hover:underline">Edit</a>
                                        <button type="submit" form="section-delete-{{ $section->id }}" class="text-red-600"
                                                onclick="return confirm('Delete this section?')">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-8 text-center text-brand-ink/60">No sections yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </section>
    </div>

    @foreach ($heroSlides as $slide)
        <form id="slide-delete-{{ $slide->id }}" action="{{ route('admin.home-hero-slides.destroy', $slide) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    @foreach ($collectionItems as $item)
        <form id="collection-delete-{{ $item->id }}" action="{{ route('admin.home-collection-items.destroy', $item) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    @foreach ($sections as $section)
        <form id="section-delete-{{ $section->id }}" action="{{ route('admin.home-sections.destroy', $section) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
