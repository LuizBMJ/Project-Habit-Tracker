let selectedHabit = null;
let calendar = null;

window.selectHabit = function(id, el) {

    selectedHabit = id;

    document.querySelectorAll('[data-habit]').forEach(btn => {
        btn.classList.remove('bg-habit-orange', 'text-white');
    });

    el.classList.add('bg-habit-orange', 'text-white');

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
        allButton.classList.add('bg-habit-orange', 'text-white');
    }

    calendar = new FullCalendar.Calendar(calendarEl, {

        initialView: 'dayGridMonth',
        locale: 'pt-br',
        height: "auto",
        dayMaxEvents: true,

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

            dayCell.style.transform = "scale(0.95)";
            setTimeout(() => {
                dayCell.style.transform = "scale(1)";
            }, 120);

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
                if (!res.ok) {
                    console.error('Status:', res.status, res.statusText);
                    return res.text().then(t => { throw new Error(t) });
                }
                return res.json();
            })
            .then(() => {
                calendar.refetchEvents();
            })
            .catch(err => {
                console.error("Erro ao marcar hábito:", err);
            });
        },

        eventDidMount: function(info) {
            info.el.style.border = "none";
            info.el.style.borderRadius = "6px";
            info.el.style.padding = "2px 4px";
            info.el.style.fontSize = "12px";
        }

    });

    calendar.render();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCalendar);
} else {
    initCalendar();
}