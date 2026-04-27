<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\StoreSetting;
use App\Models\Order;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;

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

Route::get('/assistant', [ChatController::class, 'show'])->name('assistant');
Route::get('/chat/session', [ChatController::class, 'session'])->name('chat.session');
Route::post('/chat/profile', [ChatController::class, 'updateProfile'])->name('chat.profile');
Route::post('/chat/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');
Route::post('/chat/typing', [ChatController::class, 'typing'])->name('chat.typing');
Route::post('/chat/presence', [ChatController::class, 'presence'])->name('chat.presence');
Route::get('/chat/attachments/{message}', [ChatController::class, 'attachment'])->name('chat.attachments.show');
Route::get('/chat/attachments/{message}/download', [ChatController::class, 'downloadAttachment'])->name('chat.attachments.download');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('guest');
Route::get('/register', [LoginController::class, 'showRegisterForm'])
    ->middleware('guest')
    ->name('register');
Route::post('/register', [LoginController::class, 'register'])
    ->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])
    ->middleware('guest')
    ->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetPasswordForm'])
    ->middleware('guest')
    ->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');

// Google OAuth Routes
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])
    ->middleware('guest')
    ->name('google.login');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback'])
    ->middleware('guest')
    ->name('google.callback');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
        ->middleware('guest')
        ->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('guest')
        ->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth')->name('admin.logout');
    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])
        ->middleware('guest')
        ->name('admin.password.request');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLinkEmail'])
        ->middleware('guest')
        ->name('admin.password.email');
    Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])
        ->middleware('guest')
        ->name('admin.password.reset');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])
        ->middleware('guest')
        ->name('admin.password.update');
});

Route::get('/profile', [ProfileController::class, 'show'])
    ->middleware('auth')
    ->name('profile');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', function (Request $request) {
        $products = \App\Models\Product::with('category')->latest()->take(10)->get();
        $productSearchFilter = $request->string('product_search')->trim()->value();
        $productStatusFilter = $request->string('product_status')->value();
        $productCategoryFilter = $request->integer('product_category') ?: null;

        $allProductsQuery = \App\Models\Product::with('category', 'images')->orderBy('created_at', 'desc');

        if ($productSearchFilter !== '') {
            $allProductsQuery->where(function ($query) use ($productSearchFilter) {
                $query->where('name', 'like', '%' . $productSearchFilter . '%')
                    ->orWhere('description', 'like', '%' . $productSearchFilter . '%')
                    ->orWhere('slug', 'like', '%' . $productSearchFilter . '%');
            });
        }

        if ($productCategoryFilter) {
            $allProductsQuery->where('category_id', $productCategoryFilter);
        }

        if ($productStatusFilter !== '' && $productStatusFilter !== 'all') {
            if ($productStatusFilter === 'active') {
                $allProductsQuery->where('is_active', true)->where('is_preorder', false);
            } elseif ($productStatusFilter === 'pre_order') {
                $allProductsQuery->where('is_active', true)->where('is_preorder', true);
            } elseif ($productStatusFilter === 'inactive') {
                $allProductsQuery->where('is_active', false);
            }
        }

        $allProducts = $allProductsQuery->paginate(12, ['*'], 'product_page')->withQueryString();
        $allCategories = \App\Models\Category::with('parent')->orderBy('name')->get();
        $settings = Schema::hasTable('store_settings')
            ? StoreSetting::query()->first()
            : null;
        $statusFilter = $request->string('status')->value();
        $searchFilter = $request->string('search')->trim()->value();

        $ordersQuery = Order::query()
            ->with(['items.variant.product', 'user'])
            ->latest();

        if ($statusFilter !== '' && $statusFilter !== 'all') {
            $ordersQuery->where('status', $statusFilter);
        }

        if ($searchFilter !== '') {
            $ordersQuery->where(function ($query) use ($searchFilter) {
                $query->where('order_number', 'like', '%' . $searchFilter . '%')
                    ->orWhere('customer_name', 'like', '%' . $searchFilter . '%')
                    ->orWhere('customer_email', 'like', '%' . $searchFilter . '%');
            });
        }

        $orders = $ordersQuery->paginate(12)->withQueryString();
        $orderCounts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'ready' => Order::where('status', 'ready')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];
        $orderFilters = [
            'status' => $statusFilter === '' ? 'all' : $statusFilter,
            'search' => $searchFilter,
        ];
        $productFilters = [
            'search' => $productSearchFilter,
            'status' => $productStatusFilter === '' ? 'all' : $productStatusFilter,
            'category' => $productCategoryFilter,
        ];

        return view('admin.dashboard', compact(
            'products',
            'allProducts',
            'allCategories',
            'settings',
            'orders',
            'orderCounts',
            'orderFilters',
            'productFilters'
        ));
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

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status.update');

    Route::get('/chats', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::get('/chats/data', [AdminChatController::class, 'data'])->name('admin.chat.data');
    Route::post('/chats/{conversation}/messages', [AdminChatController::class, 'storeMessage'])->name('admin.chat.messages.store');
    Route::post('/chats/{conversation}/typing', [AdminChatController::class, 'typing'])->name('admin.chat.typing');
    Route::post('/chat-presence/{conversation?}', [AdminChatController::class, 'presence'])->name('admin.chat.presence');
    Route::post('/chats/auto-replies', [AdminChatController::class, 'upsertAutoReply'])->name('admin.chat.auto-replies.upsert');
    Route::delete('/chats/auto-replies/{autoReply}', [AdminChatController::class, 'destroyAutoReply'])->name('admin.chat.auto-replies.destroy');
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