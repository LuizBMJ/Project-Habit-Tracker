<header class="bg-white border-b-2" >
    
    <div class="max-w-7xl mx-auto flex items-center justify-between p-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('habits.index') }}" class="habit-btn habit-shadow-lg px-2 py-1 bg-habit-orange">
                HT
            </a>
        
            <p>
                Habit Tracker
            </p>
        </div>
        <div class="flex gap-2 items-center">
            <!-- GITHUB -->
            @auth
            <form
            class="flex items-center gap-2"
            action="{{ route('auth.logout') }}"
            method="POST">
                @csrf
                <p>
                    Bem vindo(a), <strong>{{ auth()->user()->name }}</strong>
                </p>
                <button
                    type="submit"
                    class="habit-btn habit-shadow-lg p-2 bg-white"
                >
                    Sair
                </button>
            </form>
        @endauth
        @guest
            <div class="flex gap-2">
                <a href="{{ route('site.register') }}"
                class="p-2 habit-shadow-lg habit-btn bg-white">
                    Registre-se
                </a>
                <a href="{{ route('site.login') }}"
                class="p-2 habit-shadow-lg habit-btn bg-habit-orange">
                    Login
                </a>
            </div>
        @endguest

        <a class="habit-btn habit-shadow-lg" href="https://github.com/LuizBMJ" target="_blank">
            <x-icons.github />
        </a>
        </div>
    </div>

    
</header>