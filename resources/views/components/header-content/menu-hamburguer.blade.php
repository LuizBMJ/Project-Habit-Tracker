<div id="mobileMenu" class="mobile-drawer">

    @guest
        <a href="{{ route('site.login') }}" class="btn btn--primary" style="width:100%; justify-content:center;">
            Login
        </a>
        <a href="{{ route('site.register') }}" style="display:block; padding:0.5rem 0.75rem; border-radius:8px; font-size:0.88rem; font-weight:500; color:var(--color-text-primary); text-decoration:none; transition:background 0.12s;">
            Registrar
        </a>
    @endguest

    @auth
        <span style="display:block; padding:0.5rem 0.75rem; font-size:0.82rem; color:var(--color-text-muted);">
            {{ auth()->user()->name }}
        </span>
        <hr style="border:none; border-top:1px solid var(--color-border); margin:0.2rem 0;">
        <form action="{{ route('dashboard.logout') }}" method="POST">
            @csrf
            <button type="submit" style="width:100%;">Sair</button>
        </form>
    @endauth

    <hr style="border:none; border-top:1px solid var(--color-border); margin:0.2rem 0;">
    <a href="https://github.com/LuizBMJ/Projeto-Gerenciador-de-Habitos" target="_blank">
        GitHub
    </a>

</div>