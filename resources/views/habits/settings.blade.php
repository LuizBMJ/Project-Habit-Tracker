<x-layout>
    <main class="flex-1 py-8 px-5">
        <div class="w-full max-w-5xl mx-auto">

            {{-- NAVBAR --}}
            <x-main-content.navbar />
            <div class="my-6">
                <div id="search-wrapper" class="hidden flex flex-col gap-2.5 mb-4">
                    <div class="flex items-center gap-2 w-full">
                        <div id="search-input-wrapper" class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted pointer-events-none"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M229.66,218.34l-50.07-50.07a88,88,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.31ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z" />
                            </svg>
                            <input type="text" id="habit-search" placeholder="Buscar hábito..."
                                class="w-full bg-surface/60 backdrop-blur-md border border-border-glass rounded-xl px-4 py-2.5 pl-9 text-[0.95rem] text-text-primary outline-none transition-all duration-300 shadow-sm hover:bg-surface/80 hover:border-border focus:bg-surface focus:border-brand-blue focus:ring-[3px] focus:ring-brand-blue/20 placeholder:text-text-muted"
                                oninput="filterHabits(this.value)" data-list="habit-list">
                        </div>

                        <button id="delete-selected-btn" onclick="deleteSelectedHabits()"
                            class="hidden inline-flex items-center justify-center gap-1.5 font-medium text-sm px-3.5 py-2.5 rounded-xl transition-all duration-200 whitespace-nowrap bg-error text-white shadow-sm hover:opacity-80 active:scale-95 outline-none">
                            <x-icons.trash class="w-4 h-4" />
                            <span class="hidden md:inline">Deletar selecionados</span>
                        </button>
                    </div>

                    {{-- SELECT ALL --}}
                    <label id="select-all-wrapper"
                        class="flex items-center gap-2 cursor-pointer text-[0.84rem] text-text-secondary select-none w-max group">
                        <input type="checkbox" id="select-all-checkbox"
                            class="w-4 h-4 cursor-pointer accent-brand-blue transition-transform group-hover:scale-110">
                        Selecionar todos
                    </label>
                </div>

                <ul class="flex flex-col gap-2 w-full list-none p-0 m-0" id="habit-list" data-view="settings"
                    data-offset="0" data-paginate-url="{{ route('dashboard.habits.paginate') }}"
                    data-toggle-url="{{ url('/dashboard/habits') }}" data-edit-url="{{ url('/dashboard/habits') }}"
                    data-delete-url="{{ url('/dashboard/habits') }}">
                </ul>

                <div class="flex items-center justify-center gap-3 flex-wrap mt-6">
                    <p id="no-results" class="hidden text-[0.875rem] text-text-muted">
                        Nenhum hábito encontrado.
                    </p>
                </div>       
            </div>
            

        </div>
    </main>
</x-layout>