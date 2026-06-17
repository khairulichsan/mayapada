<x-app-layout>
    <header id="mayapada-store-header" class="bg-white border-b border-slate-200 py-5 px-6 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">

            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-sm tracking-tighter">M</div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl font-black tracking-tighter text-slate-900 uppercase">MAYAPADA<span class="text-indigo-600">.</span></h1>
                        <span class="bg-indigo-50 text-indigo-600 border border-indigo-100 font-black text-[9px] px-2 py-0.5 rounded uppercase tracking-widest">C2C Marketplace</span>
                    </div>
                    <p class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 14 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        Pasar Sudirman, Jl. Nusa Indah 1 No. 2, Darat Sekip, Pontianak Kota
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-5 text-xs text-slate-500">
                <div class="hidden sm:flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span><strong>Operasional:</strong> Senin-Sabtu 09:00-17:00 | Minggu 08:30-16:30 WIB</span>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="font-extrabold text-slate-600 hover:text-indigo-600 transition-colors px-2 py-1">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="bg-slate-900 hover:bg-indigo-600 text-white font-black px-4 py-2.5 rounded-xl transition-all uppercase tracking-wider text-[11px] shadow-sm">
                        Daftar Akun
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main id="mayapada-viewport" class="flex-grow max-w-7xl w-full mx-auto px-4 py-6 animate-fade-in">
        @include('panels.customer')
    </main>

    <footer id="mayapada-system-footer" class="bg-white border-t border-slate-200 mt-12 py-8 px-6 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="space-y-1 text-center sm:text-left">
                <p class="font-extrabold text-slate-700 tracking-wider uppercase">SISTEM PENJUALAN C2C TOKO MAYAPADA PONTIANAK</p>
                <p class="text-[11px] font-medium text-slate-400">Coded for Willy Ananda Fauzan — Skripsi Sarjana Komputer STMIK Pontianak.</p>
            </div>
            <span class="text-[10px] bg-slate-100 border border-slate-200 text-slate-600 font-mono font-bold px-2 py-0.5 rounded tracking-wide">
                GUEST_VIEW_MODE
            </span>
        </div>
    </footer>
</x-app-layout>
