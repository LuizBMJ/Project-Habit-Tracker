@auth
    <div style="display:flex; align-items:center; gap:0.5rem;">
        <span style="font-size:0.84rem; color:var(--color-text-secondary);">
            Olá, <strong style="color:var(--color-text-primary); font-weight:600;">{{ auth()->user()->name }}</strong>
        </span>
        <form action="{{ route('dashboard.logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn--ghost btn--sm">Sair</button>
        </form>
    </div>
@endauth

@guest
    <div style="display:flex; align-items:center; gap:0.5rem;">
        <a href="{{ route('site.register') }}" class="btn btn--ghost btn--sm">Registre-se</a>
        <a href="{{ route('site.login') }}" class="btn btn--primary btn--sm">Login</a>
    </div>
@endguest

<a
    class="btn btn--icon btn--ghost"
    href="https://github.com/LuizBMJ/Projeto-Gerenciador-de-Habitos"
    target="_blank"
    aria-label="GitHub"
    style="color:var(--color-text-secondary);"
>
    <x-icons.github />
</a>