<footer class="relative bg-[#05080A] pt-16 lg:pt-24 pb-12 border-t border-white/10 overflow-hidden">

    <div class="absolute inset-0 w-full h-full pointer-events-none opacity-20">
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-[#7367F0]/10 rounded-full blur-[100px] pointer-events-none"></div>
    </div>

    <div class="max-w-[1200px] mx-auto px-6 lg:px-12 relative z-10">

        <div class="flex flex-col lg:flex-row justify-between items-start gap-12 lg:gap-16 mb-16 lg:mb-24">

            <div class="max-w-xs w-full">
                <div class="flex items-center gap-3 mb-6 lg:mb-8">
                    @include('_partials.macros', ["height" => 42])
                    <span class="text-sm font-black tracking-widest uppercase text-white">SPLP KAB. LUMAJANG</span>
                </div>
                <p class="text-sm text-slate-400 font-medium leading-relaxed mb-6">
                    Infrastruktur pendukung SPBE Kabupaten Lumajang untuk layanan pemerintahan yang terpadu dan efisien.
                </p>
                <div class="text-xs text-slate-500 font-mono">
                    Dikelola oleh Diskominfo Kab. Lumajang
                </div>
            </div>

            <div class="grid grid-cols-2 lg:flex lg:flex-row gap-x-8 gap-y-12 lg:gap-24 w-full lg:w-auto">

                <div class="col-span-1">
                    <h5 class="text-xs font-black text-white uppercase tracking-widest mb-6 opacity-40">Menu</h5>
                    <ul class="space-y-4">
                        <li><a href="#beranda" class="text-sm font-bold text-slate-400 uppercase tracking-wide hover:text-[#7367F0] transition-colors">Beranda</a></li>
                        <li><a href="#tentang" class="text-sm font-bold text-slate-400 uppercase tracking-wide hover:text-[#7367F0] transition-colors">Tentang</a></li>
                        <li><a href="#fitur" class="text-sm font-bold text-slate-400 uppercase tracking-wide hover:text-[#7367F0] transition-colors">Fitur</a></li>
                        <li><a href="#kontak" class="text-sm font-bold text-slate-400 uppercase tracking-wide hover:text-[#7367F0] transition-colors">Kontak</a></li>
                    </ul>
                </div>

                <div class="col-span-1">
                    <h5 class="text-xs font-black text-white uppercase tracking-widest mb-6 opacity-40">Ikuti Kami</h5>
                    <ul class="space-y-5">
                        <li>
                            <a href="{{ $footer->facebook ?? 'https://www.facebook.com/lumajangkab/' }}" class="flex items-center gap-3 text-slate-400 hover:text-[#7367F0] transition-colors group">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:stroke-[#7367F0]"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                                <span class="text-sm font-bold uppercase tracking-wide">Facebook</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $footer->instagram ?? 'https://www.instagram.com/lumajang_kab/' }}" class="flex items-center gap-3 text-slate-400 hover:text-[#7367F0] transition-colors group">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:stroke-[#7367F0]"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                                <span class="text-sm font-bold uppercase tracking-wide">Instagram</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $footer->twitter ?? 'https://x.com/lumajang_kab' }}" class="flex items-center gap-3 text-slate-400 hover:text-[#7367F0] transition-colors group">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:stroke-[#7367F0]"><path d="M4 4l11.733 16h4.267l-11.733 -16z" /><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772" /></svg>
                                <span class="text-sm font-bold uppercase tracking-wide">Twitter</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $footer->youtube ?? 'https://www.youtube.com/@KabupatenLumajang/featured' }}" class="flex items-center gap-3 text-slate-400 hover:text-[#7367F0] transition-colors group">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:stroke-[#7367F0]"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>
                                <span class="text-sm font-bold uppercase tracking-wide">YouTube</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] font-bold uppercase tracking-widest text-slate-600 text-center md:text-left">
            <div>&copy; 2026 Dinas Komunikasi dan Informatika Kabupaten Lumajang</div>
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>SPLP {{ $footer->app_version ?? 'v1.0' }}</span>
            </div>
        </div>

    </div>
</footer>
