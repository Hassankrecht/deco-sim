<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectCategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'nullable|string|max:255|unique:project_categories,slug',
            'parent_id' => 'nullable|exists:project_categories,id',
            'order'     => 'nullable|integer|min:0',
            'translations' => 'nullable|array',
        ]);

        $data['order'] = $data['order'] ?? 0;
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $category = ProjectCategory::create($data);

        // translations
        $translations = $request->input('translations', []);
        $fallback = config('app.locale', 'en');
        $locales = config('app.frontend_locales', ['pt', 'en']);
        foreach ($locales as $locale) {
            $name = $translations[$locale]['name'] ?? ($locale === $fallback ? $data['name'] : $data['name']);
            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                ['name' => $name]
            );
        }

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, ProjectCategory $category)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'nullable|string|max:255|unique:project_categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:project_categories,id',
            'order'     => 'nullable|integer|min:0',
            'translations' => 'nullable|array',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $category->update($data);

        // translations
        $translations = $request->input('translations', []);
        $fallback = config('app.locale', 'en');
        $locales = config('app.frontend_locales', ['pt', 'en']);
        foreach ($locales as $locale) {
            $name = $translations[$locale]['name'] ?? ($locale === $fallback ? $data['name'] : $data['name']);
            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                ['name' => $name]
            );
        }

        return back()->with('success', 'Category updated.');
    }

    public function destroy(ProjectCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }
}
