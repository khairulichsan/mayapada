<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Models\Product;
use App\Models\Category;

// 1. HALAMAN UTAMA PUBLIK (Bisa diakses siapa saja tanpa login)
Route::get('/', function () {
    // Tarik data pakaian untuk kebutuhan tampilan katalog depan
    $products = Product::with('variants')->get();
    $categories = Category::where('is_active', true)->get();
    $orders = collect(); // Kosong karena tamu belum punya riwayat order

    return view('welcome', compact('products', 'categories', 'orders'));
})->name('home');

// 2. RUTE OPERASIONAL KERANJANG BELANJA (Diubah menjadi Publik agar Tamu bisa pakai)
Route::post('/cart/add', function (Illuminate\Http\Request $request) {
    $product = Product::findOrFail($request->product_id);
    $variant = $request->variant_id ? \App\Models\ProductVariant::find($request->variant_id) : null;

    $cartId = $product->id . ($variant ? '_' . $variant->id : '');
    $cart = session()->get('cart', []);

    $cart[$cartId] = [
        'product_id'   => $product->id,
        'variant_id'   => $variant ? $variant->id : null,
        'name'         => $product->name,
        'variant_name' => $variant ? $variant->name : null,
        'price'        => $variant ? $variant->price : $product->price,
        'qty'          => ($cart[$cartId]['qty'] ?? 0) + $request->qty,
    ];

    session()->put('cart', $cart);

    // Jika sudah login arahkan ke dashboard, jika tamu arahkan ke welcome page
    $targetUrl = Auth::check() ? '/dashboard?ctab=cart' : '/?ctab=cart';
    return redirect()->to($targetUrl)->with('success', 'Pakaian berhasil masuk keranjang!');
})->name('cart.add');

Route::get('/cart/remove/{id}', function ($id) {
    $cart = session()->get('cart', []);
    if (isset($cart[$id])) {
        unset($cart[$id]);
        session()->put('cart', $cart);
    }
    return redirect()->back()->with('success', 'Item berhasil dihapus dari keranjang.');
})->name('cart.remove');


// 3. SEMUA RUTE DI BAWAH INI WAJIB LOGIN (PROTEKSI STRICT AUTH)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/orders/create', [DashboardController::class, 'createOrder'])->name('orders.create');
    // Proses kirim invoice checkout hanya bisa ditembak jika sudah login sah
    Route::post('/checkout/cost', [CheckoutController::class, 'checkOngkir'])->name('checkout.cost');
    Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');

    // Rute Khusus Admin
    Route::post('/admin/orders/{id}/verify', [DashboardController::class, 'verifyPayment'])->name('admin.orders.verify');
    Route::post('/admin/orders/{id}/ship', [DashboardController::class, 'shipOrder'])->name('admin.orders.ship');
    Route::post('/admin/restock/store', [DashboardController::class, 'createRestock'])->name('admin.restock.store');
    Route::post('/admin/products/{id}/curate', [DashboardController::class, 'curateProduct'])->name('admin.products.curate');

    // Manajemen Kategori
    Route::post('/admin/categories', [DashboardController::class, 'storeCategory'])->name('admin.categories.store');
    Route::delete('/admin/categories/{id}', [DashboardController::class, 'destroyCategory'])->name('admin.categories.destroy');

    // Hapus Foto Spesifik di Galeri
    Route::post('/admin/products/{id}/delete-image', [DashboardController::class, 'deleteGalleryImage'])->name('admin.products.delete_image');

    Route::post('/supplier/products/save', [DashboardController::class, 'saveProduct'])->name('supplier.products.save');
    Route::delete('/supplier/products/{id}', [DashboardController::class, 'deleteProduct'])->name('supplier.products.destroy');
    Route::post('/supplier/restock/{id}/resolve', [DashboardController::class, 'resolveRestock'])->name('supplier.restock.resolve');

    Route::post('/customer/orders/{id}/complete', [App\Http\Controllers\CheckoutController::class, 'completeOrder'])->name('customer.orders.complete');

});

require __DIR__.'/auth.php';
