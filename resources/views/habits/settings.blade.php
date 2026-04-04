<x-layout>
    <main class="max-w-5xl mx-auto py-10 px-4 min-h-[80vh] w-full">

        {{-- NAVBAR --}}
        <x-navbar />

        <x-title>
            Configurar Hábitos
        </x-title>

        {{-- SEARCH BAR (hidden until JS reveals it after first load) --}}
        <div id="search-wrapper" class="relative w-full hidden">
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

        <ul class="flex flex-col gap-2 mt-2" id="habit-list"
            data-view="settings"
            data-offset="0"
            data-paginate-url="{{ route('habits.paginate') }}"
            data-toggle-url="{{ url('/dashboard/habits') }}"
            data-edit-url="{{ url('/dashboard/habits') }}">
        </ul>

        <p id="no-results" class="hidden text-gray-400 text-sm mt-2">
            Nenhum hábito encontrado.
        </p>

        <div class="flex flex-row gap-5 mt-2">
            <button id="load-more" class="hidden p-2 habit-shadow-lg bg-white habit-btn w-fit">
                Carregar mais
            </button>

            <button id="load-all-btn" class="hidden p-2 habit-shadow-lg bg-white habit-btn w-fit">
                Carregar tudo
            </button>
        </div>

    </main>
</x-layout>