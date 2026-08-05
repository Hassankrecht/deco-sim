<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /* ============================================================
        INDEX
    ============================================================ */
    public function index(Request $request)
    {
        // الفئات بشكل هرمي (أب/ابن)
        $parentCategories = Category::with(['translations', 'children.translations'])
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
        $childCategories = Category::with('translations')
            ->whereNotNull('parent_id')
            ->orderBy('order')
            ->get();

        if ($parentCategories->isEmpty() && $childCategories->isEmpty()) {
            $parentCategories = Category::with('translations')->get()->map(function ($cat) {
                $cat->setRelation('children', collect());
                return $cat;
            });
            $childCategories = $parentCategories;
        }

        $categoryId = $request->query('category');
        $q = $request->query('q');
        $sort = $request->query('sort', 'newest');

        $productsQuery = Product::with(['category.translations', 'translations'])
            ->when($categoryId, fn($qq) => $qq->where('category_id', $categoryId))
            ->when($q, function($qq) use ($q) {
                $qq->where(function($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            });

        switch ($sort) {
            case 'price_asc':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'oldest':
                $productsQuery->orderBy('id', 'asc');
                break;
            default:
                $productsQuery->orderBy('id', 'desc');
        }

        $products = $productsQuery->paginate(12)->appends($request->query());

        $allCategories = Category::with('translations')->orderBy('order')->get();
        $productCounts = Product::selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $productsTotal = Product::count();
        $parentCount = $parentCategories->count();
        $childCount = $childCategories->count();

        return view('admins.products.index', [
            'products' => $products,
            'parentCategories' => $parentCategories,
            'childCategories' => $childCategories,
            'categoryId' => $categoryId,
            'allCategories' => $allCategories,
            'productCounts' => $productCounts,
            'productsTotal' => $productsTotal,
            'parentCount' => $parentCount,
            'childCount' => $childCount,
            'search' => $q,
            'sort' => $sort,
        ]);
    }

    /* ============================================================
        CREATE
    ============================================================ */
    public function create()
    {
        $categories = Category::all();
        return view('admins.products.create', compact('categories'));
    }

    /* ============================================================
        STORE
    ============================================================ */
    public function store(StoreProductRequest $request)
{
    $validated = $request->validated();

    // Create product without image first
        $product = Product::create([
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'image'       => null, // initially empty
        ]);

    // Sync translations for admin content locales
    $translations = $request->input('translations', []);
    $locales = config('app.admin_content_locales', ['pt', 'en']);

    foreach ($locales as $locale) {
        $title = $translations[$locale]['title'] ?? $validated['title'];
        $desc  = $translations[$locale]['description'] ?? ($validated['description'] ?? '');

        $product->translations()->updateOrCreate(
            ['locale' => $locale],
            [
                'title'       => $title,
                'description' => $desc,
            ]
        );
    }

    /* -------------------- SAVE MAIN IMAGE -------------------- */
    if ($request->hasFile('image')) {
        $product->image = $this->storeAssetTo('products', $request->image);
        $product->save();
    }

    /* -------------------- SAVE GALLERY -------------------- */
    if ($request->hasFile('gallery')) {
        foreach ($request->gallery as $file) {
            $gname = $this->storeAssetTo('product_gallery', $file);
            ProductImage::create([
                'product_id' => $product->id,
                'image'      => $gname,
            ]);
        }
    }

    // Save product components
    if ($request->has('components')) {
        foreach ($request->components as $component) {
            $translations = isset($component['name_translations']) ? array_filter($component['name_translations']) : [];
            if (!empty($translations)) {
                // Use the current app locale, or the first available translation as fallback for 'name'
                $name = $translations['pt'] ?? ($translations['en'] ?? collect($translations)->first());
                $product->productComponents()->create([
                    'name' => $name,
                    'name_translations' => $translations,
                    'width' => $component['width'] ?? null,
                    'length' => $component['length'] ?? null,
                    'height' => $component['height'] ?? null,
                    'material' => $component['material'] ?? null,
                ]);
            }
        }
    }

    // After creating a product, redirect to the edit page so the admin can
    // immediately add gallery images or change the main image.
    return redirect()->route('admin.products.edit', $product->id)
                     ->with('success', 'Product created successfully! You can add images or update details below.');
}


    /* ============================================================
        EDIT
    ============================================================ */
    public function edit(Product $product)
    {
        $product->load(['images', 'translations']);
        $categories = Category::all();
        return view('admins.products.edit', compact('product', 'categories'));
    }

    /* ============================================================
        UPDATE
    ============================================================ */
    public function update(UpdateProductRequest $request, Product $product)
{
    // Validate WITHOUT touching image directly
    $validated = $request->validated();

    // Update normal fields manually (to avoid overwriting image)
    $product->category_id = $validated['category_id'];
    $product->title       = $validated['title'];
    $product->description = $validated['description'] ?? null;
    $product->price       = $validated['price'];
    $product->save();

    // Update translations based on submitted locales
    $translations = $request->input('translations', []);
    $locales = config('app.admin_content_locales', ['pt', 'en']);

    foreach ($locales as $locale) {
        $title = $translations[$locale]['title'] ?? $validated['title'];
        $desc  = $translations[$locale]['description'] ?? ($validated['description'] ?? '');

        $product->translations()->updateOrCreate(
            ['locale' => $locale],
            [
                'title'       => $title,
                'description' => $desc,
            ]
        );
    }

    /* ---------------- UPDATE MAIN IMAGE ---------------- */
    if ($request->hasFile('image')) {

        // Delete old main image (if exists)
        $this->deleteAsset($product->image);

        // Upload new image
        $product->image = $this->storeAssetTo('products', $request->image);
        $product->save();
    }

    // Update product components
    $product->productComponents()->delete(); // Remove old components
    if ($request->has('components')) {
        foreach ($request->components as $component) {
            $translations = isset($component['name_translations']) ? array_filter($component['name_translations']) : [];
            if (!empty($translations)) {
                $name = $translations['pt'] ?? ($translations['en'] ?? collect($translations)->first());
                $product->productComponents()->create([
                    'name' => $name,
                    'name_translations' => $translations,
                    'width' => $component['width'] ?? null,
                    'length' => $component['length'] ?? null,
                    'height' => $component['height'] ?? null,
                    'material' => $component['material'] ?? null,
                ]);
            }
        }
    }

    return redirect()
        ->route('admin.products.edit', $product->id)
        ->with('success', 'Product updated successfully!');
}

    /* ============================================================
        DELETE MAIN IMAGE
    ============================================================ */
    public function deleteMainImage(Product $product)
    {
        $this->deleteAsset($product->image);

        $product->update(['image' => null]);

        return back()->with('success', 'Main image deleted!');
    }

    /* ============================================================
        DELETE ONE GALLERY IMAGE
    ============================================================ */
    public function deleteGalleryImage(ProductImage $image)
    {
        $this->deleteAsset($image->image);

        $image->delete();

        return back()->with('success', 'Gallery image deleted!');
    }

    /* ============================================================
        ADD IMAGES TO GALLERY
    ============================================================ */
    public function addImages(\Illuminate\Http\Request $request, Product $product)
    {
        $request->validate([
            'gallery.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        foreach ($request->file('gallery') as $file) {

            $galleryName = $this->storeAssetTo('product_gallery', $file);

            ProductImage::create([
                'product_id' => $product->id,
                'image'      => $galleryName,
            ]);
        }

        return back()->with('success', 'Images uploaded successfully!');
    }

    /* ============================================================
        DESTROY
    ============================================================ */
    public function destroy(Product $product)
    {
        // Delete main image
        $this->deleteAsset($product->image);

        // Delete gallery
        foreach ($product->images as $img) {
            $this->deleteAsset($img->image);
            $img->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product deleted successfully!');
    }
    public function filterByCategory(Request $request, $id)
    {
        $request->merge(['category' => $id]);
        return $this->index($request);
    }

    private function storeAssetTo(string $folder, UploadedFile $file): string
    {
        // نحفظ داخل assets/<folder> حتى يكون الرابط متاحًا عبر asset()
        $dir = public_path("assets/{$folder}");
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = $file->hashName();
        $file->move($dir, $name);

        // نخزن المسار بشكل موحد assets/...
        return "assets/{$folder}/{$name}";
    }

    private function deleteAsset(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (str_starts_with($path, 'public/assets/')) {
            $full = public_path(substr($path, strlen('public/')));
            if (file_exists($full)) {
                @unlink($full);
            }
            return;
        }

        if (str_starts_with($path, 'assets/')) {
            $full = public_path($path);
            if (file_exists($full)) {
                @unlink($full);
            }
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
