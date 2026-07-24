<?php

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminIncomeController;
use App\Http\Controllers\Admin\AdminAppHomeSettingController;
use App\Http\Controllers\Admin\AdminMessageController;
use App\Http\Controllers\Admin\AdminHomeSettingController;
use App\Http\Controllers\Admin\RegisteredUserController;
use App\Http\Controllers\PageEventController;
use App\Http\Controllers\CouponController;
use App\Models\Admin\Admin;
use App\Http\Controllers\Admin\Reports\OrderReportController;

// Sitemap.xml
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/services/{slug}', [\App\Http\Controllers\ServiceController::class, 'show'])->name('services.show');
Route::get('/process', [HomeController::class, 'process'])->name('process');
Route::get('/gallery', [\App\Http\Controllers\GalleryController::class, 'index'])->name('gallery');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('testimonials');
// Prevent accidental GET to the POST-only contact endpoint
Route::get('/contact/send', function () {
    return redirect()->route('contact');
});
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');
Route::post('/reviews', [HomeController::class, 'storeReview'])->name('reviews.store');
Route::post('/events/track', [PageEventController::class, 'store'])->name('events.track');
Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');
Route::post('/coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');

/*
|--------------------------------------------------------------------------
| Projects
|--------------------------------------------------------------------------
*/
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');

/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Language Switcher
|--------------------------------------------------------------------------
*/
Route::get('/lang/{locale}', function ($locale) {
    $supported = config('app.frontend_locales', ['pt', 'en']);
    if (in_array($locale, $supported)) {
        session(['app_locale' => $locale]);
    }
    return back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Authentication (for normal users)
|--------------------------------------------------------------------------
*/
Auth::routes(['login' => false, 'verify' => true]);
// Custom login route with guest.custom middleware
Route::middleware('guest.custom')->group(function () {
    Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| User Dashboard + Cart (requires login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/orders', [HomeController::class, 'orders'])->name('dashboard.orders');
    Route::get('/dashboard/profile', [HomeController::class, 'profile'])->name('dashboard.profile');
    Route::post('/dashboard/profile', [HomeController::class, 'updateProfile'])->name('dashboard.profile.update');
});

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/cart/increase/{id}', [CartController::class, 'increase'])->name('cart.increase');
Route::post('/cart/decrease/{id}', [CartController::class, 'decrease'])->name('cart.decrease');

Route::middleware('cart.hasProducts')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');
    Route::get('/checkout/check-email', [CheckoutController::class, 'checkEmail'])->name('checkout.checkEmail');
});

Route::get('/checkout/thank-you/{order}', [CheckoutController::class, 'thankYou'])->name('checkout.thankyou');
Route::get('/checkout/invoice/{order}', [CheckoutController::class, 'downloadInvoice'])->name('checkout.invoice');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // 🔐 صفحة تسجيل الدخول الخاصة بالمدير
    Route::middleware('guest.custom:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    });

    // 🧱 لوحة التحكم ومساراتها (تتطلب صلاحيات مدير)
    Route::middleware('admin.auth')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // 📂 Projects CRUD
        Route::resource('projects', AdminProjectController::class);

        // ✅ حذف صورة من الجاليري
        Route::delete('project-image/{image}', [AdminProjectController::class, 'deleteImage'])
            ->name('projects.image.delete');

        // ✅ حذف الصورة الرئيسية فقط
        Route::delete('projects/{project}/mainimage', [AdminProjectController::class, 'deleteMainImage'])
            ->name('projects.mainimage.delete');
        Route::post(
            'projects/{project}/add-images',
            [AdminProjectController::class, 'addImages']
        )->name('projects.addImages');
        Route::resource('products', AdminProductController::class);

        // Delete ONLY main image
        Route::delete('products/{product}/main-image', [AdminProductController::class, 'deleteMainImage'])
            ->name('products.mainimage.delete');

        // Delete single gallery image
        Route::delete('products/gallery/{image}', [AdminProductController::class, 'deleteGalleryImage'])
            ->name('products.image.delete');

        // Add gallery images
        Route::post('products/{product}/gallery', [AdminProductController::class, 'addImages'])
            ->name('products.addImages');

        // categories
        Route::get('/categories', [App\Http\Controllers\Admin\AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [App\Http\Controllers\Admin\AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{id}', [App\Http\Controllers\Admin\AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [App\Http\Controllers\Admin\AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Filter Products by Category
        Route::get('/products/category/{id}', [App\Http\Controllers\Admin\AdminProductController::class, 'filterByCategory'])->name('products.category');

        // Project categories inline management
        Route::post('project-categories', [\App\Http\Controllers\Admin\ProjectCategoryController::class, 'store'])->name('projects.categories.store');
        Route::put('project-categories/{category}', [\App\Http\Controllers\Admin\ProjectCategoryController::class, 'update'])->name('projects.categories.update');
        Route::delete('project-categories/{category}', [\App\Http\Controllers\Admin\ProjectCategoryController::class, 'destroy'])->name('projects.categories.destroy');

        // Reviews moderation
        Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::patch('reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
        Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // Admin users CRUD
        Route::resource('admin-users', AdminUserController::class)->parameters(['admin-users' => 'admin_user']);

        // Registered website users
        Route::get('users', [RegisteredUserController::class, 'index'])->name('users.index');

        // Coupons
        Route::resource('coupons', AdminCouponController::class)->only(['index','store','update','destroy']);

        // Orders
        Route::resource('orders', AdminOrderController::class)->only(['index','show','update','destroy']);
        Route::post('orders/{order}/refund', [AdminOrderController::class, 'refund'])->name('orders.refund');
        Route::get('orders-export', [AdminOrderController::class, 'export'])->name('orders.export');
        Route::post('orders-bulk-update', [AdminOrderController::class, 'bulkUpdate'])->name('orders.bulk-update');
        Route::post('orders-bulk-delete', [AdminOrderController::class, 'bulkDelete'])->name('orders.bulk-delete');
        Route::get('home-settings', [AdminHomeSettingController::class, 'edit'])->name('home.settings.edit');
        Route::post('home-settings', [AdminHomeSettingController::class, 'update'])->name('home.settings.update');
        Route::get('app-home-settings', [AdminAppHomeSettingController::class, 'edit'])->name('app-home-settings.edit');
        Route::post('app-home-settings', [AdminAppHomeSettingController::class, 'update'])->name('app-home-settings.update');
        Route::get('income', [AdminIncomeController::class, 'index'])->name('income.index');
        Route::get('income/export', [AdminIncomeController::class, 'export'])->name('income.export');
        Route::get('reports/orders', [OrderReportController::class, 'index'])->name('reports.orders.index');
        Route::get('reports/orders/export', [OrderReportController::class, 'export'])->name('reports.orders.export');

        // Contact Messages
        Route::get('messages', [AdminMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [AdminMessageController::class, 'show'])->name('messages.show');
        Route::delete('messages/{message}', [AdminMessageController::class, 'destroy'])->name('messages.destroy');
        Route::post('messages/{message}/mark-read', [AdminMessageController::class, 'markRead'])->name('messages.mark-read');
        Route::post('messages/{message}/mark-unread', [AdminMessageController::class, 'markUnread'])->name('messages.mark-unread');
    });
});
