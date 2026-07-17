<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

// Impor Library Midtrans
use Midtrans\Config;
use Midtrans\Snap;

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

        // 5. Simpan Induk Pesanan ke Database (Status diubah jadi 'pending' bukan 'verifying')
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
            'payment_status'   => 'pending', // Diubah agar tombol bayar di web muncul
            'shipping_status'  => 'pending',
        ]);

        // 6. Simpan Detail Item yang Dibeli & Kurangi Stoknya
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            $variant = null;
            $sku = $product ? $product->sku : 'UNKNOWN';

            if (!empty($item['variant_id'])) {
                $variant = ProductVariant::find($item['variant_id']);
                if ($variant) {
                    $sku = $variant->sku;
                }
            }

            OrderItem::create([
                'order_id'           => $order->id,
                'product_id'         => $item['product_id'],
                'product_variant_id' => $item['variant_id'],
                'name'               => $item['name'],
                'variant_name'       => $item['variant_name'],
                'sku'                => $sku,
                'qty'                => $item['qty'],
                'price'              => $item['price'],
                'subtotal'           => $item['price'] * $item['qty'],
            ]);

            if ($product) {
                $product->decrement('stock', $item['qty']);
            }
            if ($variant) {
                $variant->decrement('stock', $item['qty']);
            }
        }

        // ==========================================
        // 7. INTEGRASI MIDTRANS PAYMENT GATEWAY
        // ==========================================

        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Siapkan detail transaksi untuk dikirim ke API Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $order->id,
                'gross_amount' => $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'phone' => $request->customer_phone,
            ],
        ];

        try {
            // Minta Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // Simpan token ke dalam database order
            $order->midtrans_snap_token = $snapToken;
            $order->save();

        } catch (\Exception $e) {
            // Jika API Midtrans gagal dijangkau (misal salah API Key), kembalikan error
            return redirect()->back()->with('error', 'Gagal memanggil layanan pembayaran: ' . $e->getMessage());
        }
        // ==========================================

        // 8. Bersihkan keranjang belanja setelah sukses
        session()->forget('cart');

        // 9. Lemparkan konsumen ke tab Status Pesanan
        return redirect('/dashboard?ctab=orders')->with('success', 'Nota pesanan berhasil dicetak! Silakan klik Bayar Sekarang.');
    }

    public function completeOrder($id)
    {
        $order = Order::where('id', $id)->where('customer_id', auth()->id())->firstOrFail();
        $order->update(['shipping_status' => 'delivered']);

        return redirect()->back()->with('success', 'Terima kasih! Pesanan telah dikonfirmasi selesai dan diterima dengan baik.');
    }
}
