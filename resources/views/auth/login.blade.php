<x-app-layout>
    <header id="mayapada-store-header" class="bg-white border-b border-slate-200 py-5 px-6 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-sm tracking-tighter">M</div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl font-black tracking-tighter text-slate-900 uppercase font-sans">MAYAPADA<span class="text-indigo-600">.</span></h1>
                        <span class="bg-indigo-50 text-indigo-600 border border-indigo-100 font-black text-[9px] px-2 py-0.5 rounded uppercase tracking-widest">C2C Marketplace</span>
                    </div>
                    <p class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 14 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        Pasar Sudirman, Jl. Nusa Indah 1 No. 2, Darat Sekip, Pontianak Kota
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-6 text-xs text-slate-500">
                <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span><strong>Operasional:</strong> Senin-Sabtu 09:00-17:00 | Minggu 08:30-16:30 WIB</span>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-6">
        <div id="login-modal-backdrop" class="min-h-[70vh] flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-[2rem] p-10 max-w-md w-full shadow-lg space-y-6 text-xs animate-fade-in">

                <div class="text-center space-y-2">
                    <div class="mx-auto w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase font-sans pt-1">
                        Otentikasi Kredensial.
                    </h2>
                    <p class="text-slate-500 text-[11px] font-medium">
                        Masukkan email & kata sandi resmi Anda.
                    </p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label for="email" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Alamat Email</label>
                        <input id="email" type="email" name="email" required autofocus placeholder="Contoh: admin@mayapada.com" value="{{ old('email') }}" class="w-full bg-white border border-slate-400 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium text-slate-900">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-600 font-bold text-[10px]" />
                    </div>

                    <div class="space-y-1.5">
                        <label for="password" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kata Sandi</label>
                        <input id="password" type="password" name="password" required placeholder="Contoh: admin, supplier, buyer" class="w-full bg-white border border-slate-400 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-600 focus:outline-none font-medium text-slate-900">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-600 font-bold text-[10px]" />
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-indigo-600 text-white font-black py-4 px-4 rounded-xl text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md mt-2 uppercase tracking-wider">
                        Otentikasi Masuk
                    </button>
                </form>

                <div class="border-t border-slate-200 pt-5 flex flex-col gap-2 text-center text-[11px] text-slate-400 font-semibold">
                    <p>Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 font-black hover:underline cursor-pointer">Registrasi Konsumen</a></p>
                    <p>Atau ingin gabung sebagai mitra? <a href="{{ route('register', ['role' => 'supplier']) }}" class="text-indigo-600 font-black hover:underline cursor-pointer">Registrasi Partner</a></p>
                </div>

                <div class="bg-indigo-50/70 p-4 border border-indigo-100 rounded-2xl space-y-2 text-indigo-950">
                    <span class="font-extrabold flex items-center gap-1.5 text-xs text-indigo-900 uppercase tracking-widest">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>
                        Kredensial Demo:
                    </span>
                    <ul class="list-disc list-inside space-y-1.5 text-[11px] text-indigo-900/80 leading-relaxed font-medium pl-1">
                        <li><strong>Admin Toko:</strong> admin@mayapada.com / admin</li>
                        <li><strong>Supplier Kencana:</strong> kencana@supplier.com / supplier</li>
                        <li><strong>Konsumen Pembeli:</strong> budi@gmail.com / buyer</li>
                    </ul>
                </div>

            </div>
        </div>
    </main>
</x-app-layout>
