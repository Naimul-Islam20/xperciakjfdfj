@php
    $confirm = $confirm ?? 'Delete selected items?';
@endphp

<div class="admin-bulk-bar" data-admin-bulk-bar hidden>
    <p class="admin-bulk-count">
        <span data-admin-bulk-count>0</span> selected
    </p>
    <button type="submit" class="admin-bulk-delete" data-admin-bulk-confirm="{{ $confirm }}">
        Delete selected
    </button>
</div>
