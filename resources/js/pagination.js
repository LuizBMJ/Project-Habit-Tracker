const ICONS = {
    check: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M229.66,77.66l-128,128a8,8,0,0,1-11.32,0l-56-56a8,8,0,0,1,11.32-11.32L96,188.69,218.34,66.34a8,8,0,0,1,11.32,11.32Z"></path></svg>`,
    trash: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path></svg>`,
    update: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256"><path d="M227.31,73.37,182.63,28.68a16,16,0,0,0-22.63,0L36.69,152A15.86,15.86,0,0,0,32,163.31V208a16,16,0,0,0,16,16H92.69A15.86,15.86,0,0,0,104,219.31L227.31,96a16,16,0,0,0,0-22.63ZM51.31,160,136,75.31,152.69,92,68,176.68ZM48,179.31,76.69,208H48Zm48,25.38L79.31,188,164,103.31,180.69,120Zm96-96L147.31,64l24-24L216,84.68Z"></path></svg>`,
};

function initHabitManager() {
    const list = document.getElementById('habit-list');
    if (!list) return;

    if (list.dataset.initialized === 'true') return;
    list.dataset.initialized = 'true';

    const view = list.dataset.view;
    const paginateUrl = list.dataset.paginateUrl;
    const toggleUrl = list.dataset.toggleUrl;
    const editUrl = list.dataset.editUrl;
    const deleteUrl = list.dataset.deleteUrl;
    let loading = false;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function renderHabit(habit) {
        if (view === 'dashboard') {
            return `
                <li class="glass-card mb-3 p-4 habit-item flex flex-col gap-3 group" data-id="${habit.id}" data-name="${habit.name.toLowerCase()}">
                    <div class="ripple-effect top-1/2 left-8 -translate-y-1/2 w-10 h-10"></div>
                    <div class="flex items-center justify-between z-10 relative">
                        <div class="flex items-center gap-3.5 w-full">
                            <input type="checkbox" class="habit-checkbox checkbox-animate w-6 h-6 appearance-none border-2 border-brand-blue rounded-full checked:bg-brand-blue checked:border-brand-blue cursor-pointer relative after:content-[''] after:absolute after:top-[40%] after:left-[50%] after:-translate-x-1/2 after:-translate-y-1/2 after:w-[6px] after:h-[10px] after:border-r-2 after:border-b-2 after:border-white after:rotate-45 after:opacity-0 checked:after:opacity-100 transition-all flex-shrink-0"
                                data-id="${habit.id}"
                                ${habit.wasCompletedToday ? 'checked' : ''}>
                            <p class="font-semibold text-[1.05rem] text-text-primary tracking-tight group-hover:text-brand-blue transition-colors flex-1 cursor-pointer select-none" onclick="this.previousElementSibling.click()">${habit.name}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[0.75rem] font-bold text-brand-orange bg-brand-orange-light border border-brand-orange/20 px-2 py-0.5 rounded-md flex items-center gap-1 shadow-sm">
                                <span class="text-[0.8rem]">🔥</span> 
                                <span class="streak-count">${habit.streak || 0}</span>
                            </span>
                        </div>
                    </div>
                </li>`;
        }

        if (view === 'settings') {
            return `
                <li class="glass-card mb-3 p-4 habit-item flex flex-col gap-3 group relative z-10"
                    data-id="${habit.id}"
                    data-name="${habit.name.toLowerCase()}"> 
                    <div class="flex flex-wrap sm:flex-nowrap items-center justify-between z-10 relative gap-3">
                        <div class="flex items-center gap-3.5 w-full sm:w-auto flex-1">
                            <input type="checkbox" class="habit-checkbox checkbox-animate w-6 h-6 appearance-none border-2 border-brand-blue rounded-full checked:bg-brand-blue checked:border-brand-blue cursor-pointer relative after:content-[''] after:absolute after:top-[40%] after:left-[50%] after:-translate-x-1/2 after:-translate-y-1/2 after:w-[6px] after:h-[10px] after:border-r-2 after:border-b-2 after:border-white after:rotate-45 after:opacity-0 checked:after:opacity-100 transition-all flex-shrink-0"
                                data-id="${habit.id}">
                            <p class="font-semibold text-[1.05rem] text-text-primary tracking-tight group-hover:text-brand-blue transition-colors flex-1 cursor-pointer select-none" onclick="this.previousElementSibling.click()">${habit.name}</p>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-end sm:ml-0">
                            <a href="${editUrl}/${habit.id}/edit"
                                class="flex items-center justify-center bg-surface-secondary text-text-secondary rounded-xl p-1.5 hover:bg-brand-blue hover:text-white transition-all duration-200 shadow-sm border border-border border-b-[2px] active:translate-y-[1px] active:border-b">
                                ${ICONS.update}
                            </a>
                            <form action="${deleteUrl}/${habit.id}" method="POST" class="m-0">
                                <input type="hidden" name="_token" value="${csrfToken()}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit"
                                    class="flex items-center justify-center bg-surface-secondary text-error rounded-xl p-1.5 hover:bg-error hover:text-white transition-all duration-200 shadow-sm border border-border border-b-[2px] cursor-pointer active:translate-y-[1px] active:border-b">
                                    ${ICONS.trash}
                                </button>
                            </form>
                        </div>
                    </div>
                </li>`;
        }
    }

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

            const li = checkbox.closest('li');
            if (li) {
                const streakCount = li.querySelector('.streak-count');
                if (streakCount && data.streak !== undefined) {
                    streakCount.textContent = data.streak;
                }
            }
        } catch (e) {
            console.error('Toggle failed:', e);
            checkbox.checked = !checkbox.checked;
        } finally {
            checkbox.disabled = false;
        }
    }

    function syncSelectAllCheckbox() {
        const selectAll = document.getElementById('select-all-checkbox');
        if (!selectAll) return;
        const all = list.querySelectorAll('.habit-checkbox');
        const checked = list.querySelectorAll('.habit-checkbox:checked');
        selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
        selectAll.checked = all.length > 0 && checked.length === all.length;
    }

    function syncDeleteBtn() {
        const deleteBtn = document.getElementById('delete-selected-btn');
        if (!deleteBtn) return;
        const anyChecked = list.querySelectorAll('.habit-checkbox:checked').length > 0;
        deleteBtn.classList.toggle('hidden', !anyChecked);
        deleteBtn.classList.toggle('flex', anyChecked);
    }

    list.addEventListener('change', (e) => {
        const cb = e.target.closest('.habit-checkbox');
        if (!cb) return;

        if (view === 'dashboard') {
            if (cb.checked) {
                const li = cb.closest('li');
                const ripple = li.querySelector('.ripple-effect');
                if (ripple) {
                    ripple.style.animation = 'none';
                    ripple.offsetHeight;
                    ripple.style.animation = null;
                }
            }
            toggleHabit(cb.dataset.id, cb).then(() => syncSelectAllCheckbox());
        }

        if (view === 'settings') {
            syncSelectAllCheckbox();
            syncDeleteBtn();
        }
    });

    const selectAllCb = document.getElementById('select-all-checkbox');
    if (selectAllCb) {
        selectAllCb.addEventListener('change', async () => {
            const shouldCheck = selectAllCb.checked;
            const checkboxes = [...list.querySelectorAll('.habit-checkbox')];

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
        let errorCount = 0;

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
            const searchWrapper = document.getElementById('search-wrapper');
            if (noResults) noResults.classList.remove('hidden');
            if (searchWrapper && view === 'settings') searchWrapper.classList.add('hidden');
        }
    };

    async function loadHabits(search = '') {
        if (loading) return;
        loading = true;

        try {
            const res = await fetch(`${paginateUrl}?search=${encodeURIComponent(search)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            list.innerHTML = '';
            data.habits.forEach(habit => {
                list.insertAdjacentHTML('beforeend', renderHabit(habit));
            });

            const searchWrapper = document.getElementById('search-wrapper');
            const searchInputWrapper = document.getElementById('search-input-wrapper');
            const selectAllWrapper = document.getElementById('select-all-wrapper');
            const noResults = document.getElementById('no-results');

            const allCount = data.all_count ?? 0;
            const filteredCount = data.total ?? 0;

            if (searchWrapper) {
                if (view === 'dashboard') {
                    searchWrapper.classList.remove('hidden');
                } else {
                    searchWrapper.classList.toggle('hidden', allCount === 0);
                }
            }

            if (searchInputWrapper) searchInputWrapper.classList.toggle('hidden', allCount <= 1 && !search);
            if (selectAllWrapper) selectAllWrapper.classList.toggle('hidden', allCount <= 1 && !search);

            if (noResults) noResults.classList.toggle('hidden', filteredCount > 0);

            syncSelectAllCheckbox();
        } catch (e) {
            console.error('Failed to load habits:', e);
        } finally {
            loading = false;
        }
    }

    window.filterHabits = function(value) {
        loadHabits(value);
    };

    window.resetHabitManager = function() {
        loadHabits();
    };

    loadHabits();
}

document.addEventListener('DOMContentLoaded', initHabitManager);
window.initHabitPagination = initHabitManager; // Keep old name for compatibility if needed elsewhere