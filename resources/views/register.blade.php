<x-layout>
    <main class="py-4 px-4" >

        <section class="bg-white w-full max-w-md mx-auto px-6 py-8 sm:p-10 mx-auto pb-6 mt-4 habit-shadow-lg" >

            <h1 class="font-bold text-2xl sm:text-3xl">
                Registre-se
            </h1>

            <p>
                Preencha as informações para cadastrar seus hábitos.
            </p>

            <hr class="pb-4">

            <form action="{{ route('auth.register') }}" method="POST" class="flex flex-col">
                @csrf

                <div class="flex flex-col gap-2 mb-4">
                    <label for="nome">
                        Nome
                    </label>

                    <input 
                    type="text" 
                    name="name"    
                    placeholder="Seu nome"
                    value="{{  old('name') }}"
                    class="bg-white p-2 habit-shadow @error('name') border-red-500 w-full @enderror
                    >

                    <p class="text-red-500 text-sm">
                        @error('name') 
                            {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="flex flex-col gap-2 mb-4">
                    <label for="email">
                        Email
                    </label>

                    <input 
                    type="email" 
                    name="email"    
                    placeholder="Seu@email.com"
                    value="{{  old('email') }}"
                    class="bg-white p-2 habit-shadow @error('email') border-red-500 w-full @enderror"
                    >

                    <p class="text-red-500 text-sm">
                        @error('email') 
                            {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="flex flex-col gap-2 mb-4">
                    <label for="password">
                        Senha
                    </label>

                    <div class="relative">
                        <input 
                            id="password"
                            type="password" 
                            name="password"    
                            placeholder="Senha"
                            class="w-full bg-white p-2 pr-10 habit-shadow @error('password') border-red-500 @enderror"
                        >

                        <button 
                            type="button"
                            onclick="togglePassword('password', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer"
                        >
                            <span class="eye-open">
                                <x-icons.eye />
                            </span>

                            <span class="eye-closed hidden">
                                <x-icons.eyeclosed />
                            </span>
                        </button>
                    </div>

                    <p class="text-red-500 text-sm">
                        @error('password') 
                            {{ $message }}
                        @enderror
                    </p>
                </div>
                

                <div class="flex flex-col gap-2 mb-4">
                    <label for="password">
                        Repita sua senha
                    </label>

                    <div class="relative">
                        <input 
                            id="password_confirmation"
                            type="password" 
                            name="password_confirmation"    
                            placeholder="Senha"
                            class="w-full bg-white p-2 pr-10 habit-shadow @error('password') border-red-500 @enderror"
                        >

                        <button 
                            type="button"
                            onclick="togglePassword('password_confirmation', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer"
                        >
                            <span class="eye-open">
                                <x-icons.eye />
                            </span>

                            <span class="eye-closed hidden">
                                <x-icons.eyeclosed />
                            </span>
                        </button>
                    </div>

                    <p class="text-red-500 text-sm">
                        @error('password') 
                            {{ $message }}
                        @enderror
                    </p>
                </div>
                
                

                <button 
                    type="submit"
                    class="w-full p-2 bg-habit-orange habit-shadow-lg habit-btn"
                >
                    Cadastrar
                </button>

                <p class="text-center mt-2">
                    Ainda não tem uma conta?
                    <a href="{{ route('site.login') }}" class="underline hover:opacity-50 transtition">
                        Faça login
                    </a>
                </p>
            </form>
        </section>
    </main>
</x-layout>