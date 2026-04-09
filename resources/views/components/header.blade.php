<header class="site-header">
    <div class="header-inner">

        {{-- LOGO --}}
        <a href="{{ auth()->check() ? route('dashboard.habits.index') : route('site.index') }}" class="header-logo">
            <span class="header-logo-mark">HT</span>
            <span class="header-logo-name">Habitly</span>
        </a>

        {{-- DESKTOP NAV --}}
        <nav class="header-nav desktop-nav">
            <x-header-content.menu />
        </nav>

        {{-- MOBILE TRIGGER --}}
        <div class="relative sm:hidden">
            <button
                class="mobile-menu-btn"
                onclick="toggleMobileMenu()"
                aria-label="Abrir menu"
            >
                <x-icons.menu />
            </button>

            <x-header-content.menu-hamburguer />
        </div>

    </div>
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('open');
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('mobileMenu');
        if (!menu) return;
        if (!menu.contains(e.target) && !e.target.closest('.mobile-menu-btn')) {
            menu.classList.remove('open');
        }
    });
</script>