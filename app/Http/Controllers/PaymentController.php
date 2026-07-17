<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    /**
     * Endpoint Webhook / Callback untuk menerima laporan lunas otomatis dari Midtrans
     */
    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');

        // 1. Membuat kunci kecocokan (Signature Key) untuk keamanan sistem
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // 2. Validasi keabsahan data: Pastikan sinyal ini murni dari Midtrans, bukan hacker
        if ($hashed === $request->signature_key) {

            // 3. Cek apakah transaksinya sukses (settlement / capture)
            if ($request->transaction_status === 'settlement' || $request->transaction_status === 'capture') {

                // 4. Cari data order berdasarkan ID yang dikirim Midtrans
                $order = Order::find($request->order_id);

                if ($order) {
                    // 5. Update database Anda menjadi LUNAS dan SIAP DIKEMAS
                    $order->update([
                        'payment_status' => 'paid',
                        'shipping_status' => 'processing'
                    ]);
                }
            }
        }

        // Beri respon balik ke Midtrans bahwa laporan sudah diterima dengan baik
        return response()->json(['status' => 'success', 'message' => 'Callback processed']);
    }
}
