<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\StoreSetting;
use App\Models\Order;
use App\Models\User;
use App\Models\Variant;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomizeController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;

Route::get('/', function () {
    $featuredProducts = collect();
    $featuredCategories = collect();
    $shelfProducts = collect();

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

        $shelfProducts = \App\Models\Product::with(['category', 'images', 'variants' => fn ($q) => $q->where('is_active', true)->orderByDesc('is_default')->orderBy('display_order')])
            ->where('is_active', true)
            ->latest()
            ->get();
    }

    return view('pages.home', compact('featuredProducts', 'featuredCategories', 'shelfProducts'));
});

Route::get('/test-products', function (Request $request) {
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

    $product = \App\Models\Product::with([
            'category',
            'images',
            'variants' => fn ($query) => $query->orderByDesc('is_default')->orderBy('display_order')->orderBy('id'),
        ])
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
Route::get('/cart/json', [CartController::class, 'cartJson'])->name('cart.json');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{item}', [CartController::class, 'updateQuantity'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/{item}/increment', [CartController::class, 'increment'])->name('cart.increment');
Route::post('/cart/{item}/decrement', [CartController::class, 'decrement'])->name('cart.decrement');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/paymongo/success/{order}', [CheckoutController::class, 'paymongoSuccess'])->name('checkout.paymongo.success');
Route::get('/checkout/paymongo/cancel/{order}', [CheckoutController::class, 'paymongoCancel'])->name('checkout.paymongo.cancel');
Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
Route::post('/orders/lookup', [OrderHistoryController::class, 'lookup'])->name('orders.lookup');
Route::get('/orders/{order}', [OrderHistoryController::class, 'show'])->name('orders.show');
Route::get('/orders/{order}/receipt', [OrderHistoryController::class, 'receipt'])->name('orders.receipt');
Route::get('/orders/{order}/receipt/html', [OrderHistoryController::class, 'receiptHtml'])->name('orders.receipt.html');
Route::get('/orders/{order}/receipt/pdf', [OrderHistoryController::class, 'receiptPdf'])->name('orders.receipt.pdf');
Route::post('/orders/{order}/cancel', [OrderHistoryController::class, 'cancel'])->name('orders.cancel');

Route::get('/customize', [CustomizeController::class, 'index'])->name('customize.index');

Route::get('/assistant', [ChatController::class, 'show'])->name('assistant');
Route::get('/chat/session', [ChatController::class, 'session'])->name('chat.session');
Route::post('/chat/profile', [ChatController::class, 'updateProfile'])->name('chat.profile');
Route::post('/chat/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');
Route::post('/chat/ai-message', [ChatController::class, 'aiMessage'])->name('chat.ai-message');
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

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/')->with('success', 'Your email has been verified!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

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
    ->middleware(['auth', 'verified'])
    ->name('profile');
Route::delete('/profile/picture', [ProfileController::class, 'deletePicture'])
    ->middleware(['auth', 'verified'])
    ->name('profile.picture.delete');

Route::get('/notifications', [UserNotificationController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.index');
Route::post('/notifications/read-all', [UserNotificationController::class, 'readAll'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.read-all');
Route::get('/notifications/{notification}', [UserNotificationController::class, 'open'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.open');
Route::post('/notifications/{notification}/archive', [UserNotificationController::class, 'archive'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.archive');
Route::post('/notifications/{notification}/restore', [UserNotificationController::class, 'restore'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.restore');
Route::delete('/notifications/{notification}', [UserNotificationController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.destroy');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', function (Request $request) {
        if (! $request->filled('section')) {
            $query = $request->query();
            $query['section'] = 'dashboard';

            return redirect()->route('admin.dashboard', $query);
        }

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
            ->with(['items.variant.product', 'user', 'invoice'])
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

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $defaultTrendStart = Carbon::today()->subDays(6);
        $defaultTrendEnd = Carbon::today();

        try {
            $trendStart = $request->filled('trend_start')
                ? Carbon::parse($request->string('trend_start')->value())->startOfDay()
                : $defaultTrendStart->copy()->startOfDay();
        } catch (\Throwable $e) {
            $trendStart = $defaultTrendStart->copy()->startOfDay();
        }

        try {
            $trendEnd = $request->filled('trend_end')
                ? Carbon::parse($request->string('trend_end')->value())->endOfDay()
                : $defaultTrendEnd->copy()->endOfDay();
        } catch (\Throwable $e) {
            $trendEnd = $defaultTrendEnd->copy()->endOfDay();
        }

        if ($trendStart->gt($trendEnd)) {
            [$trendStart, $trendEnd] = [$trendEnd->copy()->startOfDay(), $trendStart->copy()->endOfDay()];
        }

        $todayRevenue = (float) Order::query()
            ->whereDate('created_at', $today)
            ->whereIn('status', ['confirmed', 'ready', 'completed'])
            ->sum('total');

        $monthRevenue = (float) Order::query()
            ->whereBetween('created_at', [$monthStart, Carbon::now()])
            ->whereIn('status', ['confirmed', 'ready', 'completed'])
            ->sum('total');

        $todayOrdersCount = Order::query()
            ->whereDate('created_at', $today)
            ->count();

        $activeCustomersCount = User::query()
            ->whereHas('orders', function ($query) use ($monthStart) {
                $query->whereBetween('created_at', [$monthStart, Carbon::now()]);
            })
            ->count();

        $topProducts = \App\Models\Product::query()
            ->selectRaw('products.id as product_id, products.name, SUM(order_items.quantity) as units_sold, SUM(order_items.subtotal) as sales_total')
            ->join('product_variants as variants', 'variants.product_id', '=', 'products.id')
            ->join('order_items', 'order_items.variant_id', '=', 'variants.id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', ['confirmed', 'ready', 'completed'])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('units_sold')
            ->take(5)
            ->get();

        $topProductsRevenueTotal = (float) \App\Models\OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', ['confirmed', 'ready', 'completed'])
            ->sum('order_items.subtotal');

        $dailyRevenueMap = Order::query()
            ->selectRaw('DATE(created_at) as day, SUM(total) as revenue, COUNT(*) as orders_count')
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->whereIn('status', ['confirmed', 'ready', 'completed'])
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $trendDays = $trendStart->copy()->startOfDay()->diffInDays($trendEnd->copy()->startOfDay()) + 1;
        $revenueTrend = collect(range(0, max(0, $trendDays - 1)))->map(function ($index) use ($trendStart, $dailyRevenueMap) {
            $day = $trendStart->copy()->addDays($index);
            $key = $day->toDateString();
            $row = $dailyRevenueMap->get($key);

            return [
                'date' => $key,
                'label' => $day->format('D'),
                'revenue' => $row ? (float) $row->revenue : 0.0,
                'orders_count' => $row ? (int) $row->orders_count : 0,
            ];
        });

        $maxRevenuePoint = (float) $revenueTrend->max('revenue');

        $dashboardStats = [
            'today_revenue' => $todayRevenue,
            'month_revenue' => $monthRevenue,
            'today_orders_count' => $todayOrdersCount,
            'active_customers_count' => $activeCustomersCount,
            'pending_orders_count' => (int) ($orderCounts['pending'] ?? 0),
            'confirmed_orders_count' => (int) ($orderCounts['confirmed'] ?? 0),
            'ready_orders_count' => (int) ($orderCounts['ready'] ?? 0),
            'completed_orders_count' => (int) ($orderCounts['completed'] ?? 0),
            'cancelled_orders_count' => (int) ($orderCounts['cancelled'] ?? 0),
            'max_revenue_point' => $maxRevenuePoint,
            'top_products_revenue_total' => $topProductsRevenueTotal,
        ];

        $userStatusFilter = $request->string('user_status')->value();
        $userSearchFilter = $request->string('user_search')->trim()->value();

        $usersQuery = User::query()
            ->withTrashed()
            ->where('is_admin', false)
            ->withCount('orders')
            ->latest();

        if ($userStatusFilter !== '' && $userStatusFilter !== 'all') {
            if ($userStatusFilter === 'deleted') {
                $usersQuery->onlyTrashed();
            } else {
                $usersQuery->whereNull('deleted_at');
                $usersQuery->where('is_active', $userStatusFilter === 'active');
            }
        } else {
            $usersQuery->whereNull('deleted_at');
        }

        if ($userSearchFilter !== '') {
            $usersQuery->where(function ($query) use ($userSearchFilter) {
                $query->where('name', 'like', '%' . $userSearchFilter . '%')
                    ->orWhere('email', 'like', '%' . $userSearchFilter . '%')
                    ->orWhere('phone', 'like', '%' . $userSearchFilter . '%');
            });
        }

        $users = $usersQuery->paginate(12, ['*'], 'user_page')->withQueryString();
        $userCounts = [
            'all' => User::where('is_admin', false)->count(),
            'active' => User::where('is_admin', false)->where('is_active', true)->count(),
            'inactive' => User::where('is_admin', false)->where('is_active', false)->count(),
            'deleted' => User::onlyTrashed()->where('is_admin', false)->count(),
        ];
        $userFilters = [
            'status' => $userStatusFilter === '' ? 'all' : $userStatusFilter,
            'search' => $userSearchFilter,
        ];

        $inventorySearchFilter = $request->string('inventory_search')->trim()->value();
        $inventoryStatusFilter = $request->string('inventory_status')->value();
        $lowStockThreshold = 10;

        $inventoryQuery = Variant::query()
            ->with(['product.category'])
            ->where('is_active', true)
            ->orderBy('stock_quantity')
            ->orderBy('id');

        if ($inventorySearchFilter !== '') {
            $inventoryQuery->where(function ($query) use ($inventorySearchFilter) {
                $query->where('name', 'like', '%' . $inventorySearchFilter . '%')
                    ->orWhere('sku', 'like', '%' . $inventorySearchFilter . '%')
                    ->orWhereHas('product', function ($productQuery) use ($inventorySearchFilter) {
                        $productQuery->where('name', 'like', '%' . $inventorySearchFilter . '%');
                    });
            });
        }

        if ($inventoryStatusFilter !== '' && $inventoryStatusFilter !== 'all') {
            if ($inventoryStatusFilter === 'out_of_stock') {
                $inventoryQuery->where('stock_quantity', '<=', 0);
            } elseif ($inventoryStatusFilter === 'low_stock') {
                $inventoryQuery->whereBetween('stock_quantity', [1, $lowStockThreshold]);
            } elseif ($inventoryStatusFilter === 'in_stock') {
                $inventoryQuery->where('stock_quantity', '>', $lowStockThreshold);
            }
        }

        $inventoryItems = $inventoryQuery->paginate(12, ['*'], 'inventory_page')->withQueryString();
        $inventoryCounts = [
            'all' => Variant::where('is_active', true)->count(),
            'out_of_stock' => Variant::where('is_active', true)->where('stock_quantity', '<=', 0)->count(),
            'low_stock' => Variant::where('is_active', true)->whereBetween('stock_quantity', [1, $lowStockThreshold])->count(),
            'in_stock' => Variant::where('is_active', true)->where('stock_quantity', '>', $lowStockThreshold)->count(),
        ];
        $inventoryFilters = [
            'search' => $inventorySearchFilter,
            'status' => $inventoryStatusFilter === '' ? 'all' : $inventoryStatusFilter,
        ];

        $lowStockVariants = Variant::query()
            ->with('product')
            ->where('is_active', true)
            ->whereBetween('stock_quantity', [1, $lowStockThreshold])
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        $outOfStockVariants = Variant::query()
            ->with('product')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 0)
            ->orderBy('name')
            ->take(5)
            ->get();

        $recentInventoryMovements = InventoryMovement::query()
            ->with(['variant.product', 'actor'])
            ->latest()
            ->take(10)
            ->get();

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
            'dashboardStats',
            'topProducts',
            'revenueTrend',
            'users',
            'userCounts',
            'userFilters',
            'inventoryItems',
            'inventoryCounts',
            'inventoryFilters',
            'lowStockVariants',
            'outOfStockVariants',
            'recentInventoryMovements',
            'productFilters'
        ));
    })->name('admin.dashboard');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/customization-pricing', [\App\Http\Controllers\Admin\SettingsController::class, 'updateCustomizationPricing'])->name('admin.customization-pricing.update');
    Route::post('/settings/shop-fees', [\App\Http\Controllers\Admin\SettingsController::class, 'updateShopFees'])->name('admin.shop-fees.update');

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
    Route::get('/products/bulk-upload', [\App\Http\Controllers\Admin\ProductController::class, 'showBulkUploadForm'])->name('admin.products.bulk-upload.form');
    Route::post('/products/bulk-upload', [\App\Http\Controllers\Admin\ProductController::class, 'bulkUpload'])->name('admin.products.bulk-upload');
    Route::get('/products/bulk-upload/template.csv', [\App\Http\Controllers\Admin\ProductController::class, 'downloadBulkTemplateCsv'])->name('admin.products.bulk-upload.template.csv');
    Route::get('/products/bulk-upload/template.xls', [\App\Http\Controllers\Admin\ProductController::class, 'downloadBulkTemplateExcel'])->name('admin.products.bulk-upload.template.xls');
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
    Route::get('/orders-export', [\App\Http\Controllers\Admin\ExportController::class, 'orders'])->name('admin.orders.export');
    Route::get('/reports-export', [\App\Http\Controllers\Admin\ExportController::class, 'reports'])->name('admin.reports.export');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status.update');

    Route::get('/invoices/{invoice}/print', [\App\Http\Controllers\Admin\InvoiceController::class, 'print'])->name('admin.invoices.print');
    Route::post('/invoices/{invoice}/track-print', [\App\Http\Controllers\Admin\InvoiceController::class, 'trackPrint'])->name('admin.invoices.track-print');
    Route::get('/invoices/{invoice}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('admin.invoices.download');

    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->withTrashed()->name('admin.users.show');
    Route::patch('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('admin.users.status.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::patch('/users/{user}/restore', [AdminUserController::class, 'restore'])->withTrashed()->name('admin.users.restore');

    Route::get('/inventory', [AdminInventoryController::class, 'index'])->name('admin.inventory.index');
    Route::patch('/inventory/{variant}/adjust', [AdminInventoryController::class, 'adjust'])->name('admin.inventory.adjust');

    Route::get('/chats', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::get('/chats/data', [AdminChatController::class, 'data'])->name('admin.chat.data');
    Route::post('/chats/{conversation}/messages', [AdminChatController::class, 'storeMessage'])->name('admin.chat.messages.store');
    Route::post('/chats/{conversation}/typing', [AdminChatController::class, 'typing'])->name('admin.chat.typing');
    Route::post('/chat-presence/{conversation?}', [AdminChatController::class, 'presence'])->name('admin.chat.presence');
    Route::post('/chats/auto-replies', [AdminChatController::class, 'upsertAutoReply'])->name('admin.chat.auto-replies.upsert');
    Route::delete('/chats/auto-replies/{autoReply}', [AdminChatController::class, 'destroyAutoReply'])->name('admin.chat.auto-replies.destroy');
});

Route::post('/profile', [ProfileController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('profile.update');

Route::post('/profile/address', [ProfileController::class, 'storeAddress'])
    ->middleware(['auth', 'verified'])
    ->name('profile.address.store');

Route::put('/profile/address/{address}', [ProfileController::class, 'updateAddress'])
    ->middleware(['auth', 'verified'])
    ->name('profile.address.update');

Route::delete('/profile/address/{address}', [ProfileController::class, 'deleteAddress'])
    ->middleware(['auth', 'verified'])
    ->name('profile.address.delete');

Route::post('/profile/payment-method', [ProfileController::class, 'storePaymentMethod'])
    ->middleware(['auth', 'verified'])
    ->name('profile.payment.store');

Route::put('/profile/payment-method/{paymentMethod}', [ProfileController::class, 'updatePaymentMethod'])
    ->middleware(['auth', 'verified'])
    ->name('profile.payment.update');

Route::delete('/profile/payment-method/{paymentMethod}', [ProfileController::class, 'deletePaymentMethod'])
    ->middleware(['auth', 'verified'])
    ->name('profile.payment.delete');

Route::post('/profile/order/{order}/reorder', [ProfileController::class, 'reorder'])
    ->middleware(['auth', 'verified'])
    ->name('profile.order.reorder');

Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
    ->middleware(['auth', 'verified'])
    ->name('profile.password.update');
