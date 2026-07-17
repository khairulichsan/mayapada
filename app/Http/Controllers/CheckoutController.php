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
// 7. INTEGRASI XENDIT PAYMENT GATEWAY
// ==========================================
$secretKey = env('XENDIT_SECRET_KEY');

// Siapkan parameter invoice Xendit
$params = [
    'external_id' => (string) $order->id, // Xendit butuh format string
    'amount' => (int) $order->total_price,
    'description' => 'Pembayaran Pesanan ' . $order->id . ' di Mayapada',
    'customer' => [
        'given_names' => $request->customer_name,
        'mobile_number' => $request->customer_phone,
    ],
    // Arahkan kembali ke halaman pesanan setelah bayar
    'success_redirect_url' => url('/dashboard?ctab=orders'),
    'failure_redirect_url' => url('/dashboard?ctab=orders'),
];

// Panggil API Xendit menggunakan HTTP Client bawaan Laravel
$response = Http::withBasicAuth($secretKey, '')
    ->post('https://api.xendit.co/v2/invoices', $params);

if ($response->successful()) {
    // Ambil Link Halaman Pembayaran dari Xendit
    $invoiceUrl = $response->json('invoice_url');

    // Simpan link tersebut ke database (meminjam kolom yang sudah ada)
    $order->midtrans_snap_token = $invoiceUrl;
    $order->save();
} else {
    // Jika API Key salah atau Xendit gangguan
    return redirect()->back()->with('error', 'Gagal terhubung ke Xendit: ' . $response->body());
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
