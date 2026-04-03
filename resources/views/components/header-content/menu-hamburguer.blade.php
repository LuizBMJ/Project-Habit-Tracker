<!-- CONTAINER HAMBURGUER -->
<div class="relative sm:hidden">

    <button 
        onclick="toggleMobileMenu()"
        class="habit-btn rounded habit-shadow p-2 bg-white"
    >
        <x-icons.menu />
    </button>

    <!-- MENU MOBILE -->
    <div 
        id="mobileMenu"
        class="absolute left-1/2 -translate-x-1/2 mt-3 bg-white rounded-lg shadow-lg p-3 flex flex-col gap-2 w-40 z-50
            opacity-0 scale-95 -translate-y-2 pointer-events-none
            transition-all duration-200 ease-out"
    >

        <!-- TRIÂNGULO -->
        <div class="absolute -top-2 left-1/2 -translate-x-1/2 
                    w-0 h-0 
                    border-l-8 border-l-transparent
                    border-r-8 border-r-transparent
                    border-b-8 border-b-white">
        </div>

        @guest
            <a 
                href="{{ route('site.login') }}"
                class="habit-btn habit-shadow px-3 py-1 bg-habit-orange text-center"
            >
                Login
            </a>

            <a 
                href="{{ route('site.register') }}"
                class="habit-btn habit-shadow px-3 py-1 bg-white text-center"
            >
                Registrar
            </a>
        @endguest

        @auth
            <form action="{{ route('auth.logout') }}" method="POST">
                @csrf

                <button 
                    type="submit"
                    class="habit-btn habit-shadow px-3 py-1 bg-white w-full"
                >
                    Sair
                </button>
            </form>
        @endauth

    </div>

</div>