@extends('admin.layouts.app')

@section('title', 'Subcategories')
@section('heading', 'Subcategories')
@section('subheading', 'Child categories under a parent category')

@section('content')
    <div data-admin-bulk-wrap>
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('admin.subcategories.index') }}" class="flex items-center gap-2">
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search..."
                           class="w-36 rounded-lg border border-brand-ink/15 px-3 py-2 text-sm sm:w-44">
                    <button type="submit" class="rounded-lg border border-brand-ink/15 px-3 py-2 text-sm">Search</button>
                </form>
                @include('admin.partials.select-toggle')
            </div>
            <a href="{{ route('admin.subcategories.create') }}" class="hidden justify-center rounded-lg bg-brand-ink px-4 py-2 text-sm font-medium text-white sm:inline-flex">
                Add Subcategory
            </a>
        </div>

        <a href="{{ route('admin.subcategories.create') }}"
           class="admin-fab fixed right-4 bottom-4 z-40 inline-flex items-center gap-2 rounded-full bg-brand-ink px-4 py-3 text-sm font-medium text-white shadow-lg sm:hidden">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
            </svg>
            Add Subcategory
        </a>

        <form method="POST" action="{{ route('admin.subcategories.bulk-destroy') }}" data-admin-bulk>
            @csrf
            @method('DELETE')

            <div class="mb-20 overflow-hidden rounded-xl border border-brand-ink/10 bg-white sm:mb-0">
                @include('admin.partials.bulk-bar', ['confirm' => 'Delete selected subcategories?'])

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="bg-brand-mist/50 text-left text-brand-ink/60">
                            <tr>
                                <th class="admin-select-col px-5 py-3">
                                    <input type="checkbox" class="admin-bulk-check" data-admin-bulk-all aria-label="Select all">
                                </th>
                                <th class="px-5 py-3 font-medium">Name</th>
                                <th class="px-5 py-3 font-medium">Parent Category</th>
                                <th class="px-5 py-3 font-medium">Products</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                                <th class="px-5 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subcategories as $subcategory)
                                <tr class="border-t border-brand-ink/5">
                                    <td class="admin-select-col px-5 py-3">
                                        <input type="checkbox" class="admin-bulk-check" name="ids[]" value="{{ $subcategory->id }}" data-admin-bulk-item aria-label="Select {{ $subcategory->name }}">
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="max-w-40 truncate font-medium" title="{{ $subcategory->name }}">{{ $subcategory->name }}</div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="max-w-40 truncate" title="{{ $subcategory->parent?->name ?? '—' }}">
                                            {{ $subcategory->parent?->name ?? '—' }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">{{ $subcategory->products_count }}</td>
                                    <td class="px-5 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs {{ $subcategory->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $subcategory->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="mr-3 text-brand-ink/70 hover:text-brand-ink">Edit</a>
                                        <button type="submit" form="subcategory-delete-{{ $subcategory->id }}" class="text-red-600 hover:text-red-800"
                                                onclick="return confirm('Delete this subcategory?')">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-brand-ink/60">No subcategories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($subcategories->hasPages())
                    <div class="border-t border-brand-ink/10 px-5 py-4">
                        {{ $subcategories->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>

    @foreach ($subcategories as $subcategory)
        <form id="subcategory-delete-{{ $subcategory->id }}" action="{{ route('admin.subcategories.destroy', $subcategory) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
