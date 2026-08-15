<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubCategoryRequest;
use App\Http\Requests\Admin\UpdateSubCategoryRequest;
use App\Models\Category;
use App\Services\ProductImageService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubCategoryController extends Controller
{
    public function __construct(
        private SlugService $slugService,
        private ProductImageService $imageService,
    ) {}

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();

        $subcategories = Category::query()
            ->whereNotNull('parent_id')
            ->with('parent')
            ->withCount('products')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('parent', function ($parent) use ($search) {
                            $parent->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.subcategories.index', compact('subcategories', 'search'));
    }

    public function create(): View
    {
        return view('admin.subcategories.create', [
            'subcategory' => new Category(['is_active' => true]),
            'parents' => Category::parents()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreSubCategoryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image']);
        $data['slug'] = $data['slug'] ?: $this->slugService->unique($data['name'], Category::class);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['show_on_home'] = false;
        $data['menu_column'] = null;
        $data['menu_row'] = null;
        $data['home_sort_order'] = 0;
        $data['image'] = $this->imageService->replace(null, $request->file('image'), 'categories');

        Category::create($data);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory created successfully.');
    }

    public function edit(Category $subcategory): View
    {
        abort_if($subcategory->parent_id === null, 404);

        return view('admin.subcategories.edit', [
            'subcategory' => $subcategory,
            'parents' => Category::parents()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateSubCategoryRequest $request, Category $subcategory): RedirectResponse
    {
        abort_if($subcategory->parent_id === null, 404);

        $data = $request->safe()->except(['image']);
        $data['slug'] = $data['slug'] ?: $this->slugService->unique($data['name'], Category::class, $subcategory->id);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['show_on_home'] = false;
        $data['menu_column'] = null;
        $data['menu_row'] = null;
        $data['home_sort_order'] = 0;
        $data['image'] = $this->imageService->replace($subcategory->image, $request->file('image'), 'categories');

        $subcategory->update($data);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(Category $subcategory): RedirectResponse
    {
        abort_if($subcategory->parent_id === null, 404);

        if ($subcategory->products()->exists()) {
            return redirect()
                ->route('admin.subcategories.index')
                ->with('error', 'Cannot delete a subcategory that has products.');
        }

        $this->imageService->delete($subcategory->image);
        $subcategory->delete();

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory deleted successfully.');
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
                ->route('admin.subcategories.index')
                ->with('warning', 'No subcategories selected.');
        }

        $subcategories = Category::query()
            ->whereNotNull('parent_id')
            ->whereIn('id', $ids)
            ->withCount('products')
            ->get();

        $deleted = 0;
        $skipped = 0;

        foreach ($subcategories as $subcategory) {
            if ($subcategory->products_count > 0) {
                $skipped++;
                continue;
            }

            $this->imageService->delete($subcategory->image);
            $subcategory->delete();
            $deleted++;
        }

        if ($deleted === 0 && $skipped > 0) {
            return redirect()
                ->route('admin.subcategories.index')
                ->with('error', 'Could not delete selected subcategories. Remove products first.');
        }

        $message = $deleted.' subcategor'.($deleted === 1 ? 'y' : 'ies').' deleted successfully.';
        if ($skipped > 0) {
            $message .= ' '.$skipped.' skipped (has products).';
        }

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', $message);
    }
}
