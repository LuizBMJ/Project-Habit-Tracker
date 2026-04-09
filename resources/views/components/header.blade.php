<header class="flex flex-col items-center justify-center">
    <div class="bg-white border-b-2 mb-2 w-full">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-center gap-3 p-4 sm:justify-between">

            <!-- LOGO -->
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ auth()->check() ? route('dashboard.habits.index') : route('site.index') }}"
                    class="habit-btn habit-shadow-lg px-2 py-1 bg-habit-orange shrink-0"
                >
                    HT
                </a>

                <p class="text-sm sm:text-base truncate">
                    Habitly
                </p>
            </div>

            <x-header-content.menu class="hidden sm:block"/>

        </div>
    </div>

    <x-header-content.menu-hamburguer class="block sm:hidden"/>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('opacity-0');
            menu.classList.toggle('scale-95');
            menu.classList.toggle('-translate-y-2');
            menu.classList.toggle('pointer-events-none');
        }
    </script>
</header>