<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Services\ProductImageService;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private SlugService $slugService,
        private ProductImageService $imageService,
    ) {}

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();

        $products = Product::query()
            ->with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'search'));
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
            'product' => new Product([
                'is_active' => true,
                'pricing_mode' => Product::PRICING_MODE_SINGLE,
            ]),
            'flagSections' => HomeSection::flagType()->ordered()->get(),
            'selectedHomeSectionIds' => [],
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validatedPayload();
        $data['slug'] = $this->slugService->unique($data['name'], Product::class);

        [$image, $gallery] = $this->resolveImages($request);
        $data['image'] = $image;
        $data['gallery'] = $gallery;

        $product = Product::create($data);
        $product->homeSections()->sync($request->homeSectionIds());

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $product->load('homeSections');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'flagSections' => HomeSection::flagType()->ordered()->get(),
            'selectedHomeSectionIds' => $product->homeSections->pluck('id')->all(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validatedPayload();
        $data['slug'] = $this->slugService->unique($data['name'], Product::class, $product->id);

        $previousPaths = array_values(array_filter([
            $product->image,
            ...($product->gallery ?? []),
        ]));

        [$image, $gallery] = $this->resolveImages($request, $previousPaths);
        $data['image'] = $image;
        $data['gallery'] = $gallery;

        $product->update($data);
        $product->homeSections()->sync($request->homeSectionIds());

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->imageService->delete($product->image);
        $this->imageService->deleteMany($product->gallery ?? []);
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
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
                ->route('admin.products.index')
                ->with('warning', 'No products selected.');
        }

        $products = Product::query()->whereIn('id', $ids)->get();

        foreach ($products as $product) {
            $this->imageService->delete($product->image);
            $this->imageService->deleteMany($product->gallery ?? []);
            $product->delete();
        }

        $count = $products->count();

        return redirect()
            ->route('admin.products.index')
            ->with('success', $count.' product'.($count === 1 ? '' : 's').' deleted successfully.');
    }

    /**
     * @param  array<int, string>  $previousPaths
     * @return array{0: ?string, 1: ?array<int, string>}
     */
    private function resolveImages(StoreProductRequest|UpdateProductRequest $request, array $previousPaths = []): array
    {
        [$keptExisting, $uploadsByIndex] = $request->imageRowPayload();
        $existingInputs = (array) $request->input('existing_images', []);

        $indexes = array_values(array_unique([
            ...array_keys($keptExisting),
            ...array_keys($uploadsByIndex),
        ]));
        sort($indexes, SORT_NUMERIC);

        $finalPaths = [];

        foreach ($indexes as $index) {
            /** @var UploadedFile|null $file */
            $file = $uploadsByIndex[$index] ?? null;

            if ($file instanceof UploadedFile) {
                $oldPath = trim((string) ($existingInputs[$index] ?? ''));
                if ($oldPath !== '') {
                    $this->imageService->delete($oldPath);
                }
                $finalPaths[] = $this->imageService->store($file);
                continue;
            }

            if (! empty($keptExisting[$index])) {
                $finalPaths[] = $keptExisting[$index];
            }
        }

        $removed = array_values(array_diff($previousPaths, $finalPaths));
        $this->imageService->deleteMany($removed);

        $image = $finalPaths[0] ?? null;
        $gallery = array_values(array_slice($finalPaths, 1));

        return [$image, $gallery === [] ? null : $gallery];
    }
}
