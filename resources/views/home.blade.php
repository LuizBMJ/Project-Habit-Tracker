<x-layout>
    <main class="hero">
        <span class="hero-badge">✦ Gerenciador de hábitos</span>

        <h1 class="hero-title">
            Veja seus hábitos<br>
            <span>ganharem vida</span>
        </h1>

        <p class="hero-sub">
            Registre, acompanhe e construa uma rotina consistente com simplicidade.
        </p>

        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; justify-content:center;">
            <a href="{{ route('site.register') }}" class="btn btn--primary btn--lg">
                Começar agora
            </a>
            <a href="{{ route('site.login') }}" class="btn btn--ghost btn--lg">
                Já tenho conta
            </a>
        </div>
    </main>
</x-layout>