<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\ProductImageService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private SlugService $slugService,
        private ProductImageService $imageService,
    ) {}

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();

        $categories = Category::query()
            ->parents()
            ->withCount(['products', 'children'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(['is_active' => true]),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image']);
        $data['parent_id'] = null;
        $data['slug'] = $data['slug'] ?: $this->slugService->unique($data['name'], Category::class);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['home_sort_order'] = $data['home_sort_order'] ?? 0;
        $data['image'] = $this->imageService->replace(null, $request->file('image'), 'categories');

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        abort_if($category->parent_id !== null, 404);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        abort_if($category->parent_id !== null, 404);

        $data = $request->safe()->except(['image']);
        $data['parent_id'] = null;
        $data['slug'] = $data['slug'] ?: $this->slugService->unique($data['name'], Category::class, $category->id);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['home_sort_order'] = $data['home_sort_order'] ?? 0;
        $data['image'] = $this->imageService->replace($category->image, $request->file('image'), 'categories');

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        abort_if($category->parent_id !== null, 404);

        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete a category that has products.');
        }

        if ($category->children()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete a category that has subcategories. Remove subcategories first.');
        }

        $this->imageService->delete($category->image);
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
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
                ->route('admin.categories.index')
                ->with('warning', 'No categories selected.');
        }

        $categories = Category::query()
            ->whereNull('parent_id')
            ->whereIn('id', $ids)
            ->withCount(['products', 'children'])
            ->get();

        $deleted = 0;
        $skipped = 0;

        foreach ($categories as $category) {
            if ($category->products_count > 0 || $category->children_count > 0) {
                $skipped++;
                continue;
            }

            $this->imageService->delete($category->image);
            $category->delete();
            $deleted++;
        }

        if ($deleted === 0 && $skipped > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Could not delete selected categories. Remove products/subcategories first.');
        }

        $message = $deleted.' categor'.($deleted === 1 ? 'y' : 'ies').' deleted successfully.';
        if ($skipped > 0) {
            $message .= ' '.$skipped.' skipped (has products or subcategories).';
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', $message);
    }
}
