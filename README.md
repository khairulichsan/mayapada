Untuk mengaktifkan dan mengonfigurasi **Webhook (Notification Callback) Midtrans** pada aplikasi **Mayapada Procurement System**, Anda perlu melakukan penyelarasan antara pengaturan di **Midtrans Dashboard** dan kode **Laravel** Anda. 

Berikut adalah detail langkah demi langkah konfigurasinya:

---

### 1. Konfigurasi URL Notification di Dashboard Midtrans
Midtrans memerlukan URL publik untuk mengirimkan data transaksi (dalam bentuk HTTP POST) setiap kali status pembayaran berubah (misal dari *pending* menjadi *settlement* atau *expire*).

1. Masuk ke **[Midtrans Dashboard](https://dashboard.midtrans.com/)** (gunakan mode *Sandbox* untuk pengembangan, dan *Production* untuk rilis resmi).
2. Buka menu **Settings** > **Configuration**.
3. Cari kolom **Payment Notification URL** dan isi dengan URL endpoint Laravel Anda. Contoh formatnya:
   * **Production:** `https://nama-domain-anda.com/api/midtrans-callback`
   * **Development (Lokal):** `https://[subdomain-ngrok].ngrok-free.app/api/midtrans-callback`
4. Simpan perubahan.

> 💡 **Penting untuk Tahap Pengembangan (Lokal):**
> Karena Midtrans berjalan di internet dan server lokal Anda berjalan di `http://127.0.0.1:8000`, Midtrans tidak bisa mengirimkan data langsung ke komputer Anda. Anda harus menggunakan tool tunneling seperti **Ngrok** atau **Localtunnel** untuk membuat URL publik sementara:
> ```bash
> ngrok http 8000
> ```
> Gunakan URL HTTPS yang diberikan oleh Ngrok tersebut untuk diisi di Payment Notification URL Midtrans Dashboard.

---

### 2. Konfigurasi Route (Laravel)
Daftarkan rute penampung data dari Midtrans. Karena ini adalah request eksternal, biasanya diletakkan di dalam file `routes/api.php` agar tidak terkena verifikasi session standar web:

```php
// routes/api.php atau routes/web.php
use App\Http\Controllers\MidtransCallbackController;

Route::post('/midtrans-callback', [MidtransCallbackController::class, 'handleNotification']);
```

---

### 3. Pengecualian Proteksi CSRF (Wajib)
Laravel secara default memblokir semua request POST dari luar jika tidak menyertakan token CSRF. Anda harus mengecualikan endpoint callback Midtrans dari proteksi ini.

* **Untuk Laravel 11 (terbaru):**
  Buka file `bootstrap/app.php` dan tambahkan pengecualian pada middleware:
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->validateCsrfTokens(except: [
          '/api/midtrans-callback', // sesuaikan dengan rute Anda
      ]);
  })
  ```
* **Untuk Laravel 9 atau 10:**
  Buka file `app/Http/Middleware/VerifyCsrfToken.php` dan tambahkan ke dalam array `$except`:
  ```php
  protected $except = [
      'api/midtrans-callback',
  ];
  ```

---

### 4. Logika Penanganan Webhook (Controller)
Di dalam controller Anda (misalnya `MidtransCallbackController`), Anda harus melakukan **verifikasi keaslian data** menggunakan **Signature Key** Midtrans untuk mencegah manipulasi data dari pihak luar.

Berikut struktur logika standar untuk memproses notifikasi tersebut:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProcurementRequest; // Contoh model pengadaan
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    public function handleNotification(Request $request)
    {
        // 1. Ambil data mentah payload dari Midtrans
        $payload = $request->all();
        
        $serverKey = config('services.midtrans.server_key'); // Server Key dari .env
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $signatureKey = $payload['signature_key'];

        // 2. Verifikasi keaslian Signature Key
        // Rumus: SHA512(order_id + status_code + gross_amount + server_key)
        $localSignature = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signatureKey !== $localSignature) {
            Log::error('Signature Key Midtrans tidak valid!');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // 3. Ambil data pengadaan dari database Anda
        $procurement = ProcurementRequest::where('uuid_pembayaran', $orderId)->first();

        if (!$procurement) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // 4. Update status pengadaan berdasarkan status transaksi dari Midtrans
        $transactionStatus = $payload['transaction_status'];
        $type = $payload['payment_type'];

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                if ($payload['fraud_status'] == 'challenge') {
                    $procurement->update(['status_pembayaran' => 'pending']);
                } else {
                    $procurement->update(['status_pembayaran' => 'success']);
                }
            }
        } elseif ($transactionStatus == 'settlement') {
            // Transaksi sukses (Transfer Bank, Gopay, QRIS, dll)
            $procurement->update(['status_pembayaran' => 'success']);
        } elseif ($transactionStatus == 'pending') {
            // Menunggu pembayaran
            $procurement->update(['status_pembayaran' => 'pending']);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            // Transaksi gagal atau kedaluwarsa
            $procurement->update(['status_pembayaran' => 'failed']);
        }

        return response()->json(['message' => 'Callback processed successfully'], 200);
    }
}
```

---

### Ringkasan Alur Kerja Webhook:
1. Pembeli melakukan pembayaran pada popup **Snap Midtrans**.
2. Midtrans memproses pembayaran secara *real-time*.
3. Midtrans mengirimkan request **POST** ke URL Notification Anda (lewat Ngrok atau domain publik).
4. Laravel menerima request tersebut, memverifikasi tanda tangannya (**Signature Key**), dan jika cocok, Laravel memperbarui status pengadaan di tabel database menjadi **success** / **pending** / **failed**.

---

Apakah Anda membutuhkan bantuan untuk mengintegrasikan logika penanganan status di atas ke dalam database pengadaan Anda, atau ingin saya menuliskan script migrasi untuk tabel transaksi pembayarannya?
