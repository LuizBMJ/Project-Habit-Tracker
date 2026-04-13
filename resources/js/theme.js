
function initTheme() {
    const themeToggle = document.getElementById('theme-toggle');
    const darkIcon    = document.getElementById('theme-toggle-dark-icon');
    const lightIcon   = document.getElementById('theme-toggle-light-icon');

    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';

    if (currentTheme === 'dark') {
        darkIcon?.classList.remove('hidden');
        lightIcon?.classList.add('hidden');
    } else {
        lightIcon?.classList.remove('hidden');
        darkIcon?.classList.add('hidden');
    }

    if (!themeToggle) return;

    themeToggle.addEventListener('click', () => {
        const isDark   = document.documentElement.getAttribute('data-theme') === 'dark';
        const newTheme = isDark ? 'light' : 'dark';

        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);

        if (newTheme === 'dark') {
            darkIcon?.classList.remove('hidden');
            lightIcon?.classList.add('hidden');
        } else {
            darkIcon?.classList.add('hidden');
            lightIcon?.classList.remove('hidden');
        }

        if (window.navigator.vibrate) {
            window.navigator.vibrate(5);
        }
    });
}

document.addEventListener('DOMContentLoaded', initTheme);
window.initTheme = initTheme;
