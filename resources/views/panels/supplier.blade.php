@php
    $stab = request('stab', 'inventory');
    $brandName = auth()->user()->brand_name ?? 'SUPPLIER';

    $myProducts = $products->where('supplier_id', auth()->id());
    $myRestockOrders = $restockOrders->where('supplier_id', auth()->id());

    $totalItems = $myProducts->sum('stock');
    $lowStockCount = $myProducts->where('stock', '<', 10)->count();
    $pendingPoCount = $myRestockOrders->where('status', 'pending')->count();
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
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold">🎉 {{ session('success') }}</div>
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
                    Permintaan Restock PO dari Admin Mayapada
                </h2>
                <a href="?stab=restock" class="text-[10px] bg-indigo-50 font-black text-indigo-700 px-5 py-2.5 rounded-xl hover:bg-indigo-100 transition-all uppercase tracking-widest border border-indigo-100 shadow-sm">
                    Refresh List PO
                </a>
            </div>

            @if($myRestockOrders->isEmpty())
                <div class="bg-white border border-slate-300 rounded-[2rem] py-16 text-center px-4 shadow-sm">
                    <p class="text-slate-400 text-sm font-semibold">Belum ada nota PO pengadaan barang yang masuk.</p>
                </div>
            @else
                <div class="space-y-4">
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
                                <option value="lusin" {{ $itemData && $itemData->wholesale_unit === 'lusin' ? 'selected' : '' }}>Lusin</option>
                                <option value="kodi" {{ $itemData && $itemData->wholesale_unit === 'kodi' ? 'selected' : '' }}>Kodi</option>
                                <option value="bal" {{ $itemData && $itemData->wholesale_unit === 'bal' ? 'selected' : '' }}>Bal</option>
                            </select>
                        </div>
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
