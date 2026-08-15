@extends('admin.layouts.app')

@section('title', 'Admins')
@section('heading', 'Admin Users')
@section('subheading', 'Manage admin accounts')

@section('content')
    <div data-admin-bulk-wrap>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            @include('admin.partials.select-toggle')
            <a href="{{ route('admin.admins.create') }}" class="inline-flex rounded-lg bg-brand-ink px-4 py-2 text-sm font-medium text-white">
                Add Admin
            </a>
        </div>

        <form method="POST" action="{{ route('admin.admins.bulk-destroy') }}" data-admin-bulk>
            @csrf
            @method('DELETE')

            <div class="overflow-hidden rounded-xl border border-brand-ink/10 bg-white">
                @include('admin.partials.bulk-bar', ['confirm' => 'Delete selected admins?'])

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[780px] text-sm">
                        <thead class="bg-brand-mist/50 text-left text-brand-ink/60">
                            <tr>
                                <th class="admin-select-col px-5 py-3">
                                    <input type="checkbox" class="admin-bulk-check" data-admin-bulk-all aria-label="Select all">
                                </th>
                                <th class="px-5 py-3 font-medium">Name</th>
                                <th class="px-5 py-3 font-medium">Email</th>
                                <th class="px-5 py-3 font-medium">Status</th>
                                <th class="px-5 py-3 font-medium">Last Login</th>
                                <th class="px-5 py-3 font-medium text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($admins as $admin)
                                <tr class="border-t border-brand-ink/5">
                                    <td class="admin-select-col px-5 py-3">
                                        <input type="checkbox"
                                               class="admin-bulk-check"
                                               name="ids[]"
                                               value="{{ $admin->id }}"
                                               data-admin-bulk-item
                                               @disabled($admin->id === auth()->id() || ! auth()->user()->can('delete', $admin))
                                               aria-label="Select {{ $admin->name }}">
                                    </td>
                                    <td class="px-5 py-3 font-medium">
                                        {{ $admin->name }}
                                        @if ($admin->id === auth()->id())
                                            <span class="ml-1 text-xs text-brand-ink/50">(you)</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">{{ $admin->email }}</td>
                                    <td class="px-5 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-xs {{ $admin->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-brand-ink/60">
                                        {{ $admin->last_login_at?->diffForHumans() ?? 'Never' }}
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="{{ route('admin.admins.edit', $admin) }}" class="mr-3 text-brand-ink/70 hover:text-brand-ink">Edit</a>
                                        @can('delete', $admin)
                                            <button type="submit" form="admin-delete-{{ $admin->id }}" class="text-red-600 hover:text-red-800"
                                                    onclick="return confirm('Delete this admin?')">Delete</button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-brand-ink/60">No admin users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($admins->hasPages())
                    <div class="border-t border-brand-ink/10 px-5 py-4">
                        {{ $admins->links() }}
                    </div>
                @endif
            </div>
        </form>
    </div>

    @foreach ($admins as $admin)
        @can('delete', $admin)
            <form id="admin-delete-{{ $admin->id }}" action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endcan
    @endforeach
@endsection
