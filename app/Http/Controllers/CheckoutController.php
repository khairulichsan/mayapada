<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;


class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi formulir pengiriman
        $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_courier' => 'required|string',
        ]);

        // 2. Tarik data keranjang yang aman dari Session server
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda kosong.');
        }

        // 3. Kalkulasi Ulang Tagihan (Memisahkan Subtotal & Grand Total)
        $subtotal = 0;
        $shippingCost = 15000; // Tarif pengiriman konstan untuk tahap simulasi

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }
        $totalPrice = $subtotal + $shippingCost;

        // 4. Buat Nomor Nota Transaksi (Order ID)
        $orderId = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

        // 5. Simpan Induk Pesanan ke Database
        $order = Order::create([
            'id'               => $orderId,
            'customer_id'      => Auth::id(),
            'customer_name'    => $request->customer_name,
            'customer_phone'   => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_courier' => $request->shipping_courier,
            'subtotal'         => $subtotal,
            'shipping_cost'    => $shippingCost,
            'total_price'      => $totalPrice,
            'payment_status'   => 'verifying',
            'shipping_status'  => 'pending',
        ]);

        // 6. Simpan Detail Item yang Dibeli & Kurangi Stoknya
        foreach ($cart as $item) {

            // Ambil data produk dan varian DULU untuk mendapatkan kode SKU aslinya
            $product = Product::find($item['product_id']);
            $variant = null;
            $sku = $product ? $product->sku : 'UNKNOWN';

            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::find($item['variant_id']);
                if ($variant) {
                    $sku = $variant->sku; // Gunakan SKU varian spesifik (misal: KU-DAS-01-S-ME)
                }
            }

            // Simpan detail item berserta SKU-nya
            OrderItem::create([
                'order_id'           => $order->id,
                'product_id'         => $item['product_id'],
                'product_variant_id' => $item['variant_id'],
                'name'               => $item['name'],
                'variant_name'       => $item['variant_name'],
                'sku'                => $sku, // <-- Ini solusi dari eror MySQL tersebut!
                'qty'                => $item['qty'],
                'price'              => $item['price'],
                'subtotal'           => $item['price'] * $item['qty'],
            ]);

            // Potong stok produk utama
            if ($product) {
                $product->decrement('stock', $item['qty']);
            }

            // Potong stok varian spesifik
            if ($variant) {
                $variant->decrement('stock', $item['qty']);
            }
        }

        // 7. Bersihkan keranjang belanja setelah sukses
        session()->forget('cart');

        // 8. Lemparkan konsumen ke tab Status Pesanan
        return redirect('/dashboard?ctab=orders')->with('success', 'Nota pesanan berhasil dicetak! Silakan tunggu validasi pembayaran dari Kasir.');
    }

    public function completeOrder($id)
    {
        $order = Order::where('id', $id)->where('customer_id', auth()->id())->firstOrFail();
        $order->update(['shipping_status' => 'delivered']);

        return redirect()->back()->with('success', 'Terima kasih! Pesanan telah dikonfirmasi selesai dan diterima dengan baik.');
    }
}
