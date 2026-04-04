(function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let activeCell = null;

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

    window.closeDayDetail = function () {
        document.getElementById('day-detail-panel').classList.add('hidden');
        if (activeCell) {
            activeCell.classList.remove('ring-2', 'ring-orange-500');
            activeCell = null;
        }
    };

    function escapeHtml(str) {
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    document.querySelectorAll('[data-date]').forEach(function (cell) {
        cell.addEventListener('click', function () {

            const date  = cell.dataset.date;
            const count = parseInt(cell.dataset.count, 10);

            if (count === 0) {
                mostrarToast('error', 'Nenhum hábito concluído neste dia.');
                closeDayDetail();
                return;
            }

            // Toggle off if clicking same cell
            if (activeCell === cell) {
                closeDayDetail();
                return;
            }

            if (activeCell) activeCell.classList.remove('ring-2', 'ring-orange-500');
            activeCell = cell;
            cell.classList.add('ring-2', 'ring-orange-500');

            const panel = document.getElementById('day-detail-panel');
            document.getElementById('day-detail-date').textContent = formatDateBR(date);
            panel.classList.remove('hidden');
            showLoading(true);

            fetch('/dashboard/habits/historico/day?date=' + date, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            })
            .then(res => res.json())
            .then(function (habits) {
                showLoading(false);
                const list = document.getElementById('day-detail-list');
                list.innerHTML = '';

                habits.forEach(function (habit) {
                    const li = document.createElement('li');
                    li.className = 'flex items-center gap-2 text-sm text-gray-700 bg-white habit-shadow px-3 py-2';
                    li.innerHTML =
                        '<span class="w-2 h-2 rounded-full bg-habit-orange flex-shrink-0 inline-block"></span>' +
                        '<span>' + escapeHtml(habit.name) + '</span>';
                    list.appendChild(li);
                });
            })
            .catch(function () {
                showLoading(false);
                mostrarToast('error', 'Erro ao carregar hábitos.');
                closeDayDetail();
            });
        });
    });

})();