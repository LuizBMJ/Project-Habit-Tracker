<x-layout>
    <main class="max-w-5xl mx-auto py-10 px-4 min-h-[80vh] w-full">

        {{-- NAVBAR --}}
        <x-navbar />

        <x-title>
            Calendário
        </x-title>    

        {{-- SELEÇÃO DE HÁBITO --}}
        <div class="my-4 flex flex-wrap gap-2">

            {{-- BOTÃO TODOS --}}
            @if($habits->count() >= 2)
                <button 
                    type="button"
                    data-habit
                    data-all
                    onclick="selectHabit(null, this)"
                    class="habit-btn habit-shadow-lg px-4 py-2 hover:bg-habit-orange bg-gray-100 transition"
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
                    class="habit-btn habit-shadow-lg px-4 py-2 bg-gray-100 hover:bg-habit-orange transition"
                >
                    {{ $habit->name }}
                </button>
            @endforeach

        </div>

        {{-- CALENDÁRIO --}}
        <div class="bg-white rounded-xl shadow p-4">
            <div id="calendar"></div>
        </div>

    </main>

    {{-- FullCalendar --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

</x-layout>