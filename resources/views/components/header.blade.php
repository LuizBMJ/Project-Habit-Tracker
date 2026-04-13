<header class="bg-surface/60 backdrop-blur-xl border-b border-border-glass sticky top-0 z-[100] shadow-sm transition-all duration-300">
    <div class="max-w-[960px] mx-auto px-5 h-16 flex items-center justify-between gap-4">

        {{-- LOGO --}}
        <a href="{{ auth()->check() ? route('dashboard.habits.index') : route('site.index') }}" class="flex items-center text-none shrink-0 group transition-transform active:scale-95">
            <img src="{{ asset('images/Habity-Logo.png') }}" alt="Habitly Logo" class="w-10 h-10 object-contain group-hover:rotate-6 transition-transform">
            <span class="text-[1rem] font-bold text-text-primary tracking-tight ml-1">Habitly</span>
        </a>

        {{-- DESKTOP NAV --}}
        <nav class="hidden sm:flex items-center gap-2">
            <x-header-content.menu />
        </nav>

        {{-- MOBILE TRIGGER --}}
        <div class="relative sm:hidden">
            <button
                class="flex items-center justify-center w-10 h-10 rounded-xl border border-border-glass bg-surface/40 backdrop-blur-md cursor-pointer text-text-secondary transition-all duration-200 hover:bg-surface-secondary hover:scale-105 active:scale-95 shadow-sm shrink-0 outline-none"
                onclick="toggleMobileMenu()"
                aria-label="Abrir menu"
            >
                <x-icons.menu class="w-5 h-5" />
            </button>

            <x-header-content.menu-hamburguer />
        </div>

    </div>
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('open');
        if (menu.classList.contains('open')) {
            menu.classList.add('opacity-100', 'translate-y-0', 'pointer-events-auto');
            menu.classList.remove('opacity-0', '-translate-y-2', 'pointer-events-none', 'scale-95');
        } else {
            menu.classList.remove('opacity-100', 'translate-y-0', 'pointer-events-auto');
            menu.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none', 'scale-95');
        }
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('mobileMenu');
        if (!menu) return;
        if (!menu.contains(e.target) && !e.target.closest('button[aria-label="Abrir menu"]')) {
            menu.classList.remove('open', 'opacity-100', 'translate-y-0', 'pointer-events-auto');
            menu.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none', 'scale-95');
        }
    });
</script>