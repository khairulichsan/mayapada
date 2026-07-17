<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    /**
     * Endpoint Webhook / Callback untuk menerima laporan lunas otomatis dari Xendit
     */
    public function callback(Request $request)
    {
        $xenditToken = env('XENDIT_CALLBACK_TOKEN');

        // 1. Validasi Keamanan: Pastikan yang mengirim data benar-benar server Xendit
        if ($request->header('x-callback-token') !== $xenditToken) {
            return response()->json(['status' => 'error', 'message' => 'Token Verifikasi Tidak Valid'], 403);
        }

        // 2. Cek apakah status tagihan dari Xendit adalah LUNAS (PAID)
        if ($request->status === 'PAID') {

            // 3. Cari order berdasarkan external_id (Nomor Nota yang kita kirim ke Xendit)
            $order = Order::find($request->external_id);

            if ($order) {
                // 4. Update status di database Anda menjadi LUNAS dan SIAP DIKEMAS
                $order->update([
                    'payment_status' => 'paid',
                    'shipping_status' => 'processing'
                ]);
            }
        }

        // Beri respon 200 OK ke Xendit agar mereka tidak mengirim notifikasi berulang
        return response()->json(['status' => 'success', 'message' => 'Webhook Xendit berhasil diproses']);
    }
}
