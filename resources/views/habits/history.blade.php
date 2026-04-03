<x-layout>
    <main class="max-w-5xl mx-auto py-10 px-4 min-h-[80vh] w-full" >

        {{-- NAVBAR --}}
        <x-navbar />

        <x-title>
            Histórico
        </x-title>    


        {{-- YEAR SELECTION --}}
        <div class="my-4">
            @foreach ($availableYears as $year)
                <a href="{{ route('habits.history', $year) }}" 
                class="habit-btn habit-shadow-lg p-2 inline-block {{ $selectedYear === $year ? 'bg-habit-orange' : "bg-white" }}">
                    {{ $year }}
                </a>
            @endforeach
        </div>

        {{-- HISTORICO --}}
            @forelse($habits as $habit)
            <x-contribution :$habit :year="$selectedYear" />
            @empty

                <p>
                    Nenhum hábito para exibir histórico.
                </p>

            @endforelse
    </main>
</x-layout>