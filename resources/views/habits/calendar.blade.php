<x-layout>
    <main class="flex-1 py-8 px-5">
        <div class="w-full max-w-5xl mx-auto">

            {{-- PAGE TABS NAVIGATION --}}
            <x-main-content.navbar />


            {{-- HABIT FILTER SECTION --}}
            {{-- Only show if user has at least 1 habit --}}
            <div class="my-6">
                @if ($habits->count() >= 1)
                    {{-- Header with title and sort button --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex flex-col gap-2">
                            <p class="text-[0.75rem] font-bold uppercase tracking-widest text-text-muted">
                                Filtrar por hábito
                            </p>

                            @if ($habits->count() >= 1)
                                <p class="text-[0.75rem] text-text-secondary mb-6 -mt-2">
                                    Selecione um hábito e clique em um dia para marcar ou desmarcar.
                                </p>    
                            @else
                                <p class="text-text-secondary mt-4">
                                    Você ainda não cadastrou nenhum hábito.
                                </p>
                            @endif
                        </div>
                        
                        {{-- Sort order button (cycles through creation/alphabetical/most completed) --}}
                        <button
                            type="button"
                            onclick="toggleHabitOrder()"
                            class="inline-flex hover:cursor-pointer items-center justify-center p-2 rounded-xl border border-transparent transition-all duration-200 bg-transparent text-text-secondary border-border-strong hover:bg-surface-secondary hover:text-text-primary"
                            title="Alterar ordem"
                        >
                            <x-icons.filter />
                        </button>
                    </div>
                    
                    {{-- Horizontal scrollable list of habit buttons --}}
                    <div class="relative w-full overflow-hidden">
                        <div class="flex overflow-x-auto pb-2 scrollbar-none scroll-smooth -mx-1 px-1">
                            <div class="bg-surface/40 backdrop-blur-md border border-border-glass rounded-xl md:rounded-2xl p-2 md:p-4 flex gap-2 md:gap-2.5 w-max shadow-sm">
                                
                                {{-- "All habits" button - only shows if user has 2+ habits --}}
                                @if($habits->count() >= 2)
                                    <button
                                        type="button"
                                        data-habit
                                        data-all
                                        onclick="selectHabit(null, this)"
                                        class="inline-flex hover:cursor-pointer items-center justify-center gap-2 font-semibold text-[0.875rem] md:text-sm px-4 md:px-4 py-2.5 md:py-2.5 rounded-lg md:rounded-full transition-all duration-300 whitespace-nowrap bg-transparent text-text-secondary border border-border-glass hover:bg-surface-secondary hover:text-text-primary active:scale-95 shadow-sm">
                                        Todos
                                    </button>
                                @endif

                                {{-- Individual habit buttons - data attributes store info for sorting --}}
                                @foreach ($habits as $habit)
                                    <button
                                        type="button"
                                        data-habit
                                        data-id="{{ $habit->id }}"
                                        data-name="{{ strtolower($habit->name) }}"
                                        data-created="{{ $habit->created_at }}"
                                        data-completed="{{ $habit->habitLogs->count() }}"
                                        onclick="selectHabit({{ $habit->id }}, this)"
                                        class="inline-flex hover:cursor-pointer items-center justify-center gap-2 font-semibold text-[0.875rem] md:text-sm px-4 md:px-4 py-2.5 md:py-2.5 rounded-lg md:rounded-full transition-all duration-300 whitespace-nowrap bg-transparent text-text-secondary border border-border-glass hover:bg-surface-secondary hover:text-text-primary active:scale-95 shadow-sm">
                                        {{ $habit->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- CALENDAR SECTION --}}
            {{-- FullCalendar is loaded here - JS initializes it --}}
            @if ($habits->count() >= 1)
                <div class="bg-surface/60 backdrop-blur-md border border-border-glass rounded-xl md:rounded-2xl p-4 md:p-7 shadow-[0_4px_24px_rgba(0,0,0,0.04)] w-full overflow-hidden">
                    <div id="calendar" class="w-full"></div>
                </div>
            @else
                <p class="text-text-muted text-center">Você ainda não cadastrou nenhum hábito.</p>
            @endif

        </div>
    </main>

    </x-layout>