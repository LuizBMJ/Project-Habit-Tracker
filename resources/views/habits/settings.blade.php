<x-layout>
    <main class="max-w-5xl mx-auto py-10 px-4 min-h-[80vh] w-full" >

        {{-- NAVBAR --}}
        <x-navbar />

        <x-title>
            Configurar Hábitos
        </x-title>   

        <ul class="flex flex-col gap-2 mt-2">
            @forelse ($habits as $item)
                <li class="flex gap-2 items-center justify-between w-full">

                    {{-- ITENS --}}
                    <div class="habit-shadow-lg p-2 bg-[#FFDAAC] w-full">                       
                        <p class="font-bold text-lg">
                            {{ $item->name }}
                        </p>
                    </div>

                        {{-- EDIT --}}
                        <a href="{{ route('habits.edit', $item) }}" class="bg-white habit-shadow-lg text-white p-2 hover:opacity-50">
                            <x-icons.update />
                        </a>

                        {{-- DELETE --}}
                        <form action="{{ route('habits.destroy', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="bg-red-500 habit-shadow-lg text-white p-2 hover:opacity-50 cursor-pointer">
                                <x-icons.trash />
                            </button>
                        </form>
                </li>
            @empty
                <p>
                    Você ainda não cadastrou nenhum hábito.
                </p>
            @endforelse
        </ul>
    </main>
</x-layout>


    