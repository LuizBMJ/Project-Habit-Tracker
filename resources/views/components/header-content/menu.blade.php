<!-- AÇÕES -->
<div class="hidden sm:flex items-center gap-2 min-w-0">

    @auth
        <form
            class="flex items-center gap-2 min-w-0"
            action="{{ route('auth.logout') }}"
            method="POST"
        >
            @csrf

            <p class="text-sm truncate max-w-[120px] sm:max-w-none">
                Bem vindo(a), <strong>{{ auth()->user()->name }}</strong>
            </p>

            <button
                type="submit"
                class="habit-btn habit-shadow-lg px-2 sm:px-3 py-1 bg-white shrink-0"
            >
                Sair
            </button>
        </form>
    @endauth

    @guest
        <div class="flex items-center gap-2 min-w-0">
            <a 
                href="{{ route('site.register') }}"
                class="px-2 sm:px-3 py-1 habit-shadow-lg habit-btn bg-white truncate max-w-[90px] sm:max-w-none"
            >
                <span class="hidden sm:inline">
                    Registre-se
                </span>

            </a>

            <a 
                href="{{ route('site.login') }}"
                class="px-2 sm:px-3 py-1 habit-shadow-lg habit-btn bg-habit-orange shrink-0"
            >
                Login
            </a>
        </div>
    @endguest

    <!-- GITHUB -->
    <a 
        class="habit-btn habit-shadow-lg p-2 flex items-center justify-center shrink-0"
        href="https://github.com/LuizBMJ/Projeto-Gerenciador-de-Habitos" 
        target="_blank"
    >
        <x-icons.github />
    </a>

</div>