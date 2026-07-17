<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use Illuminate\Http\Request;

class AdminProcurementController extends Controller
{
    /**
     * Menyimpan data pengajuan (Request) baru dari Admin ke Supplier
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari modal Admin
        $validated = $request->validate([
            'request_title'      => 'required|string|max:255',
            'color'              => 'required|string|max:255',
            'size'               => 'required|string|max:50',
            'qty_requested'      => 'required|integer|min:1',
            'target_supplier_id' => 'nullable|exists:users,id' // Nullable karena bisa broadcast ke semua
        ]);

        // 2. Simpan ke database (status otomatis 'pending' sesuai bawaan migration)
        ProcurementRequest::create($validated);

        // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Permintaan pasokan pakaian kustom berhasil dikirim ke Supplier!');
    }

    /**
     * Memproses keputusan Admin atas harga yang ditawarkan Supplier (Setuju/Tolak)
     */
    public function decision(Request $request, $id)
    {
        // 1. Cari data request berdasarkan ID
        $procurement = ProcurementRequest::findOrFail($id);

        // 2. Pastikan input yang diterima hanya 'approve' atau 'reject'
        $request->validate([
            'decision' => 'required|in:approve,reject'
        ]);

        // 3. Eksekusi perubahan status
        if ($request->decision === 'approve') {
            $procurement->update(['status' => 'po_created']);

            // Catatan: Jika ke depannya Anda ingin otomatis membuat nota masuk ke tabel `restock_orders`,
            // Anda bisa menyelipkan kodingan insert ke RestockOrder di baris ini.

            $message = 'Penawaran disetujui! Dokumen PO berhasil diterbitkan untuk supplier.';
        } else {
            // Mengembalikan status ke pending agar supplier lain bisa bid,
            // atau mengubahnya menjadi rejected jika memang dibatalkan sepenuhnya.
            $procurement->update(['status' => 'rejected']);
            $message = 'Penawaran dari Supplier telah ditolak.';
        }

        return back()->with('success', $message);
    }
}
