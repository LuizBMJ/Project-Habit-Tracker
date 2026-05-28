//
// calendar.js
// This file handles the FullCalendar on the calendar page
// It allows users to see their habits on a calendar and toggle completion
//

import { Calendar } from 'fullcalendar';

// Currently selected habit filter (null = "all" habits)
let selectedHabit = null;

// Reference to the FullCalendar instance
let calendar = null;

// Habit sort order: 0 = creation date, 1 = alphabetical, 2 = most completed
let habitOrder = 0;
// 0 = creation
// 1 = alphabetical
// 2 = most completed

// Select a habit to filter by
// Highlights the selected button and refreshes calendar events
window.selectHabit = function(id, el) {

    // If clicking the already-selected habit, unselect it
    if (el.classList.contains('bg-surface-solid')) {
        selectedHabit = null;

        document.querySelectorAll('[data-habit]').forEach(btn => {
            btn.classList.remove('bg-surface-solid', 'text-brand-blue', 'border-brand-blue/50', 'shadow-md', 'scale-105');
            btn.classList.add('bg-transparent', 'text-text-secondary', 'border-border-glass');
        });

        const allBtn = document.querySelector('[data-all]');
        if (allBtn) {
            allBtn.classList.remove('bg-transparent', 'text-text-secondary', 'border-border-glass');
            allBtn.classList.add('bg-surface-solid', 'text-brand-blue', 'border-brand-blue/50', 'shadow-md', 'scale-105');
        }

        if (calendar) {
            calendar.refetchEvents();
        }
        return;
    }

    selectedHabit = id;

    // Remove active styles from all buttons
    document.querySelectorAll('[data-habit]').forEach(btn => {
        btn.classList.remove('bg-surface-solid', 'text-brand-blue', 'border-brand-blue/50', 'shadow-md', 'scale-105');
        btn.classList.add('bg-transparent', 'text-text-secondary', 'border-border-glass');
    });

    // Add active styles to the selected button
    el.classList.remove('bg-transparent', 'text-text-secondary', 'border-border-glass');
    el.classList.add('bg-surface-solid', 'text-brand-blue', 'border-brand-blue/50', 'shadow-md', 'scale-105');

    // Refresh calendar to show events for the selected habit
    if (calendar) {
        calendar.refetchEvents();
    }
}

// Initialize the FullCalendar
function initCalendar() {

    const calendarEl = document.getElementById('calendar');

    // Exit if not on calendar page
    if (!calendarEl) return;

    // Get CSRF token for requests
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    // Select "Todos" by default
    const allButton = document.querySelector('[data-all]');
    if (allButton) {
        allButton.classList.remove('bg-transparent', 'text-text-secondary', 'border-border-glass');
        allButton.classList.add('bg-surface-solid', 'text-brand-blue', 'border-brand-blue/50', 'shadow-md', 'scale-105');
    }

    // Create FullCalendar instance with custom settings
    calendar = new Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        height: "auto",
        dayMaxEvents: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        titleFormat: { year: 'numeric', month: 'short' }, // Short month for mobile

        // Adjust toolbar for different screen sizes
        windowResize: function(view) {
            if (window.innerWidth >= 768) {
                // Desktop: show month/week toggle
                calendar.setOption('headerToolbar', {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek'
                });
                calendar.setOption('titleFormat', { year: 'numeric', month: 'long' });
            } else {
                // Mobile: hide week view option
                calendar.setOption('headerToolbar', {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                });
                calendar.setOption('titleFormat', { year: 'numeric', month: 'short' });
            }
        },

        // Fetch events for the calendar
        events: function(fetchInfo, successCallback) {
            let url = '/dashboard/habits/calendar/events';
            if (selectedHabit !== null) {
                url += '?habit_id=' + selectedHabit;
            }
            fetch(url)
                .then(res => res.json())
                .then(data => successCallback(data))
                .catch(err => console.error("Error loading events:", err));
        },

        // Make day cells clickable
        dayCellDidMount: function(info) {
            info.el.style.cursor = "pointer";
        },

        // Handle clicking on a day to toggle habit
        dateClick: function(info) {
            // Require a habit to be selected first
            if (selectedHabit === null) {
                mostrarToast('error', 'Selecione um hábito primeiro');
                return;
            }

            // Add visual feedback
            const dayCell = info.dayEl;
            dayCell.classList.add('scale-95', 'opacity-80', 'transition-all', 'duration-150');
            setTimeout(() => {
                dayCell.classList.remove('scale-95', 'opacity-80');
            }, 150);

            // Send toggle request to server
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
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Failed to mark habit');
                }
                return data;
            })
            .then(() => {
                // Refresh calendar events
                calendar.refetchEvents();
            })
            .catch(err => {
                console.error("Error marking habit:", err);
                mostrarToast('error', err.message);
            });
        },

        // Style calendar events
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

// Toggle between different sort orders for habit buttons
// Called when tapping the filter/sort button
window.toggleHabitOrder = function () {

    habitOrder++;

    // Cycle through: creation -> alphabetical -> most completed -> back to creation
    if (habitOrder > 2) habitOrder = 0;

    // Get the container with habit buttons
    const container = document.querySelector('[data-habit]')?.parentElement;
    if (!container) return;

    // Get all habit buttons (not the "all" button)
    const buttons = Array.from(container.querySelectorAll('[data-habit]:not([data-all])'));

    // Sort buttons based on current order
    buttons.sort((a, b) => {

        if (habitOrder === 1) {
            // Sort alphabetically by name
            return a.dataset.name.localeCompare(b.dataset.name);
        }

        if (habitOrder === 2) {
            // Sort by completion count (highest first)
            return b.dataset.completed - a.dataset.completed;
        }

        // Sort by creation date (oldest first)
        return new Date(a.dataset.created) - new Date(b.dataset.created);
    });

    // Re-append buttons in new order
    buttons.forEach(btn => container.appendChild(btn));

    // Toast messages for each mode
    const modes = [
        "Ordem de criação",
        "Ordem alfabética",
        "Mais concluídos"
    ];

    mostrarToast('success', modes[habitOrder]);
}

// Initialize when page loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCalendar);
} else {
    initCalendar();
}