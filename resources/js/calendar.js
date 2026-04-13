let selectedHabit = null;
let calendar = null;
let habitOrder = 0;
// 0 = criação
// 1 = alfabética
// 2 = mais concluído

window.selectHabit = function(id, el) {

    selectedHabit = id;

    // Remove active styles from all buttons
    document.querySelectorAll('[data-habit]').forEach(btn => {
        btn.classList.remove('bg-surface-solid', 'text-brand-blue', 'border-brand-blue/50', 'shadow-md', 'scale-105');
        btn.classList.add('bg-transparent', 'text-text-secondary', 'border-border-glass');
    });

    // Add active styles to the selected button
    el.classList.remove('bg-transparent', 'text-text-secondary', 'border-border-glass');
    el.classList.add('bg-surface-solid', 'text-brand-blue', 'border-brand-blue/50', 'shadow-md', 'scale-105');

    if (calendar) {
        calendar.refetchEvents();
    }
}

function initCalendar() {

    const calendarEl = document.getElementById('calendar');

    if (!calendarEl) return; // not on the calendar page

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    // "Todos" começa selecionado
    const allButton = document.querySelector('[data-all]');
    if (allButton) {
        allButton.classList.remove('bg-transparent', 'text-text-secondary', 'border-border-glass');
        allButton.classList.add('bg-surface-solid', 'text-brand-blue', 'border-brand-blue/50', 'shadow-md', 'scale-105');
    }

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        height: "auto",
        dayMaxEvents: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        titleFormat: { year: 'numeric', month: 'short' }, // Mes abreviado para mobile

        // Ajuste dinâmico para telas maiores
        windowResize: function(view) {
            if (window.innerWidth >= 768) {
                calendar.setOption('headerToolbar', {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek'
                });
                calendar.setOption('titleFormat', { year: 'numeric', month: 'long' });
            } else {
                calendar.setOption('headerToolbar', {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                });
                calendar.setOption('titleFormat', { year: 'numeric', month: 'short' });
            }
        },

        events: function(fetchInfo, successCallback) {
            let url = '/dashboard/habits/calendar/events';
            if (selectedHabit !== null) {
                url += '?habit_id=' + selectedHabit;
            }
            fetch(url)
                .then(res => res.json())
                .then(data => successCallback(data))
                .catch(err => console.error("Erro ao carregar eventos:", err));
        },

        dayCellDidMount: function(info) {
            info.el.style.cursor = "pointer";
        },

        dateClick: function(info) {
            if (selectedHabit === null) {
                mostrarToast('error', 'Selecione um hábito primeiro');
                return;
            }

            const dayCell = info.dayEl;
            dayCell.classList.add('scale-95', 'opacity-80', 'transition-all', 'duration-150');
            setTimeout(() => {
                dayCell.classList.remove('scale-95', 'opacity-80');
            }, 150);

            fetch('/dashboard/habits/calendar/toggle-date', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    habit_id: selectedHabit,
                    date: info.dateStr
                })
            })
            .then(res => {
                if (!res.ok) throw new Error('Falha ao marcar hábito');
                return res.json();
            })
            .then(() => {
                calendar.refetchEvents();
            })
            .catch(err => {
                console.error("Erro ao marcar hábito:", err);
                mostrarToast('error', 'Erro ao atualizar calendário');
            });
        },

        eventDidMount: function(info) {
            info.el.style.border = "none";
            info.el.style.borderRadius = "4px";
            info.el.style.padding = "2px 4px";
            info.el.style.fontSize = "10px";
            if (window.innerWidth >= 640) {
                info.el.style.fontSize = "12px";
            }
        }
    });

    calendar.render();
}

window.toggleHabitOrder = function () {

    habitOrder++;

    if (habitOrder > 2) habitOrder = 0;

    const container = document.querySelector('[data-habit]')?.parentElement;
    if (!container) return;

    const buttons = Array.from(container.querySelectorAll('[data-habit]:not([data-all])'));

    buttons.sort((a, b) => {

        if (habitOrder === 1) {
            return a.dataset.name.localeCompare(b.dataset.name);
        }

        if (habitOrder === 2) {
            return b.dataset.completed - a.dataset.completed;
        }

        return new Date(a.dataset.created) - new Date(b.dataset.created);
    });

    buttons.forEach(btn => container.appendChild(btn));

    const modes = [
        "Ordem de criação",
        "Ordem alfabética",
        "Mais concluído"
    ];

    mostrarToast('success', modes[habitOrder]);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCalendar);
} else {
    initCalendar();
}