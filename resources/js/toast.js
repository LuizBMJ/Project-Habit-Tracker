let toastTimeout = null;

window.mostrarToast = function(tipo, mensagem) {

    const toast = document.getElementById('toast');
    const message = document.getElementById('toast-message');
    const icon = document.getElementById('toast-icon');

    if (!toast) return;

    const styles = {
        success: 'bg-green-100 border-green-400 text-green-700',
        error: 'bg-red-100 border-red-400 text-red-700',
        warning: 'bg-yellow-100 border-yellow-400 text-yellow-700'
    };

    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️'
    };

    // cancela timeout anterior
    if (toastTimeout) {
        clearTimeout(toastTimeout);
    }

    // remove cores antigas
    toast.classList.remove(
        'bg-green-100','border-green-400','text-green-700',
        'bg-red-100','border-red-400','text-red-700',
        'bg-yellow-100','border-yellow-400','text-yellow-700'
    );

    // aplica novo estilo
    toast.classList.add(...styles[tipo].split(' '));

    // conteúdo
    message.textContent = mensagem;
    icon.innerHTML = icons[tipo];

    // mostra toast
    toast.classList.remove('hidden');
    toast.classList.remove('opacity-0');

    // força reinício da animação
    void toast.offsetWidth;

    toast.classList.add('opacity-100');

    // esconder depois
    toastTimeout = setTimeout(() => {

        toast.classList.remove('opacity-100');
        toast.classList.add('opacity-0');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 500);

    }, 3000);
};