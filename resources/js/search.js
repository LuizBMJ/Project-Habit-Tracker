let searchTimeout = null;

function filterHabits(query) {
    clearTimeout(searchTimeout);

    const input = document.getElementById('habit-search');
    if (!input) return;

    const list = document.getElementById(input.dataset.list ?? 'habit-list');
    if (!list) return;

    const search = query.trim();

    searchTimeout = setTimeout(() => {
        if (search === '') {
            if (typeof window.resetHabitPagination === 'function') {
                window.resetHabitPagination();
            }
        } else {
            searchBackend(search, list);
        }
    }, 300);
}

/**
 * Sort habits by relevance to the search term:
 *   1. Name starts with the term  (closest match)
 *   2. Name contains the term elsewhere
 * Within each group habits are sorted alphabetically.
 */
function sortByRelevance(habits, search) {
    const term = search.toLowerCase();

    return habits.slice().sort((a, b) => {
        const aName = a.name.toLowerCase();
        const bName = b.name.toLowerCase();

        const aStarts = aName.startsWith(term);
        const bStarts = bName.startsWith(term);

        // Different groups — starts-with wins
        if (aStarts !== bStarts) return aStarts ? -1 : 1;

        // Same group — alphabetical
        return aName.localeCompare(bName);
    });
}

function searchBackend(search, list) {
    const paginateUrl = list.dataset.paginateUrl;
    const noResults   = document.getElementById('no-results');
    const loadMore    = document.getElementById('load-more');
    const loadAll     = document.getElementById('load-all-btn');

    fetch(`${paginateUrl}?load_all=1&search=${encodeURIComponent(search)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        list.innerHTML = '';

        const sorted = sortByRelevance(data.habits, search);

        sorted.forEach(habit => {
            const event = new CustomEvent('render-habit', { detail: habit, bubbles: true });
            list.dispatchEvent(event);
        });

        if (noResults) noResults.classList.toggle('hidden', data.habits.length > 0);

        // Hide load buttons during search — all results are already shown
        if (loadMore) loadMore.classList.add('hidden');
        if (loadAll)  loadAll.classList.add('hidden');
    })
    .catch(e => console.error('Search failed:', e));
}

window.filterHabits = filterHabits;