function initHabitPagination() {
    const list     = document.getElementById('habit-list');
    const loadMore = document.getElementById('load-more');
    if (!list || !loadMore) return;

    // Prevent duplicate initialization
    if (list.dataset.initialized === 'true') return;
    list.dataset.initialized = 'true';

    const view        = list.dataset.view;
    const paginateUrl = list.dataset.paginateUrl;
    const toggleUrl   = list.dataset.toggleUrl;
    const editUrl     = list.dataset.editUrl;
    let loading       = false;

    // Tracks whether the next bulk action should check or uncheck
    let markAllState = false; // false = "mark all", true = "unmark all"

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function renderHabit(habit) {
        if (view === 'dashboard') {
            return `
                <li class="habit-shadow-lg p-2 bg-[#FFDAAC] habit-item" data-id="${habit.id}" data-name="${habit.name.toLowerCase()}">
                    <div class="flex gap-2 items-center">
                        <input type="checkbox" class="habit-checkbox w-5 h-5 cursor-pointer"
                            data-id="${habit.id}"
                            ${habit.wasCompletedToday ? 'checked' : ''}>
                        <p class="font-bold text-lg">${habit.name}</p>
                    </div>
                </li>`;
        }

        if (view === 'settings') {
            return `
                <li class="habit-item flex gap-2 items-center justify-between w-full"
                    data-name="${habit.name.toLowerCase()}">
                    <div class="habit-shadow-lg p-2 bg-[#FFDAAC] w-full">
                        <p class="font-bold text-lg">${habit.name}</p>
                    </div>
                    <a href="${editUrl}/${habit.id}/edit" class="bg-white habit-shadow-lg text-white p-2 hover:opacity-50">✏️</a>
                    <form action="${editUrl}/${habit.id}" method="POST">
                        <input type="hidden" name="_token" value="${csrfToken()}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="bg-red-500 habit-shadow-lg text-white p-2 hover:opacity-50 cursor-pointer">🗑️</button>
                    </form>
                </li>`;
        }
    }

    function updateMarkAllBtn() {
        const btn = document.getElementById('mark-all-btn');
        if (!btn) return;
        btn.textContent = markAllState ? '✖ Desmarcar todos' : '✔ Marcar todos';
    }

    // AJAX toggle — no page reload
    async function toggleHabit(habitId, checkbox) {
        checkbox.disabled = true;

        try {
            const res = await fetch(`${toggleUrl}/${habitId}/toggle`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                    'Content-Type': 'application/json',
                }
            });

            if (!res.ok) throw new Error('Toggle failed');

            const data = await res.json();
            checkbox.checked = data.completed;

        } catch (e) {
            console.error('Toggle failed:', e);
            checkbox.checked = !checkbox.checked;
        } finally {
            checkbox.disabled = false;
        }
    }

    // Toggle all: first click checks all, second unchecks all, loops
    window.toggleAllHabits = async function () {
        const btn = document.getElementById('mark-all-btn');

        const targetCheckboxes = markAllState
            ? [...list.querySelectorAll('.habit-checkbox:checked')]
            : [...list.querySelectorAll('.habit-checkbox:not(:checked)')];

        if (targetCheckboxes.length === 0) {
            markAllState = !markAllState;
            updateMarkAllBtn();
            return;
        }

        if (btn) { btn.disabled = true; btn.textContent = 'Aguarde...'; }

        targetCheckboxes.forEach(cb => { cb.checked = !markAllState; });

        await Promise.all(targetCheckboxes.map(cb => toggleHabit(cb.dataset.id, cb)));

        markAllState = !markAllState;
        updateMarkAllBtn();

        if (btn) btn.disabled = false;
    };

    // Delegate checkbox clicks on the list (no form submit, no reload)
    list.addEventListener('change', (e) => {
        const cb = e.target.closest('.habit-checkbox');
        if (!cb) return;
        toggleHabit(cb.dataset.id, cb);
    });

    list.addEventListener('render-habit', (e) => {
        list.insertAdjacentHTML('beforeend', renderHabit(e.detail));
    });

    function setLoadButtons(hasMore) {
        const loadAll = document.getElementById('load-all-btn');
        loadMore.classList.toggle('hidden', !hasMore);
        if (loadAll) loadAll.classList.toggle('hidden', !hasMore);
    }

    async function fetchHabits() {
        if (loading) return;
        loading = true;
        loadMore.textContent = 'Carregando...';
        loadMore.disabled = true;

        const offset = parseInt(list.dataset.offset ?? '0');

        try {
            const res  = await fetch(`${paginateUrl}?offset=${offset}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            data.habits.forEach(habit => {
                list.insertAdjacentHTML('beforeend', renderHabit(habit));
            });

            const newOffset = offset + data.habits.length;
            list.dataset.offset = newOffset;

            if (newOffset > 0) {
                const searchWrapper = document.getElementById('search-wrapper');
                if (searchWrapper) searchWrapper.classList.remove('hidden');

                if (view === 'dashboard') {
                    const markAllBtn = document.getElementById('mark-all-btn');
                    if (markAllBtn) markAllBtn.classList.remove('hidden');
                }
            }

            setLoadButtons(data.hasMore);
        } catch (e) {
            console.error('Failed to load habits:', e);
        } finally {
            loadMore.textContent = 'Carregar mais';
            loadMore.disabled = false;
            loading = false;
        }
    }

    // Fetch everything remaining in one shot
    async function fetchAll() {
        if (loading) return;
        loading = true;

        const loadAll = document.getElementById('load-all-btn');
        if (loadAll) { loadAll.disabled = true; loadAll.textContent = 'Carregando...'; }
        loadMore.disabled = true;

        try {
            const res  = await fetch(`${paginateUrl}?load_all=1`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            // Clear current list and render all from scratch to avoid duplicates
            list.innerHTML = '';
            list.dataset.offset = data.habits.length;

            data.habits.forEach(habit => {
                list.insertAdjacentHTML('beforeend', renderHabit(habit));
            });

            // No more pages — hide both buttons
            setLoadButtons(false);

        } catch (e) {
            console.error('Failed to load all habits:', e);
        } finally {
            loading = false;
            if (loadAll) { loadAll.disabled = false; loadAll.textContent = 'Carregar tudo'; }
            loadMore.disabled = false;
        }
    }

    function resetAndFetch() {
        list.innerHTML = '';
        list.dataset.offset = '0';
        markAllState = false;
        updateMarkAllBtn();

        const noResults = document.getElementById('no-results');
        if (noResults) noResults.classList.add('hidden');

        fetchHabits();
    }

    loadMore.addEventListener('click', fetchHabits);

    const loadAllBtn = document.getElementById('load-all-btn');
    if (loadAllBtn) loadAllBtn.addEventListener('click', fetchAll);

    window.resetHabitPagination = resetAndFetch;

    fetchHabits();
}

document.addEventListener('DOMContentLoaded', initHabitPagination);
window.initHabitPagination = initHabitPagination;