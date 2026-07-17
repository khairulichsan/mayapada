@php
    $stab = request('stab', 'inventory');
    $brandName = auth()->user()->brand_name ?? 'SUPPLIER';

    $myProducts = $products->where('supplier_id', auth()->id());
    $myRestockOrders = $restockOrders->where('supplier_id', auth()->id());

    $totalItems = $myProducts->sum('stock');
    $lowStockCount = $myProducts->where('stock', '<', 10)->count();
    $pendingPoCount = $myRestockOrders->where('status', 'pending')->count();

    // FILTER 1: Request Kustom yang Masih Menunggu (Baru masuk atau Menunggu Jawaban Admin)
    $pendingProcurements = $procurementRequests->filter(function($req) {
        $isTarget = is_null($req->target_supplier_id) || $req->target_supplier_id == auth()->id();
        if ($req->status === 'pending' && $isTarget) return true;
        if ($req->status === 'supplier_bid' && $req->responded_by_id == auth()->id()) return true;
        return false;
    });

    // FILTER 2: Request Kustom yang Sudah Deal (PO_Created)
    $dealProcurements = $procurementRequests->where('status', 'po_created')
                                            ->where('responded_by_id', auth()->id());
@endphp

<div id="supplier-panel-container" class="pt-2 pb-12">
    <div class="flex flex-col md:flex-row md:items-end justify-between pb-6 mb-8 border-b border-slate-300 gap-4">
        <div>
            <span class="text-slate-500 font-black text-[10px] tracking-widest uppercase block mb-1">KONSINYASI SUPPLIER</span>
            <h1 class="text-3xl font-black text-slate-950 tracking-tighter uppercase font-sans">{{ $brandName }} PORTAL</h1>
            <p class="text-slate-500 text-sm font-medium mt-1">Dashboard mandiri untuk mengunggah katalog & menyetujui permintaan restock toko.</p>
        </div>
        <div class="flex items-center gap-1 bg-white p-1 rounded-full border border-slate-300 shadow-xs">
            <a href="?stab=inventory" class="px-5 py-2.5 rounded-full text-[11px] font-black tracking-wider uppercase transition-all flex items-center gap-2 {{ $stab === 'inventory' ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">Kelola Stok</a>
            <a href="?stab=restock" class="px-5 py-2.5 rounded-full text-[11px] font-black tracking-wider uppercase transition-all relative {{ $stab === 'restock' ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:text-slate-900' }}">Permintaan Restock @if($pendingPoCount > 0)<span class="absolute -top-1 -right-1 bg-rose-600 text-white font-black text-[9px] w-4 h-4 flex items-center justify-center rounded-full">{{ $pendingPoCount }}</span>@endif</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        <div class="bg-white border border-slate-300 p-6 rounded-[2rem] flex items-center gap-5 shadow-sm">
            <div class="text-2xl font-black text-slate-900 font-mono">{{ $totalItems }} pcs</div>
        </div>
        <div class="bg-white border border-slate-300 p-6 rounded-[2rem] flex items-center gap-5 shadow-sm">
            <div class="text-2xl font-black text-slate-900 font-mono">{{ $lowStockCount }} Item Kritis</div>
        </div>
        <div class="bg-white border border-slate-300 p-6 rounded-[2rem] flex items-center gap-5 shadow-sm">
            <div class="text-2xl font-black text-slate-900 font-mono">{{ $pendingPoCount }} Lembar PO</div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold shadow-sm">🎉 {{ session('success') }}</div>
    @endif

    @if($stab === 'inventory')
        <div class="space-y-6 animate-fade-in">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black text-slate-900 uppercase font-sans">Daftar Gudang Masuk</h2>
                <a href="?stab=inventory&action=create" class="bg-slate-900 hover:bg-indigo-600 text-white font-black text-[11px] px-6 py-3 rounded-2xl uppercase tracking-widest flex items-center gap-2">➕ Tambah Pasokan Baju</a>
            </div>

            <div class="bg-white border border-slate-300 rounded-[2rem] overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-300 text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 p-4">
                            <th class="p-6">Produk</th>
                            <th class="p-6">SKU</th>
                            <th class="p-6">Harga Kulakan Grosir</th>
                            <th class="p-6">Kontrol Web (Status Kurasi)</th>
                            <th class="p-6 text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold bg-white text-slate-700">
                        @foreach($myProducts as $prod)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-6 flex items-center gap-4">
                                    <img src="{{ $prod->image_path }}" class="w-12 h-12 object-cover rounded-xl border border-slate-200">
                                    <span class="font-extrabold text-slate-900 text-sm">{{ $prod->name }}</span>
                                </td>
                                <td class="p-6 font-mono font-bold">{{ $prod->sku }}</td>
                                <td class="p-6 font-black text-slate-900 font-mono">Rp {{ number_format($prod->wholesale_price, 0, ',', '.') }} / {{ $prod->wholesale_unit }}</td>
                                <td class="p-6">
                                    @if($prod->is_published)
                                        <span class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-[9px] font-black px-2.5 py-1 rounded-md uppercase">Disetujui Jual (Eceran: Rp {{ number_format($prod->price, 0, ',', '.') }})</span>
                                    @else
                                        <span class="bg-amber-50 border border-amber-200 text-amber-700 text-[9px] font-black px-2.5 py-1 rounded-md uppercase">Draft (Menunggu Kurasi Admin)</span>
                                    @endif
                                </td>
                                <td class="p-6 text-right">
                                    <div class="inline-flex gap-2">
                                        <a href="?stab=inventory&action=edit&id={{ $prod->id }}" class="text-indigo-600 font-bold hover:underline">Edit</a>
                                        <form action="{{ route('supplier.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Hapus baju ini?')">@csrf @method('DELETE')<button type="submit" class="text-rose-600 font-bold hover:underline cursor-pointer">Hapus</button></form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($stab === 'restock')
        <div class="space-y-6 animate-fade-in">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black text-slate-900 tracking-tighter uppercase font-sans">
                    Pusat Pengadaan &amp; Restock
                </h2>
                <a href="?stab=restock" class="text-[10px] bg-indigo-50 font-black text-indigo-700 px-5 py-2.5 rounded-xl hover:bg-indigo-100 transition-all uppercase tracking-widest border border-indigo-100 shadow-sm">
                    Refresh Log
                </a>
            </div>

            <div class="space-y-4">
                <h3 class="text-sm font-black text-slate-900 uppercase border-b border-slate-200 pb-2">Permintaan Pengadaan Kustom (Butuh Penawaran)</h3>

                @if($pendingProcurements->isEmpty())
                    <div class="bg-slate-50 border border-slate-200 border-dashed rounded-[2rem] py-10 text-center px-4">
                        <p class="text-slate-400 text-xs font-semibold">Tidak ada pengajuan request pakaian kustom dari Admin saat ini.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        @foreach($pendingProcurements as $req)
                            <div x-data="{ action: null }" class="bg-white border border-indigo-100 p-5 rounded-[2rem] shadow-sm">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="text-sm font-black text-slate-900 uppercase">{{ $req->request_title }}</h4>
                                        <p class="text-xs text-slate-500 font-medium mt-1">Spesifikasi: <span class="font-bold text-slate-700">{{ $req->color }} / Size {{ $req->size }}</span></p>
                                        <p class="text-xs text-slate-500 font-medium mt-0.5">Admin membutuhkan pasokan sebanyak <span class="font-bold text-indigo-600">{{ $req->qty_requested }} Pcs</span>.</p>
                                    </div>
                                    @if($req->status === 'pending')
                                        <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] px-2 py-1 rounded font-black uppercase tracking-widest shrink-0">Request Baru</span>
                                    @else
                                        <span class="bg-amber-50 text-amber-700 border border-amber-200 text-[10px] px-2 py-1 rounded font-black uppercase tracking-widest shrink-0">Menunggu Admin</span>
                                    @endif
                                </div>

                                @if($req->status === 'pending')
                                    <div x-show="action === null" class="flex gap-2">
                                        <button @click="action = 'accept'" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black py-3 rounded-xl uppercase tracking-widest transition-colors cursor-pointer shadow-sm">Sanggup &amp; Beri Harga</button>
                                        <button @click="action = 'reject'" class="flex-1 bg-rose-50 hover:bg-rose-500 hover:text-white text-rose-700 border border-rose-200 text-[10px] font-black py-3 rounded-xl uppercase tracking-widest transition-colors cursor-pointer">Tolak</button>
                                    </div>

                                    <form x-show="action === 'accept'" action="{{ route('supplier.procurement.bid', $req->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 bg-slate-50 p-5 rounded-2xl border border-slate-200 mt-3" style="display: none;" x-transition>
                                        @csrf

                                        <div class="space-y-1">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Pakaian</label>
                                            <input type="text" name="name" value="{{ $req->request_title }} - {{ $req->color }} Size {{ $req->size }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-600">
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">SKU Gudang</label>
                                                <input type="text" name="sku" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-mono font-black uppercase text-slate-900 focus:ring-2 focus:ring-indigo-600">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</label>
                                                <select name="category_id" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 uppercase focus:ring-2 focus:ring-indigo-600">
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-3 gap-3">
                                            <div class="col-span-2 space-y-1">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Harga Grosir (Penawaran)</label>
                                                <input type="number" name="offered_price" required placeholder="Misal: 45000" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-mono font-black text-slate-900 focus:ring-2 focus:ring-indigo-600">
                                            </div>
                                            <div class="col-span-1 space-y-1">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Satuan Unit</label>
                                                <select name="wholesale_unit" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 uppercase focus:ring-2 focus:ring-indigo-600">
                                                    <option value="pcs">Pcs</option>
                                                    <option value="lusin">Lusin</option>
                                                    <option value="kodi">Kodi</option>
                                                    <option value="bal">Bal</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Jumlah Stok (Pcs)</label>
                                                <input type="number" name="stock" value="{{ $req->qty_requested }}" required class="w-full bg-slate-100 border border-slate-300 rounded-xl px-4 py-3 text-xs font-mono font-black text-slate-900 focus:ring-2 focus:ring-indigo-600">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Estimasi Barang Kirim</label>
                                                <input type="date" name="estimated_delivery" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-600">
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi Bahan Kain</label>
                                            <textarea name="description" rows="2" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-indigo-600"></textarea>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Unggah Foto Pakaian (Maks 2MB)</label>
                                            <input type="file" name="image" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                        </div>

                                        <div class="flex gap-2 pt-2 border-t border-slate-200">
                                            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black py-3 rounded-xl uppercase tracking-widest cursor-pointer transition-colors shadow-sm">💾 Kirim Pengajuan</button>
                                            <button type="button" @click="action = null" class="bg-slate-200 hover:bg-slate-300 text-slate-600 text-[11px] font-black py-3 px-5 rounded-xl uppercase tracking-widest cursor-pointer transition-colors">Batal</button>
                                        </div>
                                    </form>

                                    <form x-show="action === 'reject'" action="{{ route('supplier.procurement.reject', $req->id) }}" method="POST" class="mt-2 space-y-3 bg-rose-50 p-4 rounded-xl border border-rose-200" style="display: none;" x-transition>
                                        @csrf
                                        <p class="text-[11px] text-rose-700 font-bold leading-relaxed">Yakin menolak? Jika ditolak, pesanan ini tidak dapat ditarik kembali.</p>
                                        <div class="flex gap-2">
                                            <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black py-2.5 rounded-lg uppercase tracking-wider cursor-pointer transition-colors shadow-sm">Ya, Tolak Permintaan</button>
                                            <button type="button" @click="action = null" class="bg-white hover:bg-slate-100 text-slate-600 border border-slate-300 text-[10px] font-black py-2.5 px-4 rounded-lg uppercase tracking-wider cursor-pointer transition-colors">Batal</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl mt-3 flex items-center justify-between">
                                        <div>
                                            <span class="block text-[9px] font-black text-amber-600 uppercase tracking-widest">Penawaran Anda:</span>
                                            <span class="font-mono font-black text-amber-900 text-sm">Rp {{ number_format($req->offered_price, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="block text-[9px] font-black text-amber-600 uppercase tracking-widest">Janji Siap:</span>
                                            <span class="text-xs font-bold text-amber-800 font-mono">{{ \Carbon\Carbon::parse($req->estimated_delivery)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-4 pt-4">
                <h3 class="text-sm font-black text-slate-900 uppercase border-b border-slate-200 pb-2">Purchase Order (PO) Aktif</h3>

                @if($myRestockOrders->isEmpty() && $dealProcurements->isEmpty())
                    <div class="bg-white border border-slate-300 rounded-[2rem] py-16 text-center px-4 shadow-sm">
                        <p class="text-slate-400 text-sm font-semibold">Belum ada nota PO pengadaan barang yang masuk.</p>
                    </div>
                @else
                    <div class="space-y-4">

                        @foreach($dealProcurements as $deal)
                            <div class="bg-white border border-slate-300 rounded-[2rem] p-6 hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <span class="font-extrabold text-[10px] font-mono tracking-widest text-slate-900 bg-white border border-slate-900 px-3 py-1 rounded">
                                            REQ-KUSTOM-{{ $deal->id }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">
                                            {{ $deal->updated_at->format('d M Y') }}
                                        </span>
                                    </div>

                                    <h3 class="font-black text-slate-900 text-sm uppercase tracking-tight">
                                        Permintaan Pengiriman: {{ $deal->qty_requested }} pcs {{ $deal->request_title }}
                                    </h3>
                                    <p class="text-slate-400 text-[11px] font-mono font-bold uppercase tracking-wider">
                                        Spek: Warna {{ $deal->color }} / Size {{ $deal->size }} <span class="mx-1 font-sans text-slate-300">|</span> Harga Pengadaan Konsinyasi: Rp {{ number_format($deal->offered_price, 0, ',', '.') }} / pcs
                                    </p>
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center gap-6">
                                    <div class="flex flex-col md:items-end border-r border-slate-200 pr-6">
                                        <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Nilai Transaksi</span>
                                        <span class="font-mono font-black text-slate-900 text-lg">
                                            Rp {{ number_format($deal->qty_requested * $deal->offered_price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <span class="bg-slate-900 text-white px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm">
                                        ✓ Telah Disetujui
                                    </span>
                                </div>
                            </div>
                        @endforeach

                        @foreach($myRestockOrders as $ro)
                            <div class="bg-white border border-slate-300 rounded-[2rem] p-6 hover:shadow-md transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <span class="font-extrabold text-[10px] font-mono tracking-widest text-slate-900 bg-white border border-slate-900 px-3 py-1 rounded">
                                            {{ $ro->id }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">
                                            {{ $ro->created_at->format('d M Y') }}
                                        </span>
                                    </div>

                                    <h3 class="font-black text-slate-900 text-sm uppercase tracking-tight">
                                        Permintaan Pengiriman: {{ $ro->qty }} pcs {{ $ro->product_name }}
                                    </h3>
                                    <p class="text-slate-400 text-[11px] font-mono font-bold uppercase tracking-wider">
                                        SKU ID: {{ $ro->sku }} <span class="mx-1 font-sans text-slate-300">|</span> Harga Pengadaan Konsinyasi: Rp {{ number_format($ro->price, 0, ',', '.') }} / pcs
                                    </p>
                                </div>

                                <div class="flex flex-col md:flex-row md:items-center gap-6">
                                    <div class="flex flex-col md:items-end border-r border-slate-200 pr-6">
                                        <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Nilai Transaksi</span>
                                        <span class="font-mono font-black text-slate-900 text-lg">
                                            Rp {{ number_format($ro->qty * $ro->price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    @if($ro->status === 'pending')
                                        <div class="flex gap-2">
                                            <form action="{{ route('supplier.restock.resolve', $ro->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="bg-slate-900 hover:bg-indigo-600 text-white font-black px-5 py-3 rounded-xl text-[10px] transition-all flex items-center gap-1.5 uppercase tracking-widest shadow-md cursor-pointer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                    Setujui &amp; Kirim
                                                </button>
                                            </form>
                                            <form action="{{ route('supplier.restock.resolve', $ro->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-black px-5 py-3 rounded-xl text-[10px] transition-all flex items-center gap-1.5 uppercase tracking-widest border border-rose-200 cursor-pointer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                    Tolak PO
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($ro->status === 'approved')
                                        <span class="bg-slate-900 text-white px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm">
                                            ✓ Telah Disetujui
                                        </span>
                                    @else
                                        <span class="bg-rose-50 border border-rose-200 text-rose-600 px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                            Ditolak
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if(request('action') === 'create' || request('action') === 'edit')
        @php $editId = request('id'); $itemData = $editId ? $myProducts->find($editId) : null; @endphp
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 z-50 animate-fade-in">
            <div class="bg-white rounded-[2rem] border border-slate-300 max-w-lg w-full p-8 space-y-6 shadow-2xl">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-black text-slate-900 uppercase font-sans">{{ $itemData ? 'Ubah Informasi Pasokan' : 'Daftarkan Pasokan Baru' }}</h3>
                    <a href="?stab=inventory" class="text-slate-400 text-lg font-black">✕</a>
                </div>

                <form action="{{ route('supplier.products.save') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @if($itemData)
                        <input type="hidden" name="id" value="{{ $itemData->id }}">
                        <input type="hidden" name="existing_image_path" value="{{ $itemData->image_path }}">
                    @endif

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Pakaian</label>
                        <input type="text" name="name" value="{{ $itemData ? $itemData->name : '' }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-indigo-600">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">SKU Gudang</label>
                            <input type="text" name="sku" value="{{ $itemData ? $itemData->sku : '' }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-mono font-black uppercase text-slate-900 focus:ring-2 focus:ring-indigo-600">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</label>
                            <select name="category_id" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 uppercase">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $itemData && $itemData->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2 space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Harga Grosir Mitra</label>
                            <input type="number" name="wholesale_price" value="{{ $itemData ? $itemData->wholesale_price : '' }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-mono font-black text-slate-900 focus:ring-2 focus:ring-indigo-600">
                        </div>
                        <div class="col-span-1 space-y-1">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Satuan Unit</label>
                            <select name="wholesale_unit" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-bold text-slate-900 uppercase">
                                <option value="pcs" {{ $itemData && $itemData->wholesale_unit === 'pcs' ? 'selected' : '' }}>Pcs</option>
                                <option value="lusin" {{ $itemData && $itemData->wholesale_unit === 'lusin' ? 'selected' : '' }}>Lusin</option>
                                <option value="kodi" {{ $itemData && $itemData->wholesale_unit === 'kodi' ? 'selected' : '' }}>Kodi</option>
                                <option value="bal" {{ $itemData && $itemData->wholesale_unit === 'bal' ? 'selected' : '' }}>Bal</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Jumlah Stok Pakaian</label>
                        <input type="number" name="stock" value="{{ $itemData ? $itemData->stock : '' }}" required class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-mono font-black text-slate-900 focus:ring-2 focus:ring-indigo-600">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi Bahan Kain</label>
                        <textarea name="description" rows="2" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-indigo-600">{{ $itemData ? $itemData->description : '' }}</textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Unggah Berkas Gambar Pakaian asli (Maks 2MB)</label>
                        <input type="file" name="image" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-emerald-600 text-white font-black py-4 rounded-xl text-xs uppercase tracking-widest transition-all mt-2">💾 Kirim Pengajuan Draft</button>
                </form>
            </div>
        </div>
    @endif
</div>
