@props(['weeks', 'logCounts', 'maxCount', 'year'])

@php
    $months = [];
    $seenMonths = [];
    foreach ($weeks as $weekIndex => $week) {
        foreach ($week as $day) {
            if ($day && $day->day <= 7) {
                $monthKey = $day->format('Y-m');
                if (!in_array($monthKey, $seenMonths)) {
                    $months[$weekIndex] = $day->locale('pt_BR')->translatedFormat('M');
                    $seenMonths[] = $monthKey;
                }
                break;
            }
        }
    }
@endphp

<div class="mb-6 w-full">

    {{-- GRID --}}
    <div class="bg-orange-50 p-4 habit-shadow-lg overflow-x-auto">

        {{-- MONTH LABELS --}}
        <div class="flex gap-1 min-w-max mb-1 ml-0">
            @foreach ($weeks as $weekIndex => $week)
                <div class="w-3 text-[9px] text-gray-400 text-center">
                    {{ $months[$weekIndex] ?? '' }}
                </div>
            @endforeach
        </div>

        {{-- DAY CELLS --}}
        <div class="flex gap-1 min-w-max">
            @foreach ($weeks as $week)
                <div class="flex flex-col gap-1">
                    @foreach ($week as $day)
                        @if ($day === null)
                            <div class="w-3 aspect-square"></div>
                        @else
                            @php
                                $dateStr = $day->toDateString();
                                $count   = $logCounts->get($dateStr, 0);

                                if ($count === 0) {
                                    $bg = 'bg-[#DADFE9]';
                                } elseif ($count / $maxCount <= 0.25) {
                                    $bg = 'bg-[#FFCF99]';
                                } elseif ($count / $maxCount <= 0.50) {
                                    $bg = 'bg-[#FFB266]';
                                } elseif ($count / $maxCount <= 0.75) {
                                    $bg = 'bg-[#FF8C1A]';
                                } else {
                                    $bg = 'bg-[#FF6600]';
                                }
                            @endphp
                            <div
                                class="w-3 aspect-square rounded-xs transition hover:ring-2 hover:ring-orange-400 {{ $bg }} {{ $count > 0 ? 'cursor-pointer' : 'cursor-default' }}"
                                title="{{ $day->format('d/m/Y') }}: {{ $count }} hábito(s) concluído(s)"
                                data-date="{{ $dateStr }}"
                                data-count="{{ $count }}"
                            ></div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    {{-- LEGEND --}}
    <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
        <span>Menos</span>
        <div class="w-3 h-3 rounded-xs bg-[#DADFE9]"></div>
        <div class="w-3 h-3 rounded-xs bg-[#FFCF99]"></div>
        <div class="w-3 h-3 rounded-xs bg-[#FFB266]"></div>
        <div class="w-3 h-3 rounded-xs bg-[#FF8C1A]"></div>
        <div class="w-3 h-3 rounded-xs bg-[#FF6600]"></div>
        <span>Mais</span>
    </div>

    {{-- DAY DETAIL PANEL --}}
    <div id="day-detail-panel" class="hidden mt-4 bg-orange-50 habit-shadow-lg p-4">

        <div class="flex items-center justify-between mb-3">
            <p id="day-detail-date" class="text-sm font-bold text-gray-700 capitalize"></p>
            <button
                onclick="closeDayDetail()"
                class="text-gray-400 hover:text-gray-600 transition text-xl leading-none font-light"
                title="Fechar"
            >&times;</button>
        </div>

        {{-- Loading state --}}
        <div id="day-detail-loading" class="hidden items-center gap-2 text-sm text-gray-400">
            <svg class="animate-spin w-4 h-4 text-habit-orange" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <span>Carregando...</span>
        </div>

        {{-- Habit list --}}
        <ul id="day-detail-list" class="flex flex-col gap-2"></ul>

    </div>

</div>

<script>
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
</script>