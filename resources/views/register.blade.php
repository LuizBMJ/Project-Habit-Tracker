<x-layout>
    <main class="py-10" >

        <section class="bg-white max-w-[600px] mx-auto p-10 pb-6 mt-4 habit-shadow-lg" >

            <h1 class="font-bold text-3xl">
                Registre-se
            </h1>

            <p>
                Preencha as informações para cadastrar seus hábitos.
            </p>

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
                    class="bg-white p-2 habit-shadow @error('email') border-red-500 @enderror"
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
                    placeholder="your@email.com"
                    class="bg-white p-2 habit-shadow @error('email') border-red-500 @enderror"
                    >

                    <p class="text-red-500 text-sm">
                        @error('email') 
                            {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="flex flex-col gap-2 mb-4">

                    <label for="password">
                        Password
                    </label>

                    <input 
                    type="password" 
                    name="password"    
                    placeholder="password"
                    class="bg-white p-2 habit-shadow @error('password') border-red-500 @enderror"
                    >

                    <p class="text-red-500 text-sm">
                        @error('password') 
                            {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="flex flex-col gap-2 mb-4">

                    <label for="password_confirmation">
                        Repita sua senha
                    </label>

                    <input 
                    type="password" 
                    name="password_confirmation"    
                    placeholder="password"
                    class="bg-white p-2 habit-shadow @error('password') border-red-500 @enderror"
                    >

                    <p class="text-red-500 text-sm">
                        @error('password') 
                            {{ $message }}
                        @enderror
                    </p>
                </div>
                
                

                <button 
                    type="submit"
                    class="p-2 bg-habit-orange habit-shadow-lg habit-btn"
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