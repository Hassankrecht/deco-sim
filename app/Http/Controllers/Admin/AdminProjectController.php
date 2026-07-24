<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminProjectController extends Controller
{
    /**
     * 🟢 عرض كل المشاريع
     */
    public function index()
    {
        $categoryId = request('category');
        $status     = request('status');
        $q          = request('q');
        $sort       = request('sort', 'newest');

        $projectsQuery = Project::with(['images', 'categories'])
            ->when($categoryId, fn($qq) => $qq->whereHas('categories', fn($c) => $c->where('project_categories.id', $categoryId)))
            ->when($status, fn($qq) => $qq->where('status', $status))
            ->when($q, function($qq) use ($q) {
                $qq->where(function($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%");
                });
            });

        switch ($sort) {
            case 'oldest':
                $projectsQuery->orderBy('id', 'asc');
                break;
            case 'status':
                $projectsQuery->orderBy('status', 'asc');
                break;
            default:
                $projectsQuery->orderBy('id', 'desc');
        }

        $projects = $projectsQuery
            ->paginate(10)
            ->appends(request()->query());

        $categories = ProjectCategory::with([
                'translations',
                'children' => fn($q) => $q->with('translations')->withCount('projects')->orderBy('order'),
            ])
            ->withCount('projects')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        $totalProjects = (clone $projectsQuery)->count();
        $statusCounts  = (clone $projectsQuery)
            ->reorder()
            ->select('projects.status', DB::raw('COUNT(*) as total'))
            ->groupBy('projects.status')
            ->pluck('total','projects.status');

        return view('admins.projects.index', [
            'projects' => $projects,
            'categories' => $categories,
            'categoryId' => $categoryId,
            'search' => $q,
            'sort' => $sort,
            'statusFilter' => $status,
            'totalProjects' => $totalProjects,
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * 🟡 صفحة إنشاء مشروع جديد
     */
    public function create()
    {
        $categories = ProjectCategory::with(['children.translations','translations'])->get();
        return view('admins.projects.create', compact('categories'));
    }

    /**
     * 🟢 حفظ مشروع جديد
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'date'        => 'nullable|date',
            'status'      => 'required|integer|in:1,2,3,4', // <== هنا أرقام فقط
            'main_image'  => 'nullable|image|mimes:jpg,png,jpeg,webp|max:10000',
            'gallery.*'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:10000',
            'categories'  => 'nullable|array',
            'categories.*'=> 'exists:project_categories,id',
            'translations'=> 'nullable|array',
        ]);

        // رفع الصورة الرئيسية
        if ($request->hasFile('main_image')) {
            $data['main_image'] = $this->storeAssetTo('projects', $request->file('main_image'));
        }

        $project = Project::create($data);

        // ربط الفئات
        if ($request->filled('categories')) {
            $project->categories()->sync($request->categories);
        }

        // ترجمات
        $translations = $request->input('translations', []);
        $fallback = config('app.locale', 'en');
        $locales = config('app.frontend_locales', ['pt', 'en']);
        foreach ($locales as $locale) {
            $title = $translations[$locale]['title'] ?? $data['title'];
            $desc  = $translations[$locale]['description'] ?? ($data['description'] ?? '');
            $project->translations()->updateOrCreate(
                ['locale' => $locale],
                ['title' => $title, 'description' => $desc]
            );
        }

        // رفع صور الجاليري
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $path = $this->storeAssetTo('project_images', $image);
                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully!');
    }

    /**
     * ✏️ صفحة التعديل
     */
    public function edit(Project $project)
    {
        $project->load('images', 'categories');
        $categories = ProjectCategory::with(['children.translations','translations'])->get();
        return view('admins.projects.edit', compact('project', 'categories'));
    }


    /**
     * 🔄 تحديث مشروع
     */
    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'date'        => 'nullable|date',

            // ❗ أهم نقطة — status لازم يكون رقم
            'status'      => 'required|integer|in:1,2,3,4',

            'main_image'  => 'nullable|image|mimes:jpg,png,jpeg,webp|max:10000',
            'gallery.*'   => 'nullable|image|mimes:jpg,png,jpeg,webp|max:10000',
            'categories'  => 'nullable|array',
            'categories.*'=> 'exists:project_categories,id',
            'translations'=> 'nullable|array',
        ]);

        // تحديث الصورة الرئيسية لو تم رفع واحدة جديدة
        if ($request->hasFile('main_image')) {
            $this->deleteAsset($project->main_image);
            $data['main_image'] = $this->storeAssetTo('projects', $request->file('main_image'));
        }

        $project->update($data);

        // تحديث الفئات
        $project->categories()->sync($request->categories ?? []);

        // تحديث الترجمات
        $translations = $request->input('translations', []);
        $fallback = config('app.locale', 'en');
        $locales = config('app.frontend_locales', ['pt', 'en']);
        foreach ($locales as $locale) {
            $title = $translations[$locale]['title'] ?? $data['title'];
            $desc  = $translations[$locale]['description'] ?? ($data['description'] ?? '');
            $project->translations()->updateOrCreate(
                ['locale' => $locale],
                ['title' => $title, 'description' => $desc]
            );
        }

        // إضافة صور جديدة
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $img) {
                $path = $this->storeAssetTo('project_images', $img);
                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project updated successfully!');
    }


    /**
     * ❌ حذف صورة واحدة من الجاليري
     */
    public function deleteImage(ProjectImage $image)
    {
        $this->deleteAsset($image->image_path);

        $image->delete();

        return back()->with('success', 'Gallery image deleted!');
    }


    /**
     * ❌ حذف الصورة الرئيسية فقط
     */
    public function deleteMainImage(Project $project)
    {
        $this->deleteAsset($project->main_image);

        $project->update(['main_image' => null]);

        return back()->with('success', 'Main image deleted!');
    }


    /**
     * 🗑️ حذف مشروع كامل
     */
    public function destroy(Project $project)
    {
        // حذف الصورة الرئيسية
        $this->deleteAsset($project->main_image);

        // حذف الجاليري
        foreach ($project->images as $img) {
            $this->deleteAsset($img->image_path);
            $img->delete();
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted!');
    }

    /**
     * إدارة الفئات (إضافة/تعديل/حذف) بسيطة من نفس الصفحة
     */
    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'nullable|string|max:255|unique:project_categories,slug',
            'parent_id' => 'nullable|exists:project_categories,id',
            'order'     => 'nullable|integer|min:0',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        ProjectCategory::create($data);

        return back()->with('success', 'Category added.');
    }

    public function updateCategory(Request $request, ProjectCategory $category)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'nullable|string|max:255|unique:project_categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:project_categories,id',
            'order'     => 'nullable|integer|min:0',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category->update($data);

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(ProjectCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted.');
    }

    
    public function addImages(Request $request, Project $project)
    {
        $request->validate([
            'gallery.*' => 'image|mimes:jpg,png,jpeg,webp|max:10000'
        ]);

        if ($request->hasFile('gallery')) {

            foreach ($request->file('gallery') as $image) {
                $path = $this->storeAssetTo('project_images', $image);

                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                ]);
            }
        }

        return back()->with('success', '✨ New images uploaded successfully!');
    }

    private function storeAssetTo(string $folder, UploadedFile $file): string
    {
        $dir = public_path("assets/{$folder}");
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $name = $file->hashName();
        $file->move($dir, $name);
        // نخزن المسار كما يراه المتصفح داخل assets/...
        return "assets/{$folder}/{$name}";
    }

    private function deleteAsset(?string $path): void
    {
        if (!$path) {
            return;
        }

        // إذا كان المسار يبدأ بـ public/assets، نحوله لمسار فعلي على السيرفر
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
