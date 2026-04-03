<x-layout>
    <main class="max-w-5xl mx-auto py-10 px-4 min-h-[80vh] w-full">

        {{-- NAVBAR --}}
        <x-navbar />

        <x-title>
            Calendário
        </x-title>

        
        @if ($habits->count() >= 1)
            <p class="text-sm text-gray-500 mb-6 -mt-2">
                Selecione um hábito e clique em um dia para marcar ou desmarcar.
            </p>    
            @else
                <p class="mt-4">
                    Você ainda não cadastrou nenhum hábito.
                </p>
        @endif

        {{-- HABIT SELECTOR --}}
        <div class="mb-6">
            @if ($habits->count() >= 1)
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Filtrar por hábito</p>
            @endif
            
            <div class="flex flex-wrap gap-3">

                {{-- BOTÃO TODOS --}}
                @if($habits->count() >= 2)
                    <button
                        type="button"
                        data-habit
                        data-all
                        onclick="selectHabit(null, this)"
                        class="habit-btn habit-shadow px-4 py-2 bg-gray-100 hover:bg-habit-orange hover:text-white transition-colors"
                    >
                        Todos
                    </button>
                @endif

                {{-- BOTÕES DOS HÁBITOS --}}
                @foreach ($habits as $habit)
                    <button
                        type="button"
                        data-habit
                        data-id="{{ $habit->id }}"
                        onclick="selectHabit({{ $habit->id }}, this)"
                        class="habit-btn habit-shadow px-4 py-2 bg-gray-100 hover:bg-habit-orange hover:text-white transition-colors"
                    >
                        {{ $habit->name }}
                    </button>
                @endforeach

            </div>
        </div>

        {{-- CALENDÁRIO --}}
        <div class="bg-white habit-shadow-lg p-2 sm:p-6 w-full overflow-hidden">
            <div id="calendar" class="w-full"></div>
        </div>

    </main>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    @endpush

</x-layout>