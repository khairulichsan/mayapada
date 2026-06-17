@php
    $ctab = request('ctab', 'catalog');
    $cart = session('cart', []);
    $cartCount = collect($cart)->sum('qty');
    $search = request('search');
    $categoryFilter = request('category', 'all');

    // Menampilkan hanya produk yang is_published = true
    $filteredProducts = $products->where('is_published', '!=', 0)->when($search, function($query) use ($search) {
        return $query->filter(fn($p) => str_contains(strtolower($p->name), strtolower($search)) || str_contains(strtolower($p->supplier_brand), strtolower($search)));
    })->when($categoryFilter !== 'all', function($query) use ($categoryFilter) {
        return $query->filter(fn($p) => $p->category_id == $categoryFilter);
    });

    $totalSubtotal = 0;
    foreach($cart as $item) {
        $totalSubtotal += $item['price'] * $item['qty'];
    }

    // Ambil order murni milik user yang login (jika ada)
    $myOrders = auth()->check() ? $orders->where('customer_id', auth()->id()) : collect();
@endphp

<div id="customer-panel-container" class="pt-2">

    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-1">
                KATALOG UNGGULAN<span class="text-indigo-650">.</span>
            </h2>
            <p class="text-slate-500 text-sm font-medium">
                Hai, <strong class="text-slate-900 font-extrabold">{{ auth()->check() ? auth()->user()->name : 'Pengunjung Luring' }}</strong> – Temukan busana daster legendaris &amp; batik terbaik Anda.
            </p>
        </div>

        <div id="customer-portal-tabs" class="flex items-center gap-1.5 bg-slate-100 p-1.5 rounded-2xl self-start md:self-auto shadow-xs border border-slate-200">
            <a href="?ctab=catalog" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all flex items-center gap-2 {{ $ctab === 'catalog' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Katalog Produk
            </a>

            <a href="?ctab=cart" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all flex items-center gap-2 relative {{ $ctab === 'cart' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                Keranjang Belanja
                @if($cartCount > 0)
                    <span class="bg-indigo-650 text-white font-black px-2 py-0.5 text-[10px] rounded-full min-w-5 h-5 flex items-center justify-center shadow-xs">{{ $cartCount }}</span>
                @endif
            </a>

            <a href="?ctab=orders" class="px-4 py-2.5 rounded-xl text-xs font-black tracking-wider uppercase transition-all flex items-center gap-2 {{ $ctab === 'orders' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-900' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
                Status Pesanan
            </a>
        </div>
    </div>

    @if($ctab === 'catalog')
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
            <div id="catalog-sidebar-filters" class="space-y-6 lg:border-r lg:border-slate-200 lg:pr-6">
                <div class="space-y-2">
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pencarian</span>
                    <form action="" method="GET" class="relative">
                        <input type="hidden" name="ctab" value="catalog">
                        <input type="hidden" name="category" value="{{ $categoryFilter }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/></svg>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari daster, batik..." class="w-full bg-white border border-slate-300 rounded-xl py-2.5 pl-10 pr-4 text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-all">
                    </form>
                </div>

                <div class="space-y-2">
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Kategori Produk</span>
                    <div class="flex flex-wrap lg:flex-col gap-1.5">
                        <a href="?ctab=catalog&category=all&search={{ $search }}" class="px-3.5 py-2.5 rounded-2xl text-left text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-between border {{ $categoryFilter === 'all' ? 'bg-indigo-600 text-white font-black border-indigo-600 shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-transparent' }}">
                            <span>Semua Kategori</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-md font-black {{ $categoryFilter === 'all' ? 'bg-indigo-700 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $products->where('is_published', true)->count() }}</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="?ctab=catalog&category={{ $cat->id }}&search={{ $search }}" class="px-3.5 py-2.5 rounded-2xl text-left text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-between border {{ $categoryFilter == $cat->id ? 'bg-indigo-600 text-white font-black border-indigo-600 shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-transparent' }}">
                                <span>{{ strtoupper($cat->name) }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-black {{ $categoryFilter == $cat->id ? 'bg-indigo-700 text-white' : 'bg-slate-200 text-slate-800' }}">{{ $products->where('is_published', true)->where('category_id', $cat->id)->count() }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-2">
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Urutkan Harga</span>
                    <select class="w-full bg-white border border-slate-300 rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-600 font-medium text-slate-900 transition-all">
                        <option value="default">Default Produk</option>
                    </select>
                </div>

                <div class="bg-slate-900 border border-slate-800 p-5 rounded-3xl text-white relative overflow-hidden shadow-md">
                    <span class="text-white font-black block text-xs tracking-wider uppercase">UMKM Mayapada Pontianak.</span>
                    <p class="text-[11px] text-slate-300 mt-2.5 leading-relaxed font-medium">
                        Menawarkan produk daster 100% Original dari pemasok utama batik Kencana Ungu dan Gajah Putih.
                    </p>
                    <div class="absolute right-3 bottom-0 text-indigo-500/10 font-black text-4xl select-none uppercase tracking-tighter">MAYAPADA</div>
                </div>
            </div>

            <div class="lg:col-span-3 space-y-4">
                <div class="text-xs text-gray-400 font-mono font-medium">
                    Menampilkan {{ $filteredProducts->count() }} produk dari {{ $products->where('is_published', true)->count() }} yang tersedia
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($filteredProducts as $product)
                        <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden hover:shadow-lg transition-all flex flex-col justify-between h-full group">
                            <div>
                                <div class="aspect-[4/3] bg-slate-50 overflow-hidden relative">
                                    <img src="{{ $product->image_path ?? 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=500&q=80' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    <span class="absolute top-2.5 left-2.5 bg-slate-900/80 text-white text-[10px] font-black px-2.5 py-1 rounded-md tracking-wider uppercase">
                                        {{ $product->supplier_brand }}
                                    </span>
                                </div>

                                <div class="p-5 space-y-1">
                                    <div class="text-[10px] text-slate-400 font-mono tracking-wider font-bold uppercase">{{ $product->sku }}</div>
                                    <h3 class="font-extrabold text-slate-900 text-sm line-clamp-1 uppercase">{{ $product->name }}</h3>
                                    <p class="text-slate-500 text-[11px] mt-2 line-clamp-2 leading-relaxed font-medium">{{ $product->description }}</p>
                                </div>
                            </div>

                            <div class="p-5 pt-0 flex items-center justify-between mt-2">
                                <div class="flex flex-col">
                                    <span class="text-[9px] text-slate-400 uppercase font-black tracking-wider">Harga</span>
                                    <span class="text-slate-900 font-black text-sm font-mono">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                                <a href="?ctab={{ $ctab }}&category={{ $categoryFilter }}&search={{ $search }}&select_product={{ $product->id }}" class="bg-slate-900 hover:bg-indigo-600 text-white font-black px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-1.5 cursor-pointer uppercase tracking-wider shadow-xs">
                                    <span>Beli</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if(request('select_product'))
            @php $selectedProduct = $products->find(request('select_product')); @endphp
            @if($selectedProduct)
                <div class="fixed inset-0 flex items-center justify-center p-4 z-50 animate-fade-in">
                    <div class="bg-white rounded-[2.5rem] border border-slate-300 max-w-4xl w-full p-8 grid grid-cols-1 md:grid-cols-2 gap-8 relative shadow-2xl overflow-y-auto max-h-[90vh]">

                        <a href="?ctab={{ $ctab }}&category={{ $categoryFilter }}&search={{ $search }}" class="absolute top-6 right-6 text-slate-400 hover:text-slate-900 font-black text-xl transition-colors bg-slate-100 w-9 h-9 flex items-center justify-center rounded-full border border-slate-200">✕</a>

                        <div class="space-y-4">
                            <div class="aspect-square bg-slate-50 border border-slate-200 rounded-[2rem] overflow-hidden shadow-xs relative">
                                <img id="mainImageDisplay_{{ $selectedProduct->id }}" src="{{ $selectedProduct->image_path }}" alt="{{ $selectedProduct->name }}" class="w-full h-full object-cover transition-all duration-300">
                                <span class="absolute top-4 left-4 bg-slate-900 text-white font-black text-[10px] px-3 py-1 rounded-md uppercase tracking-wider border border-slate-800">
                                    {{ $selectedProduct->supplier_brand }} BRAND
                                </span>
                            </div>

                            <div class="flex gap-3 overflow-x-auto pb-2 custom-scrollbar">
                                <div onclick="document.getElementById('mainImageDisplay_{{ $selectedProduct->id }}').src = '{{ $selectedProduct->image_path }}'" class="w-20 h-20 shrink-0 bg-slate-100 rounded-xl overflow-hidden border-2 border-indigo-500 cursor-pointer hover:opacity-80 transition-all">
                                    <img src="{{ $selectedProduct->image_path }}" class="w-full h-full object-cover">
                                </div>

                                @if(!empty($selectedProduct->gallery_images))
                                    @foreach($selectedProduct->gallery_images as $gImg)
                                        <div onclick="document.getElementById('mainImageDisplay_{{ $selectedProduct->id }}').src = '{{ $gImg }}'" class="w-20 h-20 shrink-0 bg-slate-150 rounded-xl overflow-hidden border border-slate-200 cursor-pointer opacity-70 hover:opacity-100 hover:border-indigo-400 transition-all">
                                            <img src="{{ $gImg }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col justify-between space-y-6">
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <span class="text-indigo-600 font-mono text-[11px] font-black uppercase tracking-widest block">{{ $selectedProduct->sku }}</span>
                                    <h2 class="text-2xl font-black text-slate-900 uppercase font-sans tracking-tight leading-tight">{{ $selectedProduct->name }}</h2>
                                    <div class="text-xs text-slate-400 font-bold">Kategori: <span class="text-slate-800 uppercase">{{ $selectedProduct->category->name }}</span></div>
                                </div>

                                <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl flex flex-col">
                                    <span class="text-[9px] text-slate-400 uppercase font-black tracking-widest">Harga Eceran Resmi Pasar Sudirman:</span>
                                    <span class="text-2xl font-mono font-black text-slate-950 mt-1">Rp {{ number_format($selectedProduct->price, 0, ',', '.') }} <span class="text-xs font-sans text-slate-400 font-bold">/ pcs</span></span>
                                </div>

                                <div class="space-y-1.5">
                                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi Bahan &amp; Spesifikasi:</span>
                                    <div class="bg-indigo-50/40 p-4 rounded-2xl border border-indigo-100 max-h-32 overflow-y-auto custom-scrollbar">
                                        <p class="text-xs text-slate-600 leading-relaxed font-semibold italic whitespace-pre-line">"{{ $selectedProduct->description }}"</p>
                                    </div>
                                </div>

                                <form action="{{ route('cart.add') }}" method="POST" class="space-y-4 pt-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $selectedProduct->id }}">

                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pilih Varian Kombo (Ukuran - Warna):</label>
                                        <div class="grid grid-cols-1 gap-2 max-h-[140px] overflow-y-auto pr-1">
                                            @foreach($selectedProduct->variants as $index => $v)
                                                <label class="border border-slate-200 rounded-xl p-3 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors bg-white">
                                                    <div class="flex items-center gap-2.5">
                                                        <input type="radio" name="variant_id" value="{{ $v->id }}" {{ $index === 0 ? 'checked' : '' }} class="text-indigo-600 focus:ring-0">
                                                        <span class="text-xs font-black text-slate-900 uppercase tracking-wide">{{ $v->name }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Tersedia: {{ $v->stock }} pcs</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-3 items-end pt-2 border-t border-slate-100">
                                        <div class="col-span-1 space-y-1">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Jumlah Beli</label>
                                            <input type="number" name="qty" value="1" min="1" max="10" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 text-center font-mono font-black text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                                        </div>
                                        <div class="col-span-2">
                                            <button type="submit" class="w-full bg-slate-900 hover:bg-indigo-600 text-white font-black py-4 rounded-xl text-xs uppercase tracking-widest transition-all cursor-pointer shadow-md flex items-center justify-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                                Masukkan Keranjang Jual
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="text-[10px] text-slate-400 font-semibold text-center border-t border-slate-100 pt-3">
                                ✓ Transaksi aman terproteksi Kas Toko Mayapada Pasar Sudirman.
                            </div>
                        </div>

                    </div>
                </div>
            @endif
        @endif
    @endif

    @if($ctab === 'cart')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-xl font-black text-slate-900 tracking-tighter uppercase flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-900"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    Detail Keranjang
                </h3>

                @if(empty($cart))
                    <div class="border-2 border-slate-900 rounded-3xl p-16 text-center bg-white flex flex-col items-center justify-center min-h-[400px]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300 mb-4"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        <p class="text-slate-900 text-sm font-black uppercase tracking-tight">Keranjang Anda masih kosong.</p>
                        <p class="text-slate-400 text-xs mt-1 font-semibold">Kembali ke katalog untuk mengisinya.</p>
                        <a href="?ctab=catalog" class="mt-6 bg-slate-900 hover:bg-indigo-600 text-white font-black text-xs px-6 py-3 rounded-xl uppercase tracking-widest transition-all">
                            Eksplor Katalog
                        </a>
                    </div>
                @else
                    <div class="bg-white border-2 border-slate-900 rounded-3xl overflow-hidden divide-y divide-slate-100">
                        @foreach($cart as $idKey => $item)
                            <div class="p-5 flex items-center justify-between">
                                <div>
                                    <h4 class="font-extrabold text-slate-950 text-sm uppercase">{{ $item['name'] }}</h4>
                                    @if($item['variant_name']) <span class="text-[10px] bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded uppercase">Varian: {{ $item['variant_name'] }}</span> @endif
                                    <p class="text-xs text-slate-400 font-mono mt-1">Rp {{ number_format($item['price'], 0, ',', '.') }} × {{ $item['qty'] }} pcs</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-mono font-black text-slate-950">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                                    <a href="{{ route('cart.remove', $idKey) }}" class="text-rose-500 font-bold text-xs">Hapus</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="checkout-form-section">
                <div class="bg-white border-2 border-slate-900 p-6 rounded-3xl space-y-6 shadow-xs sticky top-6">

                    @if(auth()->check())
                        <div>
                            <h3 class="font-black text-slate-950 text-base uppercase tracking-tight">Formulir Pesanan &amp; Distribusi</h3>
                            <p class="text-slate-400 text-[11px] font-medium mt-0.5">Silakan lengkapi data untuk memproses alur pengiriman terintegrasi.</p>
                        </div>

                        <form action="{{ route('checkout.store') }}" method="POST" class="space-y-4">
                            @csrf
                            @foreach($cart as $item)
                                <input type="hidden" name="items[][product_id]" value="{{ $item['product_id'] }}">
                                <input type="hidden" name="items[][variant_id]" value="{{ $item['variant_id'] }}">
                                <input type="hidden" name="items[][qty]" value="{{ $item['qty'] }}">
                            @endforeach

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                                <input type="text" name="customer_name" value="{{ auth()->user()->name }}" required class="w-full bg-white border border-slate-400 rounded-xl p-2.5 text-xs font-semibold focus:outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Telepon/WA</label>
                                <input type="text" name="customer_phone" value="{{ auth()->user()->contact }}" required class="w-full bg-white border border-slate-400 rounded-xl p-2.5 text-xs font-mono font-bold focus:outline-none">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Alamat Lengkap Pengiriman</label>
                                <textarea name="shipping_address" required rows="2" placeholder="Masukkan alamat jalan, kelurahan, kecamatan, dan kota" class="w-full bg-white border border-slate-400 rounded-xl p-2.5 text-xs font-semibold focus:outline-none"></textarea>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Ekspedisi Pengiriman</label>
                                <select name="shipping_courier" class="w-full bg-white border border-slate-400 rounded-xl p-2.5 text-xs font-bold text-slate-800 focus:outline-none">
                                    <option value="jne">JNE Reguler (Rp15.000, Delivery 2-3 hari)</option>
                                </select>
                            </div>

                            <div class="border-t border-dashed border-slate-300 pt-4 space-y-1.5 text-xs font-medium">
                                <div class="flex justify-between text-slate-500">
                                    <span>Subtotal Pakaian</span>
                                    <span class="font-mono">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-slate-500">
                                    <span>Biaya Pengiriman (JNE)</span>
                                    <span>Rp 15.000</span>
                                </div>
                                <div class="flex justify-between font-black text-slate-950 border-t border-slate-900 pt-2 text-sm uppercase">
                                    <span>Total Invoice</span>
                                    <span class="font-mono text-base font-black">Rp {{ number_format($totalSubtotal + 15000, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <button type="submit" @if(empty($cart)) disabled @endif class="w-full bg-slate-900 hover:bg-indigo-600 text-white font-black py-3.5 rounded-xl text-xs uppercase tracking-widest transition-all cursor-pointer text-center disabled:bg-slate-300 disabled:cursor-not-allowed">
                                Buat Pesanan &amp; Bayar
                            </button>
                        </form>
                    @else
                        <div class="text-center py-6 space-y-4 animate-fade-in">
                            <div class="mx-auto w-12 h-12 bg-indigo-50 border border-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-950 text-base uppercase tracking-tight">Satu Langkah Lagi!</h3>
                                <p class="text-slate-400 text-[11px] font-medium mt-1 leading-relaxed">
                                    Silakan masuk atau mendaftarkan akun terlebih dahulu untuk melanjutkan kelayakan transaksi kargo pakaian Anda.
                                </p>
                            </div>

                            <div class="flex flex-col gap-2 pt-2">
                                <a href="{{ route('login') }}" class="w-full bg-slate-900 hover:bg-indigo-600 text-white font-black py-3.5 rounded-xl text-xs uppercase tracking-widest transition-all text-center shadow-xs">
                                    Otentikasi Masuk
                                </a>
                                <a href="{{ route('register') }}" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3 rounded-xl text-xs uppercase tracking-wider transition-all text-center border border-slate-200">
                                    Registrasi Konsumen
                                </a>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif

    @if($ctab === 'orders')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black text-slate-900 tracking-tighter uppercase flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-900"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Riwayat Transaksi Anda
                </h2>
            </div>

            @if(auth()->guest())
                <div class="bg-white border-2 border-slate-900 rounded-3xl py-16 text-center px-4">
                    <p class="text-slate-900 text-sm font-black uppercase">Belum terautentikasi</p>
                    <p class="text-slate-400 text-xs mt-1 font-semibold">Silakan login untuk melacak riwayat pembayaran nota administrasi Anda.</p>
                </div>
            @else
                @forelse($myOrders as $order)
                    <div class="bg-white border-2 border-slate-900 rounded-3xl overflow-hidden shadow-xs">
                        <div class="bg-slate-50 border-b border-slate-900 p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3 font-mono text-xs font-black text-slate-950">
                                <span>{{ $order->id }}</span>
                            </div>

                            @if($order->payment_status === 'paid')
                                        @if($order->shipping_status === 'delivered')
                                            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 font-black px-4 py-3 rounded-xl text-center text-xs uppercase tracking-wider font-sans shadow-sm">
                                                🏁 Barang Telah Diterima
                                            </div>
                                        @elseif($order->shipping_status === 'shipped')
                                            <div class="bg-amber-50 border border-amber-200 text-amber-900 font-black px-4 py-3 rounded-xl text-center text-xs uppercase tracking-wider font-sans shadow-sm">
                                                📦 Sedang Dalam Pengiriman<br>
                                                <span class="text-[10px] text-amber-700 block mt-1 mb-3">Resi: {{ $order->tracking_number ?? 'Menunggu Resi' }}</span>

                                                <form action="{{ route('customer.orders.complete', $order->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Apakah Anda yakin barang pesanan sudah sampai dengan aman?')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all cursor-pointer shadow-md flex items-center justify-center gap-1.5">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                        Barang Sudah Sampai
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="bg-blue-50 border border-blue-200 text-blue-700 font-black px-4 py-3 rounded-xl text-center text-xs uppercase tracking-wider font-sans shadow-sm">
                                                🛍️ Sedang Dikemas
                                            </div>
                                        @endif
                                    @elseif($order->payment_status === 'rejected')
                                        <div class="bg-rose-50 border border-rose-200 text-rose-700 font-black px-4 py-3 rounded-xl text-center text-xs uppercase tracking-wider font-sans">
                                            Bukti Bayar Tidak Valid
                                        </div>
                                    @else
                                        <div class="bg-indigo-50 border border-indigo-200 text-indigo-700 font-black px-4 py-3 rounded-xl text-center text-xs uppercase tracking-wider font-sans">
                                            Menunggu Validasi Administrasi
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-slate-200 rounded-3xl py-12 text-center text-slate-400 italic">
                        Belum ada riwayat transaksi belanja.
                    </div>
                @endforelse
            @endif

        </div>
    @endif
    <div class="fixed bottom-6 right-6 z-[100] group animate-fade-in print:hidden">
    <div class="absolute right-full mr-4 top-1/2 -translate-y-1/2 px-4 py-2 bg-white text-slate-800 text-xs font-black uppercase tracking-wider rounded-xl shadow-xl border border-slate-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 whitespace-nowrap">
        Halo! Ada yang bisa dibantu? 👋
        <div class="absolute -right-1 top-1/2 -translate-y-1/2 w-3 h-3 bg-white rotate-45 border-r border-t border-slate-200"></div>
    </div>

    <a href="https://wa.me/6283125567152?text=Halo%20Admin%20Mayapada,%20saya%20butuh%20bantuan%20terkait%20pesanan%20saya."
       target="_blank"
       rel="noopener noreferrer"
       class="relative flex items-center justify-center w-14 h-14 bg-[#25D366] text-white rounded-full shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">

        <span class="absolute inset-0 rounded-full bg-[#25D366] animate-ping opacity-25"></span>

        <svg class="w-8 h-8 relative z-10" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 13.76 2.46 15.42 3.26 16.89L2 22L7.27 20.73C8.71 21.54 10.31 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM17.19 16.32C16.96 16.97 15.82 17.5 15.22 17.59C14.77 17.65 14.12 17.75 11.51 16.67C8.16 15.28 5.99 11.88 5.83 11.66C5.66 11.45 4.45 9.84 4.45 8.16C4.45 6.48 5.3 5.67 5.66 5.3C5.96 5 6.44 4.86 6.91 4.86C7.06 4.86 7.2 4.87 7.32 4.88C7.68 4.91 7.86 4.93 8.1 5.51C8.39 6.22 9.11 7.97 9.2 8.16C9.29 8.35 9.4 8.59 9.29 8.81C9.17 9.03 9.08 9.13 8.94 9.31C8.79 9.49 8.65 9.61 8.5 9.8C8.34 10 8.16 10.21 8.36 10.55C8.56 10.89 9.09 11.75 9.87 12.44C10.87 13.33 11.69 13.61 12.06 13.77C12.42 13.93 12.83 13.89 13.08 13.62C13.4 13.28 13.79 12.72 14.2 12.16C14.51 11.75 14.89 11.8 15.22 11.92C15.55 12.03 17.3 12.89 17.63 13.06C17.96 13.23 18.18 13.31 18.26 13.45C18.35 13.59 18.35 14.23 18.06 14.93V16.32H17.19Z"></path>
        </svg>
    </a>
</div>
</div>
