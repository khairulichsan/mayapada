<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\RestockOrder;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'dashboard');
        $stab = $request->query('stab', 'inventory'); // Tab internal supplier

        // Tarik data mentah relasional dari database MySQL
        $products = Product::with('variants')->get();
        $categories = Category::where('is_active', true)->get();
        $orders = Order::with('items')->orderBy('created_at', 'desc')->get();
        $users = User::all();
        $restockOrders = RestockOrder::orderBy('created_at', 'desc')->get();

        // Hitung statistik dashboard utama untuk Admin
        $paidOrders = Order::where('payment_status', 'paid')->get();
        $grossRevenue = $paidOrders->reduce(function ($sum, $order) {
            return $sum + ($order->total_price - $order->shipping_cost);
        }, 0);

        $cogs = $grossRevenue * 0.75;
        $netProfit = $grossRevenue * 0.25;
        $pendingPayments = Order::where('payment_status', 'verifying')->count();
        $pendingShipments = Order::where('shipping_status', 'processing')->count();
        $lowStockItems = Product::where('stock', '<', 10)->where('is_published', true)->count();

        // Filter rekapitulasi laporan laba rugi bulanan
        $reportMonth = $request->query('month', '06');
        $reportYear = $request->query('year', '2026');
        $reportTransactions = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', $reportMonth)
            ->whereYear('created_at', $reportYear)
            ->get();

        $reportSums = ['gross' => 0, 'cost' => 0, 'profit' => 0];
        foreach ($reportTransactions as $trx) {
            $itemVal = $trx->total_price - $trx->shipping_cost;
            $reportSums['gross'] += $itemVal;
            $reportSums['cost'] += $itemVal * 0.75;
            $reportSums['profit'] += $itemVal * 0.25;
        }

        return view('dashboard', compact(
            'activeTab', 'stab', 'products', 'categories', 'orders', 'users', 'restockOrders',
            'grossRevenue', 'cogs', 'netProfit', 'pendingPayments', 'pendingShipments', 'lowStockItems',
            'reportMonth', 'reportYear', 'reportTransactions', 'reportSums'
        ));
    }

    /**
     * PROSEDUR SUPPLIER: Kirim spek baju & Harga Grosir (Lusin/Bal) + Upload Gambar Asli
     */
    public function saveProduct(Request $request)
    {
        $request->validate([
            'name'            => 'required|string',
            'sku'             => 'required|string|unique:products,sku,' . $request->id,
            'category_id'     => 'required|exists:categories,id',
            'wholesale_price' => 'required|numeric|min:1000',
            'wholesale_unit'  => 'required|string|in:lusin,kodi,bal',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi file gambar asli
        ]);

        $user = Auth::user();
        $imagePath = $request->input('existing_image_path', 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=500&q=80');

        // Proses penyimpanan file gambar ke storage lokal jika diupload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $filename, 'public');
            $imagePath = asset('storage/products/' . $filename); // Menghasilkan link lokal yang valid
        }

        // Menyimpan ke MySQL, otomatis dikunci is_published = false (Draft) sebelum divalidasi Admin
        Product::updateOrCreate(
            ['id' => $request->id],
            [
                'supplier_id'     => $user->id,
                'supplier_brand'  => $user->brand_name ?? 'Supplier Lokal',
                'sku'             => $request->sku,
                'name'            => $request->name,
                'slug'            => Str::slug($request->name) . '-' . rand(100, 999),
                'category_id'     => $request->category_id,
                'wholesale_price' => $request->wholesale_price,
                'wholesale_unit'  => $request->wholesale_unit,
                'description'     => $request->description,
                'image_path'      => $imagePath,
                'is_published'    => false
            ]
        );

        return redirect()->to('/dashboard?stab=inventory')->with('success', 'Draft produk berhasil dikirim ke antrean kurasi Admin Toko Mayapada.');
    }

    /**
     * PROSEDUR ADMIN: Mengkurasi barang, pasang harga eceran, tambah varian size/warna, dan publish ke web
     */
    public function curateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $gallery = $product->gallery_images ?? [];

        // Proses Foto Utama (Opsional)
        $imagePath = $product->image_path;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_main_' . \Illuminate\Support\Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $filename, 'public');
            $imagePath = asset('storage/products/' . $filename);
        }

        // Proses Multi-Upload Galeri Tambahan
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $gFile) {
                $gFilename = time() . '_' . uniqid() . '.' . $gFile->getClientOriginalExtension();
                $gFile->storeAs('products', $gFilename, 'public');
                $gallery[] = asset('storage/products/' . $gFilename);
            }
        }

        // Update Spek Produk
        $product->update([
            'name'           => $request->name,
            'category_id'    => $request->category_id,
            'price'          => $request->retail_price,
            'description'    => $request->description,
            'image_path'     => $imagePath,
            'gallery_images' => $gallery,
            'is_published'   => $request->is_published,
        ]);

        // RE-GENERATE VARIAN BERDASARKAN STOK SPESIFIK PER UKURAN
        \App\Models\ProductVariant::where('product_id', $product->id)->delete();

        $colorsArray = $request->colors ? explode(',', $request->colors) : ['Standar'];

        if ($request->sizes) {
            foreach ($request->sizes as $size) {
                // Ambil nilai stok spesifik dari input (misal: name="stock_m")
                $specificStock = $request->input('stock_' . str_replace(' ', '_', $size), 0);

                // Bagi stok secara merata ke dalam variasi warna yang ada
                $stockPerColor = max(0, floor($specificStock / count($colorsArray)));

                foreach ($colorsArray as $color) {
                    \App\Models\ProductVariant::create([
                        'product_id' => $product->id,
                        'name'       => strtoupper(trim($size)) . ' - ' . ucwords(trim($color)),
                        'sku'        => $product->sku . '-' . strtoupper(trim($size)) . '-' . strtoupper(substr(trim($color), 0, 2)),
                        'price'      => $product->price,
                        'stock'      => $stockPerColor,
                    ]);
                }
            }
        }

        return redirect()->to('/dashboard?tab=curation')->with('success', 'Spek, Galeri, dan Stok Varian berhasil dikurasi!');
    }

    // Fungsi Hapus Foto Spesifik dari Galeri
    public function deleteGalleryImage(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $imageUrl = $request->input('image_url');

        $gallery = $product->gallery_images ?? [];
        // Hapus URL yang cocok dari array
        $gallery = array_filter($gallery, function($url) use ($imageUrl) {
            return $url !== $imageUrl;
        });

        $product->update(['gallery_images' => array_values($gallery)]);
        return redirect()->back()->with('success', 'Foto spesifik berhasil dihapus dari galeri.');
    }

    // Fungsi CRUD Kategori: Tambah
    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:categories']);
        Category::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'is_active' => true
        ]);
        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    // Fungsi CRUD Kategori: Hapus
    public function destroyCategory($id)
    {
        Category::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus permanen.');
    }

    public function deleteProduct($id)
    {
        $product = Product::where('id', $id)->where('supplier_id', Auth::id())->firstOrFail();
        $product->delete();
        return redirect()->to('/dashboard?stab=inventory')->with('success', 'Produk sukses dihapus dari katalog.');
    }

    public function verifyPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        if ($request->input('status') === 'paid') {
            $order->update(['payment_status' => 'paid', 'shipping_status' => 'processing']);
        } else {
            $order->update(['payment_status' => 'rejected']);
        }
        return redirect()->back()->with('success', 'Status pembayaran pesanan berhasil diperbarui.');
    }

    public function shipOrder(Request $request, $id)
    {
        $request->validate(['tracking_number' => 'required|string']);
        $order = Order::findOrFail($id);
        $order->update(['shipping_status' => 'shipped', 'tracking_number' => $request->input('tracking_number')]);
        return redirect()->back()->with('success', 'Nomor resi kurir sukses didaftarkan.');
    }

    public function createRestock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|numeric|min:5',
            'price'      => 'required|numeric',
        ]);

        $product = Product::findOrFail($request->product_id);
        $rstId = 'RST-' . date('Ymd') . '-' . rand(100, 999);

        RestockOrder::create([
            'id'             => $rstId,
            'supplier_id'    => $product->supplier_id,
            'supplier_brand' => $product->supplier_brand,
            'product_id'     => $product->id,
            'sku'            => $product->sku,
            'qty'            => $request->qty,
            'price'          => $request->price,
            'status'         => 'pending'
        ]);

        return redirect()->back()->with('success', 'Nota PO pengadaan berhasil dikirim ke dashboard mitra supplier.');
    }

    public function resolveRestock(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $restock = RestockOrder::where('id', $id)->where('supplier_id', Auth::id())->firstOrFail();
        $status = $request->input('status');
        $restock->update(['status' => $status]);

        if ($status === 'approved') {
            $product = Product::find($restock->product_id);
            if ($product) {
                $product->increment('stock', $restock->qty);
                $firstVariant = $product->variants()->first();
                if ($firstVariant) {
                    $firstVariant->increment('stock', $restock->qty);
                }
            }
        }
        return redirect()->to('/dashboard?stab=restock')->with('success', 'Nota PO pengadaan stok berhasil ditindaklanjuti.');
    }
}
