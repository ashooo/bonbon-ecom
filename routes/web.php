<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\StoreSetting;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderHistoryController;

Route::get('/', function () {
    $featuredProducts = collect();
    $featuredCategories = collect();

    if (Schema::hasTable('products') && Schema::hasTable('categories')) {
        $featuredProducts = \App\Models\Product::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        $featuredCategories = \App\Models\Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->take(8)
            ->get();
    }

    return view('pages.home', compact('featuredProducts', 'featuredCategories'));
});

Route::get('/products', function (Request $request) {
    $products = collect();
    $categories = collect();

    if (Schema::hasTable('products') && Schema::hasTable('categories')) {
        $categories = \App\Models\Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $productsQuery = \App\Models\Product::with('category')
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->value();

            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $productsQuery->where('category_id', $request->integer('category'));
        }

        match ($request->input('sort')) {
            'price_low' => $productsQuery->orderBy('sale_price')->orderBy('price'),
            'price_high' => $productsQuery->orderByDesc('sale_price')->orderByDesc('price'),
            'latest' => $productsQuery->latest(),
            default => $productsQuery->orderBy('name'),
        };

        $products = $productsQuery->paginate(9)->withQueryString();
    }

    return view('products.index', compact('products', 'categories'));
});

Route::get('/product/{slug}', function ($slug) {
    abort_unless(Schema::hasTable('products') && Schema::hasTable('categories'), 404);

    $product = \App\Models\Product::with(['category', 'images'])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

    $relatedProducts = \App\Models\Product::with('category')
        ->where('is_active', true)
        ->where('id', '!=', $product->id)
        ->when($product->category_id, function ($query) use ($product) {
            $query->where('category_id', $product->category_id);
        })
        ->latest()
        ->take(3)
        ->get();

    return view('products.show', compact('product', 'relatedProducts'));
})->name('products.show');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{item}', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/{item}/increment', [CartController::class, 'increment'])->name('cart.increment');
Route::post('/cart/{item}/decrement', [CartController::class, 'decrement'])->name('cart.decrement');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
Route::post('/orders/lookup', [OrderHistoryController::class, 'lookup'])->name('orders.lookup');

Route::get('/customize', function () {
    return view('pages.customize');
});

Route::get('/assistant', function () {
    return view('pages.assistant');
})->name('assistant');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Google OAuth Routes
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/profile', [ProfileController::class, 'show'])
    ->middleware('auth')
    ->name('profile');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', function () {
        $products = \App\Models\Product::with('category')->latest()->take(10)->get();
        $allProducts = \App\Models\Product::with('category', 'images')->orderBy('created_at', 'desc')->get();
        $allCategories = \App\Models\Category::with('parent')->orderBy('name')->get();
        $settings = Schema::hasTable('store_settings')
            ? StoreSetting::query()->first()
            : null;

        return view('admin.dashboard', compact('products', 'allProducts', 'allCategories', 'settings'));
    })->name('admin.dashboard');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');

    // Category Routes
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);
    Route::get('/categories-data', [\App\Http\Controllers\Admin\CategoryController::class, 'getCategories'])->name('admin.categories.data');

    // Product Routes
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);
    Route::delete('/products/images/{image}', [\App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('admin.products.images.delete');
    Route::post('/products/images/order', [\App\Http\Controllers\Admin\ProductController::class, 'updateImageOrder'])->name('admin.products.images.order');
});

Route::post('/profile', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

Route::post('/profile/address', [ProfileController::class, 'storeAddress'])
    ->middleware('auth')
    ->name('profile.address.store');

Route::put('/profile/address/{address}', [ProfileController::class, 'updateAddress'])
    ->middleware('auth')
    ->name('profile.address.update');

Route::delete('/profile/address/{address}', [ProfileController::class, 'deleteAddress'])
    ->middleware('auth')
    ->name('profile.address.delete');

Route::post('/profile/payment-method', [ProfileController::class, 'storePaymentMethod'])
    ->middleware('auth')
    ->name('profile.payment.store');

Route::put('/profile/payment-method/{paymentMethod}', [ProfileController::class, 'updatePaymentMethod'])
    ->middleware('auth')
    ->name('profile.payment.update');

Route::delete('/profile/payment-method/{paymentMethod}', [ProfileController::class, 'deletePaymentMethod'])
    ->middleware('auth')
    ->name('profile.payment.delete');

Route::post('/profile/order/{order}/reorder', [ProfileController::class, 'reorder'])
    ->middleware('auth')
    ->name('profile.order.reorder');
