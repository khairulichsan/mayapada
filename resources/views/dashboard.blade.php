<x-app-layout>
    <header id="mayapada-store-header" class="bg-white border-b border-slate-200 py-5 px-6 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-sm tracking-tighter">
                    M
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl font-black tracking-tighter text-slate-900 uppercase">
                            MAYAPADA<span class="text-indigo-600">.</span>
                        </h1>
                        <span class="bg-indigo-50 text-indigo-600 border border-indigo-100 font-black text-[9px] px-2 py-0.5 rounded uppercase tracking-widest">C2C Marketplace</span>
                    </div>
                    <p class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                        📍 Pasar Sudirman, Jl. Nusa Indah 1 No. 2, Darat Sekip, Pontianak Kota
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-6 text-xs text-slate-500">
                <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5">
                    <span>🕒 <strong>Operasional:</strong> Senin-Sabtu 09:00-17:00 | Minggu 08:30-16:30 WIB</span>
                </div>

                <div class="flex items-center gap-3 pl-2 border-l border-slate-200">
                    <span class="text-slate-600">
                        Log sebagai: <strong class="text-slate-900 font-bold">{{ auth()->user()->name }}</strong>
                        <span class="text-[10px] uppercase bg-slate-200 px-1.5 py-0.5 rounded font-bold">({{ auth()->user()->role }})</span>
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="font-bold text-red-500 hover:text-red-700 transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main id="mayapada-viewport" class="max-w-7xl w-full mx-auto px-4 py-6 flex-grow">
        @if(auth()->user()->role === 'customer')
            @include('panels.customer')
        @elseif(auth()->user()->role === 'supplier')
            @include('panels.supplier')
        @elseif(auth()->user()->role === 'admin')
            @include('panels.admin')
        @endif
    </main>

    <footer id="mayapada-system-footer" class="bg-white border-t border-slate-200 mt-12 py-8 px-6 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="space-y-1 text-center sm:text-left">
                <p class="font-extrabold text-slate-700 tracking-wider">SISTEM PENJUALAN C2C TOKO MAYAPADA PONTIANAK</p>
                <p class="text-[11px] font-medium text-slate-400">Coded for Willy Ananda Fauzan — Skripsi Sarjana Komputer STMIK Pontianak.</p>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[10px] bg-indigo-50 border border-indigo-150 text-indigo-700 font-mono font-bold px-2 py-0.5 rounded tracking-wide">
                    PRODUCTION_MODE
                </span>
            </div>
        </div>
    </footer>
</x-app-layout>
