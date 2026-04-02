<x-layout>
    <main class="py-10 min-h-[calc(100vh-140px)] px-4" >

        <x-navbar />

        @session('success')
            <div class="flex">
                <p class="bg-green-100 border-2 border-green-400 text-green-700 p-3 mb-4">
                    {{ session('success') }}
                </p>
            </div>
        @endsession

        <p>
            Bem vindo(a), {{ auth()->user()->name }}
        </p>

        <div>
            <h2 class="text-lg mt-8 mb-2">
                {{ date('d/m/Y') }}
            </h2>

            <ul class="flex flex-col gap-2">

                @forelse ($habits as $item)
                    <li class="habit-shadow-lg p-2 bg-[#FFDAAC]">
                        <form 
                        action="{{ route('habits.toggle', $item->id) }}" method="POST" 
                        class="flex gap-2 items-center"
                        id="form-{{ $item->id }}"
                        >

                            @csrf

                            <input 
                            type="checkbox" 
                            class="w-5 h-5"
                            {{ $item->wasCompletedToday() ? 'checked' : '' }}
                            onchange="document.getElementById('form-{{ $item->id }}').submit()"
                            >
                            <p class="font-bold text-lg">
                                {{ $item->name }}
                            </p>
                        </form>
                    </li>
                @empty
                    <p>
                        Você ainda não cadastrou nenhum hábito.
                    </p>

                    <a href="{{ route('habits.create') }}" class="bg-white p-2 border-2">
                        Cadastre um novo hábito agora!
                    </a>
                @endforelse
            </ul>
        </div>
    </main>
</x-layout>