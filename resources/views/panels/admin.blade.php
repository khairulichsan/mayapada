<div id="admin-panel-container" class="pt-4 pb-12">
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 mb-6 border-b border-slate-200">
        <div>
            <span class="text-indigo-600 font-black text-[10px] tracking-widest uppercase block">OTORITAS TOKO UTAMA (SUPER ADMIN)</span>
            <h1 id="admin-portal-title" class="text-3xl font-black text-slate-950 tracking-tighter mt-1 uppercase">Mayapada Control Panel</h1>
            <p class="text-slate-500 text-sm font-semibold">Dashboard pusat manajemen e-commerce C2C, validasi mutasi harian, dan monitoring audit inventaris.</p>
        </div>

        <div id="admin-portal-tabs" class="flex flex-wrap items-center gap-1.5 mt-4 md:mt-0 bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
            <a href="?tab=dashboard" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all {{ $activeTab === 'dashboard' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Dashboard
            </a>

            <a href="?tab=curation" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all {{ $activeTab === 'curation' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Kurasi Toko
            </a>

            <a href="?tab=payments" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all relative {{ $activeTab === 'payments' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Saring Bayar
                @if($pendingPayments > 0)
                    <span class="absolute -top-1 -right-1 bg-rose-600 border border-white text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center">{{ $pendingPayments }}</span>
                @endif
            </a>

            <a href="?tab=shipping" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all relative {{ $activeTab === 'shipping' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Kemas &amp; Kirim
                @if($pendingShipments > 0)
                    <span class="absolute -top-1 -right-1 bg-indigo-600 border border-white text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center">{{ $pendingShipments }}</span>
                @endif
            </a>

            <a href="?tab=stock" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all {{ $activeTab === 'stock' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Stok &amp; Restock
            </a>

            <a href="?tab=reports" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all {{ $activeTab === 'reports' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Laba Rugi
            </a>

            <a href="?tab=users" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all {{ $activeTab === 'users' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                Aktor
            </a>
            <a href="?tab=categories" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all {{ $activeTab === 'categories' ? 'bg-white text-slate-950 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">Manajemen Kategori</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold shadow-xs">
            🎉 {{ session('success') }}
        </div>
    @endif

    @if($activeTab === 'dashboard')
        <div class="space-y-8">
            <div id="admin-main-counters" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white border border-slate-200 p-6 rounded-3xl shadow-xs">
                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest block">Omset Penjualan Kotor</span>
                    <div class="text-2xl font-black text-slate-950 mt-1 font-mono tracking-tight">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-emerald-700 font-bold mt-1.5">📈 Berjalan lancar di sistem MySQL</div>
                </div>

                <div class="bg-white border border-slate-200 p-6 rounded-3xl shadow-xs">
                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest block">Estimasi Keuntungan Bersih (25%)</span>
                    <div class="text-2xl font-black text-indigo-700 mt-1 font-mono tracking-tight">Rp {{ number_format($netProfit, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-slate-500 font-bold mt-1.5">Beban Konsinyasi Supplier: <strong class="text-slate-600 font-extrabold">75%</strong></div>
                </div>

                <div class="bg-white border border-slate-200 p-6 rounded-3xl shadow-xs">
                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest block">Validasi Bayar Pending</span>
                    <div class="text-2xl font-black text-rose-600 mt-1 font-mono tracking-tight">{{ $pendingPayments }} Slip</div>
                    @if($pendingPayments > 0)
                        <a href="?tab=payments" class="text-[11px] text-rose-700 font-black mt-1.5 block uppercase tracking-wider hover:underline">Minta Verifikasi →</a>
                    @else
                        <div class="text-[11px] text-emerald-700 font-bold mt-1.5">✓ Seluruh dana klir</div>
                    @endif
                </div>

                <div class="bg-white border border-slate-200 p-6 rounded-3xl shadow-xs">
                    <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest block">Kritis Stok Gudang</span>
                    <div class="text-2xl font-black text-amber-600 mt-1 font-mono tracking-tight">{{ $lowStockItems }} Produk</div>
                    <div class="text-[11px] text-slate-500 font-bold mt-1.5">Segera ajukan PO pengadaan.</div>
                </div>
            </div>

            @if($lowStockItems > 0)
                <div class="bg-indigo-50 border border-indigo-200 rounded-3xl p-5 flex items-center justify-between gap-4">
                    <div class="text-indigo-900 text-xs font-bold">
                        <strong class="font-extrabold uppercase text-[11px] block text-indigo-700">⚠️ Pemberitahuan Sistem:</strong>
                        Terdapat {{ $lowStockItems }} varian pakaian daster/batik dengan stok kritis di bawah 10 pcs. Gunakan fitur pengadaan stok cepat.
                    </div>
                    <a href="?tab=stock" class="bg-indigo-600 hover:bg-slate-900 text-white font-black text-[11px] px-4 py-2.5 rounded-xl transition-all uppercase tracking-wider shrink-0">
                        Proses PO Restock
                    </a>
                </div>
            @endif
        </div>

    @elseif($activeTab === 'payments')
        <div class="space-y-4">
            <div>
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Validasi Bukti Transfer Pembeli</h2>
                <p class="text-gray-400 text-xs mt-0.5">Periksa keabsahan slip transfer setoran bank sebelum memproses pengiriman produk.</p>
            </div>

            @php $verifyingOrders = $orders->where('payment_status', 'verifying'); @endphp

            @if($verifyingOrders->isEmpty())
                <div class="bg-slate-50 border border-slate-200 rounded-3xl py-16 text-center px-4">
                    <div class="text-emerald-600 text-3xl mb-2">✓</div>
                    <p class="text-slate-900 text-sm font-black uppercase tracking-tight">Tidak ada bukti pembayaran yang masuk atau belum diproses</p>
                    <p class="text-slate-400 text-xs mt-1 font-semibold">Seluruh setoran transaksi pelanggan Mayapada telah tervalidasi tuntas.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($verifyingOrders as $order)
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 grid grid-cols-1 lg:grid-cols-3 gap-6 shadow-xs">
                            <div class="space-y-3">
                                <span class="font-mono text-xs font-black text-slate-950 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">{{ $order->id }}</span>
                                <h4 class="font-black text-slate-950 text-sm mt-3 uppercase">Pelanggan: {{ $order->customer_name }}</h4>
                                <p class="text-slate-500 text-xs font-bold font-mono">{{ $order->customer_phone }}</p>
                                <div class="bg-slate-50 p-4 rounded-xl text-xs border border-slate-150">
                                    <span class="font-black text-slate-900 block uppercase tracking-wide text-[10px]">Alamat Pengiriman:</span>
                                    <p class="italic leading-relaxed font-semibold text-slate-600">{{ $order->shipping_address }}</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Detail Belanja</span>
                                <div class="space-y-1.5 text-xs">
                                    @foreach($order->items as $item)
                                        <div class="flex justify-between border-b border-slate-100 py-1">
                                            <span class="font-semibold text-slate-800">{{ $item->name }} {{ $item->variant_name ? "({$item->variant_name})" : "" }} × {{ $item->qty }}</span>
                                            <span class="font-mono font-black text-slate-950">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-sm font-black text-slate-950 flex justify-between uppercase border-t border-dashed border-slate-200 pt-2">
                                    <span>Total Setoran Ditagih</span>
                                    <span class="font-mono text-indigo-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col justify-between">
                                <div class="text-center py-6 text-slate-500 font-semibold text-xs bg-indigo-50 border border-indigo-100 rounded-xl">
                                    💰 Cek Mutasi Bank Rekening Mandiri <br> Total Tagihan Lunas
                                </div>
                                <div class="flex gap-2 mt-4">
                                    <form action="{{ route('admin.orders.verify', $order->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="status" value="paid">
                                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3 rounded-xl text-xs uppercase tracking-wider cursor-pointer">
                                            Terima Bayar
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.orders.verify', $order->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-black px-4 py-3 rounded-xl text-xs uppercase tracking-wider cursor-pointer border border-rose-200">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    @elseif($activeTab === 'shipping')
        <div class="space-y-4">
            <div>
                <h2 class="text-base font-black text-slate-950 uppercase tracking-tight">Kemas &amp; Kirim Pesanan</h2>
                <p class="text-slate-400 text-xs mt-0.5 font-semibold">Setelah pesanan disetujui bayar, siapkan bungkus pakaian daster/batik dan pasang resi pelayanan ekspedisi.</p>
            </div>

            @php $processingOrders = $orders->where('shipping_status', 'processing'); @endphp

            @if($processingOrders->isEmpty())
                <div class="bg-slate-50 border border-slate-200 rounded-3xl py-16 text-center px-4">
                    <p class="text-gray-500 text-sm font-semibold">Tidak ada pesanan divalidasi yang siap kirim.</p>
                    <p class="text-gray-400 text-xs mt-1">Status kargo operasional toko Mayapada aman terkontrol.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($processingOrders as $order)
                        <div class="bg-white border border-slate-200 rounded-3xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xs">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-[11px] font-black text-slate-950 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">{{ $order->id }}</span>
                                    <span class="bg-emerald-50 text-emerald-800 text-[9px] font-black px-2 py-0.5 rounded border border-emerald-200 uppercase">Sudah Dibayar</span>
                                </div>
                                <h4 class="font-black text-slate-950 text-sm mt-1 uppercase">Kirim Ke: {{ $order->customer_name }} ({{ $order->shipping_courier }})</h4>
                                <p class="text-slate-500 text-xs font-semibold italic">{{ $order->shipping_address }}</p>
                            </div>

                            <form action="{{ route('admin.orders.ship', $order->id) }}" method="POST" class="flex gap-2 items-center">
                                @csrf
                                <input type="text" name="tracking_number" required placeholder="Nomor Resi Kirim" class="bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs focus:ring-1 focus:ring-indigo-600 focus:outline-none uppercase font-mono font-bold">
                                <button type="submit" class="bg-indigo-600 hover:bg-slate-900 text-white font-black text-xs px-5 py-3 rounded-xl transition-all uppercase tracking-wider cursor-pointer shadow-xs">
                                    Daftarkan Kirim
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-12 space-y-4 animate-fade-in pt-8 border-t border-slate-200">
                <div class="flex items-center justify-between pb-2">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Pantauan Logistik & Riwayat Penerimaan</h3>
                </div>

                @php
                    // Tarik data pesanan yang sudah dikirim (shipped) atau selesai (delivered) secara Real-Time
                    $trackedOrders = \App\Models\Order::where('payment_status', 'paid')
                                        ->whereIn('shipping_status', ['shipped', 'delivered'])
                                        ->orderBy('updated_at', 'desc')
                                        ->get();
                @endphp

                @if($trackedOrders->isEmpty())
                    <div class="bg-white border border-slate-200 rounded-3xl p-8 text-center shadow-sm">
                        <p class="text-xs font-semibold text-slate-400">Belum ada riwayat paket yang sedang dalam perjalanan atau selesai.</p>
                    </div>
                @else
                    <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-widest">
                                <tr>
                                    <th class="p-4">Nota & Pembeli</th>
                                    <th class="p-4">Ekspedisi & Resi</th>
                                    <th class="p-4 text-center">Status Barang</th>
                                    <th class="p-4 text-right">Waktu Update</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-bold text-slate-800">
                                @foreach($trackedOrders as $trk)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-4">
                                        <span class="block font-mono text-indigo-600">{{ $trk->id }}</span>
                                        <span class="block text-slate-900 mt-1 uppercase">{{ $trk->customer_name }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="block text-slate-900 uppercase">{{ $trk->shipping_courier }}</span>
                                        <span class="block text-[10px] text-slate-500 font-mono mt-1">{{ $trk->tracking_number ?? 'Menunggu Update Sistem' }}</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($trk->shipping_status === 'delivered')
                                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                Telah Diterima
                                            </span>
                                        @else
                                            <span class="bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5 shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                                Dalam Perjalanan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right text-[10px] text-slate-400 font-mono">
                                        {{ $trk->updated_at->diffForHumans() }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    @elseif($activeTab === 'stock')
        <div class="space-y-6">
            <div>
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Audit Stok &amp; Purchase Order (PO) Pengadaan</h2>
                <p class="text-gray-400 text-xs mt-0.5">Pantau jumlah inventaris baju daster/batik secara integral. Kirim pesanan restock langsung ke lapak Supplier.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="p-4">Pakaian</th>
                            <th class="p-4">SKU</th>
                            <th class="p-4">Supplier Partner</th>
                            <th class="p-4">Stok Saat Ini</th>
                            <th class="p-4">Harga Jual</th>
                            <th class="p-4 text-right">Opsi Pengadaan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 text-slate-950 font-extrabold">{{ $product->name }}</td>
                                <td class="p-4 font-mono font-black text-slate-500">{{ $product->sku }}</td>
                                <td class="p-4 font-black text-indigo-700 uppercase tracking-wider text-[11px]">{{ $product->supplier_brand }}</td>
                                <td class="p-4">
                                    <span class="font-mono font-black px-2.5 py-1 rounded-lg text-xs {{ $product->stock < 10 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-50 text-slate-850' }}">
                                        {{ $product->stock }} pcs
                                    </span>
                                </td>
                                <td class="p-4 font-black font-mono text-slate-950">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('admin.restock.store') }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="number" name="qty" value="30" min="5" class="w-16 bg-slate-50 border border-slate-300 rounded-lg p-1.5 text-center font-mono text-xs font-bold focus:outline-none">
                                        <input type="hidden" name="price" value="{{ round($product->price * 0.60) }}">
                                        <button type="submit" class="bg-indigo-50 hover:bg-slate-900 text-indigo-950 hover:text-white border border-indigo-200 font-black text-[10px] px-3 py-2 rounded-lg transition-all uppercase tracking-wider cursor-pointer">
                                            Kirim PO
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4 space-y-3">
                <h3 class="font-extrabold text-slate-950 text-xs uppercase tracking-widest">Alur Pengadaan Terkirim (PO Log)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($restockOrders as $ro)
                        <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl text-xs space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="font-mono font-black text-slate-800 bg-slate-200/60 px-2 py-0.5 rounded-md border border-slate-300">{{ $ro->id }}</span>
                                @if($ro->status === 'pending')
                                    <span class="text-indigo-700 text-[10px] font-black uppercase">Menunggu Approval</span>
                                @elseif($ro->status === 'approved')
                                    <span class="text-emerald-700 text-[10px] font-black uppercase">✓ Disetujui</span>
                                @else
                                    <span class="text-rose-700 text-[10px] font-black uppercase">Ditolak</span>
                                @endif
                            </div>
                            <p class="font-black text-slate-900">{{ $ro->qty }} pcs {{ $ro->product_name }}</p>
                            <p class="text-slate-500 font-bold text-[11px]">Mitra: {{ $ro->supplier_brand }} | Nilai PO: <span class="font-mono text-slate-950">Rp {{ number_format($ro->qty * $ro->price, 0, ',', '.') }}</span></p>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs italic font-semibold">Belum ada rekam PO pengadaan yang diajukan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @elseif($activeTab === 'categories')
        <div class="space-y-6">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-tight">Manajemen Kategori Produk</h2>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white p-6 rounded-3xl border border-slate-200 flex gap-4 items-end shadow-sm">
                @csrf
                <div class="flex-1 space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Kategori Baru</label>
                    <input type="text" name="name" required placeholder="Contoh: Kemeja Pria, Gamis, dll" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-indigo-600">
                </div>
                <button type="submit" class="bg-slate-900 text-white font-black px-6 py-3 rounded-xl text-xs uppercase tracking-widest hover:bg-indigo-600 transition-all">Tambah</button>
            </form>

            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-widest">
                        <tr><th class="p-4">Nama Kategori</th><th class="p-4 text-right">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-bold text-slate-800">
                        @foreach($categories as $cat)
                        <tr>
                            <td class="p-4 uppercase">{{ $cat->name }}</td>
                            <td class="p-4 text-right">
                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus Kategori?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($activeTab === 'curation')
        <div class="space-y-6 animate-fade-in">
            <div>
                <h2 class="text-base font-black text-slate-950 uppercase tracking-tight">Kurasi Sentral &amp; Penerbitan Katalog Publik</h2>
                <p class="text-slate-400 text-xs mt-0.5 font-semibold">Tinjau pasokan masuk dari Supplier, hitung margin, pasang varian ukuran/warna, lalu tentukan harga jual eceran Anda.</p>
            </div>

            <div class="bg-white border border-slate-300 rounded-[2rem] overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-300 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 p-4">
                            <th class="p-6">Pakaian &amp; Asal Supplier</th>
                            <th class="p-6">Harga Grosir Asal</th>
                            <th class="p-6">Harga Ecer Jual Toko</th>
                            <th class="p-6">Status Publikasi</th>
                            <th class="p-6 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold bg-white text-slate-700">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-6 flex items-center gap-4">
                                    <img src="{{ $product->image_path }}" class="w-12 h-12 object-cover rounded-xl border border-slate-200">
                                    <div>
                                        <span class="font-extrabold text-slate-900 text-sm block">{{ $product->name }}</span>
                                        <span class="text-[10px] bg-indigo-50 border border-indigo-100 text-indigo-700 font-black px-1.5 py-0.5 rounded uppercase mt-0.5 inline-block">{{ $product->supplier_brand }}</span>
                                    </div>
                                </td>
                                <td class="p-6 font-mono text-slate-500">Rp {{ number_format($product->wholesale_price, 0, ',', '.') }} / {{ $product->wholesale_unit }}</td>
                                <td class="p-6 font-mono font-black text-slate-900">
                                    {{ $product->price ? 'Rp ' . number_format($product->price, 0, ',', '.') : 'Belum Ditentukan' }}
                                </td>
                                <td class="p-6">
                                    @if($product->is_published)
                                        <span class="text-emerald-700 text-[9px] bg-emerald-50 font-black px-2 py-0.5 rounded border border-emerald-200">✓ TAMPIL DI WEB</span>
                                    @else
                                        <span class="text-amber-700 text-[9px] bg-amber-50 font-black px-2 py-0.5 rounded border border-amber-200">⚠️ DISEMBUNYIKAN (DRAFT)</span>
                                    @endif
                                </td>
                                <td class="p-6 text-right">
                                    <a href="?tab=curation&action=curate&id={{ $product->id }}" class="bg-slate-900 hover:bg-indigo-600 text-white font-black text-[10px] px-3.5 py-2 rounded-xl uppercase tracking-wider transition-all">Kurasi &amp; Spek</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(request('action') === 'curate')
            @php $curItem = $products->find(request('id')); @endphp
            @if($curItem)
                <div class="fixed inset-0 flex items-center justify-center p-4 z-50 animate-fade-in">
                    <div class="bg-white rounded-[2rem] border border-slate-300 max-w-lg w-full p-8 space-y-5 shadow-2xl overflow-y-auto max-h-[90vh]">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                            <h3 class="text-lg font-black text-slate-900 uppercase font-sans">Lembar Validasi &amp; Penentuan Spek Retail</h3>
                            <a href="?tab=curation" class="text-slate-400 text-lg font-black">✕</a>
                        </div>

                        <form action="{{ route('admin.products.curate', $curItem->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs font-medium space-y-1">
                                <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Nota Modal Masuk Supplier:</p>
                                <p class="text-slate-900 font-extrabold text-sm uppercase">{{ $curItem->name }} (SKU: {{ $curItem->sku }})</p>
                                <p class="text-slate-700 font-mono font-bold">Harga Setoran Mitra: <span class="text-indigo-600">Rp {{ number_format($curItem->wholesale_price, 0, ',', '.') }} / {{ $curItem->wholesale_unit }}</span></p>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Judul Komersial Pasar</label>
                                <input type="text" name="name" value="{{ $curItem->name }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Harga Jual Eceran (Per Pcs)</label>
                                    <input type="number" name="retail_price" value="{{ $curItem->price ?? round($curItem->wholesale_price / 12 * 1.3) }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-mono font-black text-indigo-600 focus:ring-2 focus:ring-indigo-600">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Ubah Kategori Komersial</label>
                                    <select name="category_id" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 uppercase">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $curItem->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @php
                                $existingSizes = [];
                                $existingStocks = [];
                                $existingColors = [];
                                $hasVariants = $curItem->variants->count() > 0;

                                if ($hasVariants) {
                                    foreach($curItem->variants as $v) {
                                        $parts = explode(' - ', $v->name);
                                        if(count($parts) == 2) {
                                            $szLower = strtolower(trim($parts[0]));
                                            $col = ucwords(strtolower(trim($parts[1])));

                                            if (!in_array($szLower, $existingSizes)) $existingSizes[] = $szLower;
                                            $existingStocks[$szLower] = ($existingStocks[$szLower] ?? 0) + $v->stock;
                                            if (!in_array($col, $existingColors)) $existingColors[] = $col;
                                        }
                                    }
                                }
                                $colorsValue = count($existingColors) > 0 ? implode(', ', $existingColors) : 'Merah, Hitam, Navy, Biru';
                            @endphp

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Ketersediaan Ukuran & Input Detail Stok</label>
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 grid grid-cols-2 gap-4">
                                    @foreach(['s', 'm', 'l', 'xl', 'xxl', 'all size'] as $index => $sz)
                                        @php
                                            $safeSz = str_replace(' ', '_', $sz);
                                            $isChecked = false;
                                            $stockVal = '';

                                            if ($hasVariants) {
                                                // Jika sudah pernah dikurasi, ingat datanya!
                                                $isChecked = in_array($sz, $existingSizes);
                                                $stockVal = $existingStocks[$sz] ?? '';
                                            } else {
                                                // Jika pertama kali kurasi, jadikan stok total masuk ke ukuran S (index 0)
                                                if ($index === 0) {
                                                    $isChecked = true;
                                                    $stockVal = $curItem->stock;
                                                }
                                            }
                                        @endphp
                                        <div class="flex items-center justify-between gap-2 border-b border-slate-200 pb-2">
                                            <label class="flex items-center gap-2 cursor-pointer uppercase tracking-wider text-xs font-black text-slate-800">
                                                <input type="checkbox" name="sizes[]" value="{{ $sz }}" {{ $isChecked ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-0"> {{ $sz }}
                                            </label>
                                            <input type="number" name="stock_{{ $safeSz }}" value="{{ $stockVal }}" placeholder="Jml Stok" min="0" class="w-24 bg-white border border-slate-300 rounded-lg px-2 py-1.5 text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-600">
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-[10px] text-slate-500 italic">*Stok total otomatis dimasukkan ke varian pertama saat pertama kali kurasi.</p>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Variasi Pilihan Warna (Pisah dengan Tanda Koma)</label>
                                <input type="text" name="colors" value="{{ $colorsValue }}" placeholder="Contoh: Merah, Hitam, Kuning, Hijau" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi Narasi Katalog Pembeli</label>
                                <textarea name="description" rows="3" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-900">{{ $curItem->description }}</textarea>
                            </div>

                            <div class="space-y-3 pt-2 border-t border-slate-200">
                                <div class="grid grid-cols-3 gap-3 items-center">
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Ganti Foto Utama (1 File)</label>
                                        <input type="file" name="image" class="w-full text-xs text-slate-500 file:py-1 file:px-2 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 font-semibold">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-emerald-600">Tambah Foto Galeri (+)</label>
                                        <input type="file" name="gallery[]" multiple class="w-full text-xs text-slate-500 file:py-1 file:px-2 file:rounded-lg file:border-0 file:bg-emerald-50 file:text-emerald-700 font-semibold">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Web</label>
                                        <select name="is_published" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-black uppercase text-slate-900">
                                            <option value="1" {{ $curItem->is_published ? 'selected' : '' }}>✓ Rilis ke Publik</option>
                                            <option value="0" {{ !$curItem->is_published ? 'selected' : '' }}>✕ Sembunyikan (Draft)</option>
                                        </select>
                                    </div>
                                </div>

                                @if(!empty($curItem->gallery_images))
                                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                                        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Kelola Galeri Tambahan Saat Ini</span>
                                        <div class="flex flex-wrap gap-3">
                                            @foreach($curItem->gallery_images as $index => $gImg)
                                                <div class="relative w-16 h-16 rounded-lg overflow-visible border border-slate-300 shadow-sm">
                                                    <img src="{{ $gImg }}" class="w-full h-full object-cover rounded-lg">
                                                    <button type="submit" form="delete-img-form-{{ $curItem->id }}-{{ $index }}" class="absolute -top-2 -right-2 bg-rose-600 text-white w-5 h-5 flex items-center justify-center rounded-full text-[10px] font-black shadow-md hover:bg-rose-700 hover:scale-110 transition-transform cursor-pointer" title="Hapus Foto Ini">✕</button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 hover:bg-slate-900 text-white font-black py-4 rounded-xl text-xs uppercase tracking-widest transition-all mt-4 shadow-md">💾 Simpan &amp; Terapkan Spesifikasi</button>
                        </form>

                        @if(!empty($curItem->gallery_images))
                            @foreach($curItem->gallery_images as $index => $gImg)
                                <form id="delete-img-form-{{ $curItem->id }}-{{ $index }}" action="{{ route('admin.products.delete_image', $curItem->id) }}" method="POST" class="hidden">
                                    @csrf
                                    <input type="hidden" name="image_url" value="{{ $gImg }}">
                                </form>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        @endif

    @elseif($activeTab === 'reports')
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 p-6 rounded-3xl flex flex-col sm:flex-row items-end justify-between gap-4">
                <form action="" method="GET" class="flex flex-wrap items-center gap-4">
                    <input type="hidden" name="tab" value="reports">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Bulan</label>
                        <select name="month" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-black uppercase tracking-wider cursor-pointer">
                            @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $mKey => $mVal)
                                <option value="{{ $mKey }}" {{ $reportMonth === $mKey ? 'selected' : '' }}>{{ $mVal }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Tahun</label>
                        <select name="year" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs font-black uppercase tracking-wider cursor-pointer">
                            <option value="2026" {{ $reportYear === '2026' ? 'selected' : '' }}>2026</option>
                            <option value="2025" {{ $reportYear === '2025' ? 'selected' : '' }}>2025</option>
                        </select>
                    </div>
                </form>

                <button onclick="window.print()" class="bg-slate-900 hover:bg-indigo-600 text-white font-black text-xs px-5 py-3.5 rounded-xl transition-all flex items-center gap-2 uppercase tracking-wider cursor-pointer shadow-xs">
                    Cetak Laporan Formal (PDF)
                </button>
            </div>

            <div id="printable-report-area" class="bg-white border-2 border-slate-900 p-10 rounded-3xl space-y-6 font-serif">
                <div class="text-center pb-6 border-b-4 border-slate-900">
                    <h2 class="text-xl font-black text-slate-950 uppercase tracking-widest">UMKM MAYAPADA PONTIANAK</h2>
                    <p class="text-xs text-slate-500 italic mt-1">Jl. Nusa Indah 1 Blok E No. 2, Kel. Darat Sekip, Pontianak Kota</p>
                    <h3 class="text-md font-bold text-indigo-700 uppercase mt-4 underline tracking-widest">LAPORAN PENDAPATAN &amp; LABA RUGI PENJUALAN</h3>
                    <p class="text-xs text-slate-500 font-mono mt-1 font-bold">Periode Buku: Bulan {{ $reportMonth }} Tahun {{ $reportYear }}</p>
                </div>

                @if($reportTransactions->isEmpty())
                    <div class="py-12 text-center text-slate-400 italic font-semibold text-xs leading-relaxed">
                        Tidak ada data transaksi lunas tercatat pada periode bulan {{ $reportMonth }}/{{ $reportYear }}.
                    </div>
                @else
                    <table class="w-full text-left text-xs border-collapse border-t border-b border-gray-300">
                        <thead>
                            <tr class="border-b border-gray-900 text-gray-700 font-bold font-mono">
                                <th class="py-2.5">Tanggal</th>
                                <th class="py-2.5">Nomor Nota (Order ID)</th>
                                <th class="py-2.5">Pelanggan</th>
                                <th class="py-2.5 text-right">Nilai Kotor</th>
                                <th class="py-2.5 text-right">HPP Konsinyasi (75%)</th>
                                <th class="py-2.5 text-right">Laba Mayapada (25%)</th>
                                <th class="py-2.5 text-center">Status Logistik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reportTransactions as $trx)
                                @php $itemVal = $trx->total_price - $trx->shipping_cost; @endphp
                                <tr class="text-slate-800">
                                    <td class="py-2 font-mono">{{ $trx->created_at->format('d/m') }}</td>
                                    <td class="py-2 font-mono font-bold">{{ $trx->id }}</td>
                                    <td class="py-2">{{ $trx->customer_name }}</td>
                                    <td class="py-2 text-right font-mono">Rp {{ number_format($itemVal, 0, ',', '.') }}</td>
                                    <td class="py-2 text-right font-mono text-gray-500">Rp {{ number_format($itemVal * 0.75, 0, ',', '.') }}</td>
                                    <td class="py-2 text-right font-mono text-indigo-700 font-bold">Rp {{ number_format($itemVal * 0.25, 0, ',', '.') }}</td>
                                    <td class="py-2 text-right font-mono text-indigo-700 font-bold">Rp {{ number_format($itemVal * 0.25, 0, ',', '.') }}</td>

                                    <td class="py-2 text-center">
                                        @if($trx->shipping_status === 'delivered')
                                            <span class="text-[9px] bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-1 rounded font-black uppercase tracking-wider">✔ Diterima</span>
                                        @elseif($trx->shipping_status === 'shipped')
                                            <span class="text-[9px] bg-amber-50 text-amber-700 border border-amber-200 px-2 py-1 rounded font-black uppercase tracking-wider">✈ Dikirim</span>
                                        @else
                                            <span class="text-[9px] bg-blue-50 text-blue-700 border border-blue-200 px-2 py-1 rounded font-black uppercase tracking-wider">📦 Dikemas</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="space-y-2 text-xs max-w-md ml-auto pt-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <span class="block font-bold text-gray-700 border-b border-gray-200 pb-1 uppercase tracking-wide">RINGKASAN REKAPITULASI KEUANGAN</span>
                        <div class="flex justify-between text-gray-600">
                            <span>Total Omset Kotor (Pakaian):</span>
                            <strong class="text-slate-900 font-mono">Rp {{ number_format($reportSums['gross'], 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Total Kongsi Pengadaan (75% Supplier):</span>
                            <strong class="text-gray-500 font-mono">Rp {{ number_format($reportSums['cost'], 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex justify-between text-indigo-800 border-t border-dashed border-gray-200 pt-2 font-bold text-sm">
                            <span>Laba Bersih Mayapada (25%):</span>
                            <strong class="font-mono">Rp {{ number_format($reportSums['profit'], 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    @elseif($activeTab === 'users')
        <div class="space-y-4">
            <div>
                <h2 class="text-base font-bold text-gray-900 tracking-tight">Partner &amp; Aktor Aplikasi</h2>
                <p class="text-gray-400 text-xs mt-0.5">Daftar pengguna terdaftar di sistem C2C UMKM Mayapada Pontianak.</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-xs">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                            <th class="p-4">Nama Lengkap</th>
                            <th class="p-4">Email Hak Akses</th>
                            <th class="p-4">Role / Jabatan</th>
                            <th class="p-4">Brand (Khusus Supplier)</th>
                            <th class="p-4">Kontak / WA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-semibold text-slate-700">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 font-bold text-slate-900">{{ $user->name }}</td>
                                <td class="p-4 font-mono">{{ $user->email }}</td>
                                <td class="p-4 uppercase font-bold text-[10px]">
                                    <span class="px-2.5 py-1 rounded-full border {{ $user->role === 'admin' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : ($user->role === 'supplier' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-blue-50 border-blue-200 text-blue-700') }}">
                                        {{ $user->role === 'customer' ? 'Konsumen' : $user->role }}
                                    </span>
                                </td>
                                <td class="p-4 font-semibold text-slate-700">{{ $user->brand_name ?? '-' }}</td>
                                <td class="p-4 font-mono">{{ $user->contact ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<style>
    @media print {
        #mayapada-store-header, #admin-portal-tabs, #mayapada-system-footer, .border-b, button {
            display: none !important;
        }
        #printable-report-area {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    }
</style>
