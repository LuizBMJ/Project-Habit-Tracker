<x-layout>
    <main style="flex:1; display:flex; align-items:center; justify-content:center; padding:2rem 1rem;">

        <div class="auth-card">

            <h1 class="auth-heading">Criar conta</h1>
            <p class="auth-subheading">Preencha as informações para começar a registrar seus hábitos.</p>

            <form action="{{ route('auth.register') }}" method="POST">
                @csrf

                {{-- NAME --}}
                <div class="form-group">
                    <label class="form-label" for="name">Nome</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        placeholder="Seu nome"
                        value="{{ old('name') }}"
                        class="form-input @error('name') form-input--error @enderror"
                    >
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="seu@email.com"
                        value="{{ old('email') }}"
                        class="form-input @error('email') form-input--error @enderror"
                    >
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- PASSWORD --}}
                <div class="form-group">
                    <label class="form-label" for="password">Senha</label>
                    <div class="input-wrapper">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="new-password"
                            class="form-input @error('password') form-input--error @enderror"
                        >
                        <button
                            type="button"
                            class="input-toggle-btn"
                            onclick="togglePassword('password', this)"
                            tabindex="-1"
                        >
                            <span class="eye-open"><x-icons.eye /></span>
                            <span class="eye-closed hidden"><x-icons.eyeclosed /></span>
                        </button>
                    </div>
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirmar senha</label>
                    <div class="input-wrapper">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            placeholder="••••••••"
                            autocomplete="new-password"
                            class="form-input"
                        >
                        <button
                            type="button"
                            class="input-toggle-btn"
                            onclick="togglePassword('password_confirmation', this)"
                            tabindex="-1"
                        >
                            <span class="eye-open"><x-icons.eye /></span>
                            <span class="eye-closed hidden"><x-icons.eyeclosed /></span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary btn--lg" style="width:100%; margin-top:0.25rem;">
                    Criar conta
                </button>
            </form>

            <p class="auth-footer-link">
                Já tem uma conta?
                <a href="{{ route('site.login') }}">Faça login</a>
            </p>

            <div class="auth-divider">
                <hr><span>ou</span><hr>
            </div>

            <a href="{{ route('auth.google.redirect') }}" class="btn--google">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Continuar com Google
            </a>

        </div>

    </main>
</x-layout>