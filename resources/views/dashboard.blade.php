<x-layout>
    <main style="flex:1; padding:2rem 1.25rem;">
        <div class="page-container--wide">

            {{-- NAVBAR TABS --}}
            <x-main-content.navbar />

            {{-- HEADER ROW --}}
            <div class="dash-header">
                <div>
                    <x-main-content.title>
                        {{ \Carbon\Carbon::now()->locale('pt_BR')->translatedFormat('l, d \d\e F') }}
                    </x-main-content.title>
                </div>

                <a href="{{ route('dashboard.habits.create') }}" class="btn btn--accent">
                    + Adicionar
                </a>
            </div>

            {{-- SEARCH + SELECT ALL --}}
            <div id="search-wrapper" class="hidden" style="display:flex; flex-direction:column; gap:0.6rem; margin-bottom:1rem;">
                <div class="search-bar">
                    <svg class="search-bar-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M229.66,218.34l-50.07-50.07a88,88,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.31ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"/>
                    </svg>
                    <input
                        type="text"
                        id="habit-search"
                        placeholder="Buscar hábito..."
                        class="form-input"
                        oninput="filterHabits(this.value)"
                        data-list="habit-list"
                    >
                </div>

                <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.84rem; color:var(--color-text-secondary); user-select:none; width:fit-content;">
                    <input type="checkbox" id="select-all-checkbox" style="width:15px; height:15px; cursor:pointer; accent-color:var(--color-brand-blue);">
                    Marcar todos
                </label>
            </div>

            {{-- HABIT LIST --}}
            <ul
                class="flex flex-col gap-2 w-full"
                id="habit-list"
                data-view="dashboard"
                data-offset="0"
                data-paginate-url="{{ route('dashboard.habits.paginate') }}"
                data-toggle-url="{{ url('/dashboard/habits') }}"
                data-edit-url="{{ url('/dashboard/habits') }}"
                data-delete-url="{{ url('/dashboard/habits') }}"
                style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:0.5rem;"
            ></ul>

            {{-- ACTIONS --}}
            <div style="display:flex; align-items:center; justify-content:center; gap:0.75rem; flex-wrap:wrap; margin-top:1.5rem;">
                <p id="no-results" class="hidden" style="font-size:0.875rem; color:var(--color-text-muted);">
                    Nenhum hábito encontrado.
                </p>

                <button id="load-more" class="btn btn--ghost hidden">
                    Carregar mais
                </button>

                <button id="load-all-btn" class="btn btn--ghost hidden">
                    Carregar tudo
                </button>
            </div>

        </div>
    </main>
</x-layout>