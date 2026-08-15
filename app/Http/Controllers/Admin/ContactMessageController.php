<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $filter = $request->string('filter')->value();

        $messages = ContactMessage::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('comment', 'like', "%{$search}%");
                });
            })
            ->when($filter === 'unread', fn ($query) => $query->unread())
            ->when($filter === 'read', fn ($query) => $query->where('is_read', true))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.contacts.index', [
            'messages' => $messages,
            'search' => $search,
            'filter' => $filter,
            'unreadCount' => ContactMessage::unread()->count(),
        ]);
    }

    public function show(ContactMessage $contact): View
    {
        $contact->markAsRead();

        return view('admin.contacts.show', [
            'message' => $contact,
        ]);
    }

    public function destroy(ContactMessage $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Contact message deleted.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = collect($request->input('ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()
                ->route('admin.contacts.index')
                ->with('warning', 'No messages selected.');
        }

        $count = ContactMessage::query()->whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', $count.' message'.($count === 1 ? '' : 's').' deleted.');
    }
}
