<x-layout>
    <main class="max-w-5xl mx-auto py-10 min-h-[calc(100vh-180px)] px-4" >

        {{-- NAVBAR --}}
        <x-navbar />


        {{-- YEAR SELECTION --}}
        <div class="my-4">
            @foreach ($availableYears as $year)
                <a href="{{ route('habits.history', $year) }}" 
                class="habit-btn habit-shadow-lg p-2 inline-block {{ $selectedYear === $year ? 'bg-habit-orange' : "bg-white"}}">
                    {{ $year }}
                </a>
            @endforeach
        </div>

        {{-- HISTORICO --}}
            @forelse($habits as $habit)
            <x-contribution :$habit :year="$selectedYear" />
            @empty
            <div>
                <p class="text-black">
                Nenhum hábito para exibir histórico.
                </p>
                <a href="{{ route('habits.create') }}" class="underline ">
                Crie um novo hábito
                </a>
            </div>
            @endforelse
    </main>
</x-layout>