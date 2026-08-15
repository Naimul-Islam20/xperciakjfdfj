<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHomeSectionRequest;
use App\Http\Requests\Admin\UpdateHomeSectionRequest;
use App\Models\Category;
use App\Models\HomeSection;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeSectionController extends Controller
{
    public function __construct(private SlugService $slugService) {}

    public function create(): View
    {
        return view('admin.home-page.sections.create', [
            'section' => new HomeSection([
                'type' => HomeSection::TYPE_FLAG,
                'is_active' => true,
                'show_view_all' => true,
                'product_limit' => 6,
                'grid_columns' => 3,
            ]),
            'categories' => $this->availableParentCategories(),
            'subcategories' => $this->availableSubcategories(),
        ]);
    }

    public function store(StoreHomeSectionRequest $request): RedirectResponse
    {
        $data = $this->prepareSectionData($request->validated());

        HomeSection::create($data);

        return redirect()
            ->route('admin.home-page.index')
            ->with('success', 'Home section created successfully.');
    }

    public function edit(HomeSection $homeSection): View
    {
        return view('admin.home-page.sections.edit', [
            'section' => $homeSection,
            'categories' => $this->availableParentCategories($homeSection->id),
            'subcategories' => $this->availableSubcategories($homeSection->id),
        ]);
    }

    public function update(UpdateHomeSectionRequest $request, HomeSection $homeSection): RedirectResponse
    {
        $data = $this->prepareSectionData($request->validated(), $homeSection->id);

        $homeSection->update($data);

        return redirect()
            ->route('admin.home-page.index')
            ->with('success', 'Home section updated successfully.');
    }

    public function destroy(HomeSection $homeSection): RedirectResponse
    {
        $homeSection->delete();

        return redirect()
            ->route('admin.home-page.index')
            ->with('success', 'Home section deleted successfully.');
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
                ->route('admin.home-page.index')
                ->with('warning', 'No sections selected.');
        }

        $count = HomeSection::query()->whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.home-page.index')
            ->with('success', $count.' section'.($count === 1 ? '' : 's').' deleted successfully.');
    }

    private function prepareSectionData(array $data, ?int $ignoreId = null): array
    {
        $title = $data['title'] ?? '';
        $data['slug'] = $this->slugService->unique($title, HomeSection::class, $ignoreId);
        $data['product_limit'] = $data['product_limit'] ?? 6;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['grid_columns'] = $data['grid_columns'] ?? 3;
        $data['product_flag'] = null;

        if (in_array($data['type'], [HomeSection::TYPE_CATEGORY, HomeSection::TYPE_SUBCATEGORY], true)) {
            // title already set from category/subcategory name in FormRequest
        } else {
            $data['category_id'] = null;
        }

        return $data;
    }

    private function usedCategoryIds(?int $ignoreSectionId = null): Collection
    {
        return HomeSection::query()
            ->whereIn('type', [HomeSection::TYPE_CATEGORY, HomeSection::TYPE_SUBCATEGORY])
            ->when($ignoreSectionId, fn ($query) => $query->where('id', '!=', $ignoreSectionId))
            ->whereNotNull('category_id')
            ->pluck('category_id');
    }

    private function availableParentCategories(?int $ignoreSectionId = null): Collection
    {
        return Category::query()
            ->parents()
            ->active()
            ->whereNotIn('id', $this->usedCategoryIds($ignoreSectionId))
            ->orderBy('name')
            ->get();
    }

    private function availableSubcategories(?int $ignoreSectionId = null): Collection
    {
        return Category::query()
            ->whereNotNull('parent_id')
            ->active()
            ->with('parent')
            ->whereNotIn('id', $this->usedCategoryIds($ignoreSectionId))
            ->orderBy('name')
            ->get();
    }
}
