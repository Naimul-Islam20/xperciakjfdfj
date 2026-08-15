<div class="search-overlay" data-search-overlay hidden>
    <div class="search-overlay-backdrop" data-search-close></div>

    <div class="search-overlay-panel" role="dialog" aria-modal="true" aria-label="Search">
        <div class="search-overlay-inner">
            <form class="search-form" action="{{ route('search') }}" method="get" role="search">
                <label class="sr-only" for="site-search-input">Search</label>
                <input
                    id="site-search-input"
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search"
                    autocomplete="off"
                    data-search-input
                >
                <button type="submit" class="search-form-icon" aria-label="Submit search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="M20 20l-3.5-3.5" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>

            <button type="button" class="search-close" aria-label="Close search" data-search-close>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path d="M6 6l12 12M18 6L6 18" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>
</div>
