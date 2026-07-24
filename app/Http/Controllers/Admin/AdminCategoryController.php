<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductCategoryTranslation;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['translations', 'parent.translations'])
            ->orderBy('id', 'desc')
            ->get();
        return view('admins.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer',
            'translations' => 'nullable|array',
        ]);

        Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
        ]);

        $category = Category::latest()->first();
        $translations = $request->input('translations', []);
        $fallback = config('app.locale', 'en');
        $locales = config('app.frontend_locales', ['pt', 'en']);
        foreach ($locales as $locale) {
            $name = $translations[$locale]['name'] ?? ($locale === $fallback ? $request->name : $request->name);
            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                ['name' => $name]
            );
        }

        return back()->with('success', 'Category added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $id,
            'order' => 'nullable|integer',
            'translations' => 'nullable|array',
        ]);

        $cat = Category::findOrFail($id);
        $cat->name = $request->name;
        $cat->parent_id = $request->parent_id;
        $cat->order = $request->order ?? $cat->order;
        $cat->save();

        $translations = $request->input('translations', []);
        $fallback = config('app.locale', 'en');
        $locales = config('app.frontend_locales', ['pt', 'en']);
        foreach ($locales as $locale) {
            $name = $translations[$locale]['name'] ?? ($locale === $fallback ? $request->name : $request->name);
            $cat->translations()->updateOrCreate(
                ['locale' => $locale],
                ['name' => $name]
            );
        }

        return back()->with('success', 'Category updated.');
    }

    public function destroy($id)
    {
        Category::destroy($id);

        return back()->with('success', 'Category deleted.');
    }
}
