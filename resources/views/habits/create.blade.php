<x-layout>
    <main class="flex-1 py-8 px-4 sm:px-6">
        <div class="w-full max-w-[720px] mx-auto">

            <div class="flex flex-wrap items-center justify-center gap-4 mb-6">
                <h1 class="text-[1.35rem] font-bold text-text-primary tracking-[-0.02em]">
                    Cadastrar novo Hábito
                </h1>
            </div>

            <section class="max-w-[600px] mx-auto bg-surface border border-border rounded-2xl p-5 sm:p-7 shadow-[0_4px_24px_rgba(0,0,0,0.04)]">
                @if($limitReached)
                    <div class="mb-6 p-4 bg-error/10 border border-error/20 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-error flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216ZM128,80a8,8,0,0,1,8,8v48a8,8,0,0,1-16,0V88A8,8,0,0,1,128,80Zm0,60a12,12,0,1,1-12,12A12,12,0,0,1,128,140Z" />
                        </svg>
                        <p class="text-[0.875rem] text-error font-medium">
                            Você já atingiu o limite máximo de 10 hábitos. Delete um hábito existente para criar um novo.
                        </p>
                    </div>
                @endif

                <form action="{{ route('dashboard.habits.store') }}" method="POST">
                    @csrf

                    <div class="flex flex-col mb-4.5">
                        <label for="name" class="block text-[0.85rem] font-medium text-text-secondary mb-1.5 tracking-[0.01em]">
                            Nome do hábito
                        </label>

                        <input 
                            type="text" 
                            name="name"    
                            placeholder="Ex: Ler 10 páginas"
                            class="w-full bg-surface-input border border-border rounded-xl px-3.5 py-2.5 text-[0.95rem] text-text-primary outline-none transition-all duration-200 shadow-[inset_0_1px_2px_rgba(0,0,0,0.02)] hover:border-border-strong focus:bg-surface focus:border-brand-blue focus:ring-[3px] focus:ring-brand-blue/15 placeholder:text-text-muted @error('name') border-error focus:ring-error/15 @enderror {{ $limitReached ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $limitReached ? 'disabled' : '' }}
                        >

                        @error('name') 
                            <p class="text-[0.8rem] text-error mt-1.5 min-h-[1.1rem]">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button 
                        type="submit"
                        {{ $limitReached ? 'disabled' : '' }}
                        class="w-full hover:cursor-pointer mt-2 inline-flex items-center justify-center gap-1.5 font-medium text-[1rem] leading-none px-5 py-3 rounded-xl transition-all duration-200 whitespace-nowrap {{ $limitReached ? 'bg-text-muted cursor-not-allowed text-white' : 'bg-brand-blue text-white shadow-[0_2px_4px_rgba(0,113,227,0.2)] hover:bg-brand-blue-hover hover:shadow-[0_4px_8px_rgba(0,113,227,0.3)]' }}"
                    >
                        Cadastrar Hábito
                    </button>
                    
                </form>
            </section>

        </div>
    </main>
</x-layout>