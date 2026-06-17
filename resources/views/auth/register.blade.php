@php
    // Logika sederhana untuk beralih mode visual berdasarkan URL parameter (?role=supplier)
    $isSupplier = request('role') === 'supplier';
@endphp

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

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-8">
        <div id="register-modal-backdrop" class="flex items-center justify-center p-4">
            <div class="bg-white border border-slate-200 rounded-[2rem] p-10 max-w-md w-full shadow-lg space-y-6 text-xs animate-fade-in">

                <div class="text-center space-y-2">
                    <div class="mx-auto w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase font-sans pt-1">
                        @if($isSupplier) Registrasi Supplier Partner. @else Registrasi Konsumen. @endif
                    </h2>
                    <p class="text-slate-500 text-[11px] font-medium leading-relaxed">
                        @if($isSupplier) Daftarkan brand garmen pakaian Anda. @else Buat akun pelanggan untuk berbelanja pakaian. @endif
                    </p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    @if($isSupplier)
                        <input type="hidden" name="role" value="supplier">
                    @endif

                    <div class="space-y-1.5">
                        <label for="name" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Lengkap</label>
                        <input id="name" type="text" name="name" required placeholder="Nama lengkap Anda" value="{{ old('name') }}" class="w-full bg-white border border-slate-400 rounded-xl px-4 py-3 text-xs font-medium text-slate-900 focus:ring-2 focus:ring-indigo-600 focus:outline-none">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-rose-600 font-bold text-[10px]" />
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Username Unik / Alamat Email</label>
                        <input id="email" type="email" name="email" required placeholder="Gunakan alamat email resmi" value="{{ old('email') }}" class="w-full bg-white border border-slate-400 rounded-xl px-4 py-3 text-xs font-medium text-slate-900 focus:ring-2 focus:ring-indigo-600 focus:outline-none">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-600 font-bold text-[10px]" />
                    </div>

                    <div class="space-y-1.5">
                        <label for="password" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kata Sandi</label>
                        <input id="password" type="password" name="password" required placeholder="Min 8 karakter demi keamanan" class="w-full bg-white border border-slate-400 rounded-xl px-4 py-3 text-xs font-medium text-slate-900 focus:ring-2 focus:ring-indigo-600 focus:outline-none">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-600 font-bold text-[10px]" />
                    </div>

                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Ketik ulang kata sandi Anda" class="w-full bg-white border border-slate-400 rounded-xl px-4 py-3 text-xs font-medium text-slate-900 focus:ring-2 focus:ring-indigo-600 focus:outline-none">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-rose-600 font-bold text-[10px]" />
                    </div>

                    <div class="space-y-1.5">
                        <label for="contact" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nomor Telepon/WA</label>
                        <input id="contact" type="text" name="contact" placeholder="Contoh: +62 899-7000-8000" value="{{ old('contact') }}" class="w-full bg-white border border-slate-400 rounded-xl px-4 py-3 text-xs font-mono font-bold focus:ring-2 focus:ring-indigo-600 focus:outline-none text-slate-900">
                    </div>

                    @if($isSupplier)
                        <div class="space-y-1.5 pt-1 animate-fade-in">
                            <label for="brand_name" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Brand Pemasok</label>
                            <input id="brand_name" type="text" name="brand_name" required placeholder="Contoh: Kencana Ungu, Gajah Putih" value="{{ old('brand_name') }}" class="w-full bg-white border border-slate-400 rounded-xl px-4 py-3 text-xs font-semibold focus:ring-2 focus:ring-indigo-600 focus:outline-none text-slate-900">
                        </div>
                    @endif

                    <button type="submit" class="w-full bg-slate-900 hover:bg-indigo-600 text-white font-black py-4 px-4 rounded-xl text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md mt-2 uppercase tracking-wider disabled:bg-slate-200">
                        @if($isSupplier) Daftarkan Akun Partner @else Daftarkan Akun Partner @endif
                    </button>
                </form>

                <div class="border-t border-slate-200 pt-5 flex flex-col gap-2 text-center text-[11px] text-slate-400 font-semibold">
                    <p>Sudah memiliki akun terdaftar? <a href="{{ route('login') }}" class="text-indigo-600 font-black hover:underline cursor-pointer">Kembali ke Halaman Login</a></p>
                </div>

            </div>
        </div>
    </main>
</x-app-layout>
