// ── SVG icon strings (mirrors resources/views/components/icons/*.blade.php) ──
const ICONS = {
    check:  `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M229.66,77.66l-128,128a8,8,0,0,1-11.32,0l-56-56a8,8,0,0,1,11.32-11.32L96,188.69,218.34,66.34a8,8,0,0,1,11.32,11.32Z"></path></svg>`,
    trash:  `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path></svg>`,
    update: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM51.31,160,136,75.31,152.69,92,68,176.68ZM48,179.31,76.69,208H48Zm48,25.38L79.31,188,164,103.31,180.69,120Zm96-96L147.31,64l24-24L216,84.68Z"></path></svg>`,
};

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
    const deleteUrl   = list.dataset.deleteUrl;
    let loading       = false;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    // ── Render ────────────────────────────────────────────────────────────────

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
                    data-id="${habit.id}"
                    data-name="${habit.name.toLowerCase()}">
                    <input type="checkbox" class="habit-checkbox w-5 h-5 cursor-pointer flex-shrink-0"
                        data-id="${habit.id}">
                    <div class="habit-shadow-lg p-2 bg-[#FFDAAC] w-full">
                        <p class="font-bold text-lg">${habit.name}</p>
                    </div>
                    <a href="${editUrl}/${habit.id}/edit"
                        class="flex items-center bg-white habit-shadow-lg p-2 hover:opacity-50">
                        ${ICONS.update}
                    </a>
                    <form action="${editUrl}/${habit.id}" method="POST">
                        <input type="hidden" name="_token" value="${csrfToken()}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit"
                            class="flex items-center bg-red-500 habit-shadow-lg text-white p-2 hover:opacity-50 cursor-pointer">
                            ${ICONS.trash}
                        </button>
                    </form>
                </li>`;
        }
    }

    // ── Dashboard: per-habit AJAX toggle ─────────────────────────────────────

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
            checkbox.checked = !checkbox.checked; // revert on error
        } finally {
            checkbox.disabled = false;
        }
    }

    // ── Shared: sync the select-all checkbox state ────────────────────────────
    // Called after every render, toggle, and fetch so the checkbox always
    // reflects what is actually loaded and checked in the list.

    function syncSelectAllCheckbox() {
        const selectAll = document.getElementById('select-all-checkbox');
        if (!selectAll) return;
        const all     = list.querySelectorAll('.habit-checkbox');
        const checked = list.querySelectorAll('.habit-checkbox:checked');
        selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
        selectAll.checked       = all.length > 0 && checked.length === all.length;
    }

    // ── Settings: show/hide "Deletar selecionados" ────────────────────────────

    function syncDeleteBtn() {
        const deleteBtn = document.getElementById('delete-selected-btn');
        if (!deleteBtn) return;
        const anyChecked = list.querySelectorAll('.habit-checkbox:checked').length > 0;
        deleteBtn.classList.toggle('hidden', !anyChecked);
        deleteBtn.classList.toggle('flex',   anyChecked);
    }

    // ── Individual checkbox clicks on the list ────────────────────────────────

    list.addEventListener('change', (e) => {
        const cb = e.target.closest('.habit-checkbox');
        if (!cb) return;

        if (view === 'dashboard') {
            toggleHabit(cb.dataset.id, cb).then(() => syncSelectAllCheckbox());
        }

        if (view === 'settings') {
            syncSelectAllCheckbox();
            syncDeleteBtn();
        }
    });

    // ── Select-all checkbox ───────────────────────────────────────────────────

    const selectAllCb = document.getElementById('select-all-checkbox');
    if (selectAllCb) {
        selectAllCb.addEventListener('change', async () => {
            const shouldCheck = selectAllCb.checked;
            const checkboxes  = [...list.querySelectorAll('.habit-checkbox')];

            if (view === 'dashboard') {
                const targets = checkboxes.filter(cb => cb.checked !== shouldCheck);
                if (targets.length === 0) return;

                targets.forEach(cb => { cb.checked = shouldCheck; });

                selectAllCb.disabled = true;
                await Promise.all(targets.map(cb => toggleHabit(cb.dataset.id, cb)));
                selectAllCb.disabled = false;

                syncSelectAllCheckbox();
            }

            if (view === 'settings') {
                checkboxes.forEach(cb => { cb.checked = shouldCheck; });
                syncDeleteBtn();
            }
        });
    }

    // ── Settings: delete selected ─────────────────────────────────────────────

    window.deleteSelectedHabits = async function () {
        const checked = [...list.querySelectorAll('.habit-checkbox:checked')];

        if (checked.length === 0) {
            mostrarToast('error', 'Nenhum hábito selecionado.');
            return;
        }

        const btn = document.getElementById('delete-selected-btn');
        if (btn) { btn.disabled = true; btn.innerHTML = `${ICONS.trash} Deletando...`; }

        const ids = checked.map(cb => cb.dataset.id);
        let successCount = 0;
        let errorCount   = 0;

        await Promise.all(ids.map(async (id) => {
            try {
                const res = await fetch(`${deleteUrl}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken(),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ _method: 'DELETE' }),
                });
                if (!res.ok) throw new Error('Delete failed');
                const li = list.querySelector(`li[data-id="${id}"]`);
                if (li) li.remove();
                successCount++;
            } catch (e) {
                console.error(`Failed to delete habit ${id}:`, e);
                errorCount++;
            }
        }));

        const selectAll = document.getElementById('select-all-checkbox');
        if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }

        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `${ICONS.trash} Deletar selecionados`;
            btn.classList.add('hidden');
            btn.classList.remove('flex');
        }

        if (errorCount === 0) {
            mostrarToast('success', `${successCount} hábito(s) deletado(s) com sucesso!`);
        } else if (successCount === 0) {
            mostrarToast('error', 'Erro ao deletar os hábitos selecionados.');
        } else {
            mostrarToast('warning', `${successCount} deletado(s), ${errorCount} com erro.`);
        }

        if (list.querySelectorAll('.habit-item').length === 0) {
            const noResults = document.getElementById('no-results');
            if (noResults) noResults.classList.remove('hidden');
        }
    };

    // ── Pagination ────────────────────────────────────────────────────────────

    list.addEventListener('render-habit', (e) => {
        list.insertAdjacentHTML('beforeend', renderHabit(e.detail));
        syncSelectAllCheckbox();
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
        loadMore.disabled    = true;

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
            }

            setLoadButtons(data.hasMore);

            // Sync after new items are painted — picks up wasCompletedToday
            // state on initial load AND reflects any prior select-all action
            // when the user loads more pages.
            syncSelectAllCheckbox();
        } catch (e) {
            console.error('Failed to load habits:', e);
        } finally {
            loadMore.textContent = 'Carregar mais';
            loadMore.disabled    = false;
            loading              = false;
        }
    }

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

            list.innerHTML      = '';
            list.dataset.offset = data.habits.length;

            data.habits.forEach(habit => {
                list.insertAdjacentHTML('beforeend', renderHabit(habit));
            });

            syncSelectAllCheckbox();
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
        list.innerHTML      = '';
        list.dataset.offset = '0';

        const noResults = document.getElementById('no-results');
        if (noResults) noResults.classList.add('hidden');

        const selectAll = document.getElementById('select-all-checkbox');
        if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }

        const deleteBtn = document.getElementById('delete-selected-btn');
        if (deleteBtn) { deleteBtn.classList.add('hidden'); deleteBtn.classList.remove('flex'); }

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