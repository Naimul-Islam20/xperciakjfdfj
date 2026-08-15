@extends('admin.layouts.app')

@section('title', 'About Us')
@section('heading', 'About Us')
@section('subheading', 'Edit the content shown on the public About Us page')

@section('content')
    <div class="space-y-6">
        <section class="rounded-xl border border-brand-ink/10 bg-white p-4 sm:p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-display text-lg font-semibold">Page Content</h2>
                    <p class="mt-1 text-sm text-brand-ink/60">
                        Changes appear on
                        <a href="{{ route('about.show') }}" target="_blank" rel="noopener" class="underline hover:text-brand-ink">/about</a>.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.about.update') }}" class="mt-4 grid gap-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="about_page_title" class="mb-1 block text-sm font-medium">Page Title *</label>
                    <input id="about_page_title" name="about_page_title" type="text"
                           value="{{ old('about_page_title', $settings->about_page_title ?: 'About Us') }}" required
                           class="w-full rounded-lg border border-brand-ink/15 px-3 py-2 text-sm"
                           placeholder="About Us">
                    @error('about_page_title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="about_page_subtitle" class="mb-1 block text-sm font-medium">Subtitle</label>
                    <input id="about_page_subtitle" name="about_page_subtitle" type="text"
                           value="{{ old('about_page_subtitle', $settings->about_page_subtitle) }}"
                           class="w-full rounded-lg border border-brand-ink/15 px-3 py-2 text-sm"
                           placeholder="{{ ($settings->site_name ?: 'XPERCIAINC').' is a part of '.($settings->company_name ?: 'R.P. Trading Company') }}">
                    <p class="mt-1 text-xs text-brand-ink/50">Leave blank to use: Site Name is a part of Company Name (from Site Info).</p>
                    @error('about_page_subtitle')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="about_page_body" class="mb-1 block text-sm font-medium">Body *</label>
                    <textarea id="about_page_body" name="about_page_body" rows="12" required
                              class="w-full rounded-lg border border-brand-ink/15 px-3 py-2 text-sm font-mono leading-relaxed"
                              placeholder="Write the About Us story…">{{ old('about_page_body', $settings->about_page_body) }}</textarea>
                    <p class="mt-1 text-xs text-brand-ink/50">
                        Separate paragraphs with a blank line. Wrap words in *asterisks* for bold, e.g. *Wholesaler*.
                    </p>
                    @error('about_page_body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <button type="submit" class="rounded-lg bg-brand-ink px-4 py-2 text-sm font-medium text-white hover:bg-brand-ink/90">
                        Save About Us
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
