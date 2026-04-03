
let selectedHabit = null;
let calendar = null;

window.selectHabit = function(id, el) {
    selectedHabit = id;

    document.querySelectorAll('.habit-btn').forEach(btn => {
        btn.classList.remove('bg-habit-orange', 'text-white');
    });

    el.classList.add('bg-habit-orange', 'text-white');

    calendar.refetchEvents();
}

document.addEventListener('DOMContentLoaded', function () {

    let calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',

        events: function(fetchInfo, successCallback) {
            let url = '/dashboard/habits/calendar/events';

            if (selectedHabit) {
                url += '?habit_id=' + selectedHabit;
            }

            fetch(url)
                .then(res => res.json())
                .then(data => successCallback(data));
        },

        dateClick: function(info) {

            if (!selectedHabit) {
                alert('Selecione um hábito primeiro');
                return;
            }

            fetch('/dashboard/habits/calendar/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    habit_id: selectedHabit,
                    date: info.dateStr
                })
            })
            .then(() => calendar.refetchEvents());
        }
    });

    calendar.render();
});
