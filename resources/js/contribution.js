//
// contribution.js
// This file handles the history page contribution grid
// It allows clicking on days to see which habits were completed
//

(function () {

    // Get CSRF token for requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Keep track of which cell is currently active (highlighted)
    let activeCell = null;

    // Format a date string to Brazilian format
    // Example: "2024-01-15" -> "sunday, January 15, 2024"
    function formatDateBR(dateStr) {
        const [year, month, day] = dateStr.split('-').map(Number);
        const date = new Date(year, month - 1, day);
        return date.toLocaleDateString('pt-BR', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    // Show or hide the loading spinner
    function showLoading(show) {
        const loading = document.getElementById('day-detail-loading');
        const list    = document.getElementById('day-detail-list');
        if (show) {
            loading.classList.remove('hidden');
            loading.classList.add('flex');
            list.classList.add('hidden');
        } else {
            loading.classList.add('hidden');
            loading.classList.remove('flex');
            list.classList.remove('hidden');
        }
    }

    // Close the day detail panel
    window.closeDayDetail = function () {
        document.getElementById('day-detail-panel').classList.add('hidden');
        if (activeCell) {
            activeCell.classList.remove('ring-2', 'ring-brand-orange');
            activeCell = null;
        }
    };

    // Escape HTML to prevent XSS attacks
    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // Add click handlers to each day cell in the grid
    document.querySelectorAll('[data-date]').forEach(function (cell) {
        cell.addEventListener('click', function () {

            const date  = cell.dataset.date;
            const count = parseInt(cell.dataset.count, 10);

            // If no habits completed, show message and don't open panel
            if (count === 0) {
                mostrarToast('error', 'Nenhum hábito foi completado nesse dia.');
                window.closeDayDetail();
                return;
            }

            // If clicking the same cell, close the panel
            if (activeCell === cell) {
                window.closeDayDetail();
                return;
            }

            // Remove highlight from previously active cell
            if (activeCell) activeCell.classList.remove('ring-2', 'ring-brand-orange');

            // Highlight this cell
            activeCell = cell;
            cell.classList.add('ring-2', 'ring-brand-orange');

            // Open the panel and show loading
            const panel = document.getElementById('day-detail-panel');
            document.getElementById('day-detail-date').textContent = formatDateBR(date);
            panel.classList.remove('hidden');
            showLoading(true);

            // Fetch habits for this day
            fetch('/dashboard/habits/historico/day?date=' + date, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            })
            .then(res => res.json())
            .then(function (habits) {
                showLoading(false);
                const list = document.getElementById('day-detail-list');
                list.innerHTML = '';

                // Render each habit as a list item
                habits.forEach(function (habit) {
                    const li = document.createElement('li');
                    li.className = 'habit-day-item';
                    li.innerHTML =
                        '<span class="habit-day-dot"></span>' +
                        '<span>' + escapeHtml(habit.name) + '</span>';
                    list.appendChild(li);
                });
            })
            .catch(function () {
                showLoading(false);
                mostrarToast('error', 'Erro ao carregar os hábitos.');
                closeDayDetail();
            });
        });
    });

})();
