<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupplierProcurementController extends Controller
{
    /**
     * Memproses ketika Supplier klik "Sanggup & Beri Harga"
     */
    public function bid(Request $request, $id)
    {
        $procurement = ProcurementRequest::findOrFail($id);

        // 1. Validasi super ketat untuk harga, tanggal, dan detail produk baru
        $validated = $request->validate([
            'offered_price'      => 'required|numeric|min:1000',
            'estimated_delivery' => 'required|date|after_or_equal:today',
            'name'               => 'required|string',
            'sku'                => 'required|string|unique:products,sku',
            'category_id'        => 'required|exists:categories,id',
            'wholesale_unit'     => 'required|string|in:pcs,lusin,kodi,bal',
            'stock'              => 'required|integer|min:1',
            'description'        => 'nullable|string',
            'image'              => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();

        // 2. Upload Gambar Fisik Baju ke Folder Lokal
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_custom_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('products', $filename, 'public');
            $imagePath = asset('storage/products/' . $filename);
        }

        // 3. JADIKAN DRAF PRODUK! Masukkan ke tabel Products agar tampil di tab Kurasi Admin
        Product::create([
            'supplier_id'     => $user->id,
            'supplier_brand'  => $user->brand_name ?? 'Supplier Lokal',
            'sku'             => $request->sku,
            'name'            => $request->name,
            'slug'            => Str::slug($request->name) . '-' . rand(100, 999),
            'category_id'     => $request->category_id,
            'wholesale_price' => $request->offered_price,
            'wholesale_unit'  => $request->wholesale_unit,
            'price'           => 0, // <--- PERBAIKAN: Beri nilai awal 0 agar tidak ditolak MySQL
            'stock'           => $request->stock,
            'description'     => $request->description,
            'image_path'      => $imagePath,
            'is_published'    => false
        ]);

        // 4. Update status RFQ Kustom menjadi sudah diberi harga
        $procurement->update([
            'responded_by_id'    => $user->id,
            'offered_price'      => $request->offered_price,
            'estimated_delivery' => $request->estimated_delivery,
            'status'             => 'supplier_bid',
        ]);

        return back()->with('success', 'Berhasil! Harga penawaran dan draf produk telah dikirim ke Admin untuk diperiksa.');
    }

    /**
     * Memproses ketika Supplier klik "Tolak Permintaan"
     */
    public function reject(Request $request, $id)
    {
        $procurement = ProcurementRequest::findOrFail($id);

        $procurement->update([
            'responded_by_id' => Auth::id(),
            'status'          => 'rejected',
        ]);

        return back()->with('success', 'Permintaan pasokan dari Admin berhasil Anda tolak.');
    }
}
