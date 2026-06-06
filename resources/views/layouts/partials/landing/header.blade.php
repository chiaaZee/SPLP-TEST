<nav x-data="navbar"
     :class="scrolled ? 'bg-white/90 backdrop-blur-md py-4 shadow-sm' : 'bg-transparent py-10'"
     class="fixed top-0 w-full z-[100] transition-all duration-500">

    <div class="max-w-[1400px] mx-auto px-6 lg:px-12 flex justify-between items-center">

        <a href="{{ route('home') }}" class="flex items-center gap-3 group cursor-pointer relative z-[101]">
            <div class="flex items-center justify-center transition-all duration-500 group-hover:scale-110">
                @include('_partials.macros', ["height" => 42])
            </div>
            <span class="text-sm font-black tracking-ultra uppercase group-hover:text-[#7367F0] transition-colors">
                SPLP <span class="text-[#7367F0]">Lumajang</span>
            </span>
        </a>

        <div class="hidden lg:flex items-center gap-12 xl:gap-16">
            <template x-for="item in navLinks" :key="item.name">
                <a :href="item.id"
                   class="text-sm font-bold uppercase tracking-widest opacity-60 hover:opacity-100 hover:text-[#7367F0] transition-all"
                   x-text="item.name">
                </a>
            </template>

            @auth
                <a href="{{ route('dashboard') }}" class="px-8 py-3 rounded-full text-xs font-black uppercase tracking-widest text-white transition-all duration-500 bg-gradient-to-r from-[#7367F0] via-[#A69EF5] to-[#7367F0] bg-[length:200%_auto] hover:bg-right hover:scale-105 hover:shadow-[0_5px_15px_rgba(115,103,240,0.4)]">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="px-8 py-3 rounded-full text-xs font-black uppercase tracking-widest text-white transition-all duration-500 bg-gradient-to-r from-[#7367F0] via-[#A69EF5] to-[#7367F0] bg-[length:200%_auto] hover:bg-right hover:scale-105 hover:shadow-[0_5px_15px_rgba(115,103,240,0.4)]">
                    Masuk
                </a>
            @endauth
        </div>

        <button @click="toggleMenu()" class="lg:hidden p-2 text-gray-600 hover:text-[#7367F0] transition-colors relative z-[101]">
            <svg x-show="!isMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            <svg x-show="isMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div x-show="isMenuOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-5"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-5"
         class="fixed inset-0 z-[90] bg-white/95 backdrop-blur-xl pt-28 px-6 lg:hidden flex flex-col h-screen">

        <div class="flex flex-col w-full">

            <template x-for="item in navLinks" :key="item.name">
                <a :href="item.id" @click="closeMenu()"
                   class="group flex justify-between items-center py-4 border-b border-gray-100 hover:border-[#7367F0]/30 transition-all">
                    <span class="text-base font-bold text-slate-700 group-hover:text-[#7367F0] transition-colors" x-text="item.name"></span>
                    <svg class="w-4 h-4 text-slate-300 group-hover:text-[#7367F0] transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </template>

            <div class="mt-8">
                @auth
                    <a href="{{ route('dashboard') }}" class="flex justify-center w-full text-white py-3.5 rounded-xl text-sm font-bold uppercase tracking-widest px-8 py-3 transition-all duration-500 hover:scale-105 hover:shadow-[0_5px_15px_rgba(115,103,240,0.4)]">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex justify-center w-full text-white py-3.5 rounded-xl text-sm font-bold uppercase tracking-widest px-8 py-3 transition-all duration-500 bg-gradient-to-r from-[#7367F0] via-[#A69EF5] to-[#7367F0] bg-[length:200%_auto] hover:bg-right hover:scale-105 hover:shadow-[0_5px_15px_rgba(115,103,240,0.4)]">
                        Masuk
                    </a>
                @endauth

                <div class="mt-8 text-center">
                    <p class="text-[10px] text-slate-400 font-medium">SPLP Kabupaten Lumajang &copy; 2026</p>
                </div>
            </div>

        </div>
    </div>
</nav>
