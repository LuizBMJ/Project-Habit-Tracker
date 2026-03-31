<x-layout>
    <main class="py-10" >

        <section class="bg-white max-w-[600px] mx-auto p-10 border-2 mt-4" >

            <h1 class="font-bold text-3xl">
                Faça Login
            </h1>

            <p>
                Insira seus dados para acessar.
            </p>

            <form action="/login" method="POST" class="flex flex-col">
                @csrf

                <div class="flex flex-col gap-2 mb-4">
                    <label for="email">
                        Email
                    </label>

                    <input 
                    type="email" 
                    name="email"    
                    placeholder="your@email.com"
                    class="bg-white p-2 border-2 @error('email') border-red-500 @enderror"
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
                    class="bg-white p-2 border-2 @error('password') border-red-500 @enderror"
                    >

                    <p class="text-red-500 text-sm">
                        @error('password') 
                            {{ $message }}
                        @enderror
                    </p>
                </div>
                
                

                <button 
                    type="submit"
                    class="bg-white border-2 p-2"
                >
                    Entrar
                </button>
            </form>
        </section>
    </main>
</x-layout>