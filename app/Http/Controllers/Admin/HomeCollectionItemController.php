<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHomeCollectionItemRequest;
use App\Http\Requests\Admin\UpdateHomeCollectionItemRequest;
use App\Models\Category;
use App\Models\HomeCollectionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeCollectionItemController extends Controller
{
    public function create(): View
    {
        return view('admin.home-page.collections.create', [
            'item' => new HomeCollectionItem([
                'is_active' => true,
                'sort_order' => 0,
            ]),
            ...$this->formOptions(),
        ]);
    }

    public function store(StoreHomeCollectionItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['item_type']);

        HomeCollectionItem::create($data);

        return redirect()
            ->route('admin.home-page.index')
            ->with('success', 'Collection item added successfully.');
    }

    public function edit(HomeCollectionItem $homeCollectionItem): View
    {
        $homeCollectionItem->load('category.parent');

        return view('admin.home-page.collections.edit', [
            'item' => $homeCollectionItem,
            ...$this->formOptions($homeCollectionItem->id),
        ]);
    }

    public function update(
        UpdateHomeCollectionItemRequest $request,
        HomeCollectionItem $homeCollectionItem
    ): RedirectResponse {
        $data = $request->validated();
        unset($data['item_type']);

        $homeCollectionItem->update($data);

        return redirect()
            ->route('admin.home-page.index')
            ->with('success', 'Collection item updated successfully.');
    }

    public function destroy(HomeCollectionItem $homeCollectionItem): RedirectResponse
    {
        $homeCollectionItem->delete();

        return redirect()
            ->route('admin.home-page.index')
            ->with('success', 'Collection item removed successfully.');
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
                ->with('warning', 'No collection items selected.');
        }

        $count = HomeCollectionItem::query()->whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.home-page.index')
            ->with('success', $count.' collection item'.($count === 1 ? '' : 's').' removed successfully.');
    }

    /**
     * @return array{parentCategories: Collection, subcategories: Collection}
     */
    private function formOptions(?int $ignoreItemId = null): array
    {
        $usedIds = HomeCollectionItem::query()
            ->when($ignoreItemId, fn ($query) => $query->where('id', '!=', $ignoreItemId))
            ->pluck('category_id');

        $available = Category::query()
            ->active()
            ->with('parent')
            ->whereNotIn('id', $usedIds)
            ->orderBy('name')
            ->get();

        return [
            'parentCategories' => $available->whereNull('parent_id')->values(),
            'subcategories' => $available->whereNotNull('parent_id')->values(),
        ];
    }
}
