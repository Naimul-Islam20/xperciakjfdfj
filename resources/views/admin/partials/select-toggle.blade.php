@php
    $label = $label ?? 'Select';
@endphp

<div class="admin-select-toolbar" data-admin-select-toolbar>
    <button type="button" class="admin-select-toggle" data-admin-select-toggle>
        {{ $label }}
    </button>
    <button type="button" class="admin-select-cancel" data-admin-select-cancel hidden>
        Cancel
    </button>
</div>
