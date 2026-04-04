<x-layout>
    <main class="max-w-5xl mx-auto py-10 px-4 min-h-[80vh] w-full">

        {{-- NAVBAR --}}
        <x-navbar />

        {{-- CONTENT --}}
        <div class="flex flex-col gap-4">

            <x-title>
                {{ \Carbon\Carbon::now()->locale('pt_BR')->translatedFormat('l, d \d\e F') }}
            </x-title>

            {{-- SEARCH + MARK ALL (hidden until JS reveals after first load) --}}
            <div id="search-wrapper" class="flex gap-2 w-full hidden">
                <div class="relative flex-1">
                    <input
                        type="text"
                        id="habit-search"
                        placeholder="Buscar hábito..."
                        class="w-full bg-white p-2 pl-9 habit-shadow focus:outline-none"
                        oninput="filterHabits(this.value)"
                        data-list="habit-list"
                    >
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M229.66,218.34l-50.07-50.07a88,88,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.31ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"/>
                    </svg>
                </div>

                <button id="mark-all-btn"
                    onclick="toggleAllHabits()"
                    class="p-2 habit-shadow-lg bg-white habit-btn w-fit shrink-0">
                    ✔ Marcar todos
                </button>
            </div>

            {{-- HABIT LIST --}}
            <ul class="flex flex-col gap-2 w-full" id="habit-list"
                data-view="dashboard"
                data-offset="0"
                data-paginate-url="{{ route('habits.paginate') }}"
                data-toggle-url="{{ url('/dashboard/habits') }}"
                data-edit-url="{{ url('/dashboard/habits') }}">
            </ul>

            <div class="flex flex-row gap-5 items-center flex-wrap">
                <p id="no-results" class="hidden text-gray-400 text-sm">
                    Nenhum hábito encontrado.
                </p>

                <a href="{{ route('habits.create') }}" class="p-2 habit-shadow-lg bg-habit-orange habit-btn w-fit">
                    + Adicionar
                </a>

                <button id="load-more" class="hidden p-2 habit-shadow-lg bg-white habit-btn w-fit">
                    Carregar mais
                </button>

                <button id="load-all-btn" class="hidden p-2 habit-shadow-lg bg-white habit-btn w-fit">
                    Carregar tudo
                </button>
            </div>

        </div>
    </main>

</x-layout>