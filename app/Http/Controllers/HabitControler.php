<?php

namespace App\Http\Controllers;

// This controller handles all habit-related pages and actions

use App\Http\Requests\HabitRequest;
use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HabitControler extends Controller
{
    // This trait allows the controller to authorize actions
    use AuthorizesRequests;

    // Show the main dashboard with today's habits
    public function index(): View
    {
        // Get all habits for the current user, including their logs
        $habits = Auth::user()->habits()
            ->with('habitLogs')
            ->get();
        $habitCount = $habits->count();

        return view('habits.dashboard', compact('habits', 'habitCount'));
    }

    // Show the page to create a new habit
    public function create(): View
    {
        // Check if the user has reached the limit of 10 habits
        $limitReached = Auth::user()->habits()->count() >= 10;

        return view('habits.create', compact('limitReached'));
    }

    // Save a new habit
    public function store(HabitRequest $request)
    {
        // Check if user has reached the limit of 10 habits
        $count = Auth::user()->habits()->count();
        if ($count >= 10) {
            return redirect()
                ->route('dashboard.habits.index')
                ->with('error', 'Você chegou ao limite de 10 hábitos.');
        }

        // Validate the data and create the habit
        $validated = $request->validated();
        Auth::user()->habits()->create($validated);

        // If this is the 10th habit, show a warning
        if ($count + 1 === 10) {
            return redirect()
                ->route('dashboard.habits.index')
                ->with('warning', 'Você chegou ao limite hábitos!');
        }

        return redirect()
            ->route('dashboard.habits.index')
            ->with('success', 'Hábito criado com sucesso!');
    }

    // Show the page to edit an existing habit
    public function edit(Habit $habit)
    {
        // Make sure the user owns this habit
        $this->authorize('update', $habit);

        return view('habits.edit', compact('habit'))->with('success', 'Hábito atualizado com sucesso!');
    }

    // Update an existing habit
    public function update(HabitRequest $request, Habit $habit)
    {
        // Make sure the user owns this habit
        $this->authorize('update', $habit);

        // Update the habit with the new data
        $habit->update($request->validated());

        return redirect()
            ->route('dashboard.habits.settings')
            ->with('success', 'Hábito atualizado com sucesso!');
    }

    // Delete a habit
    public function destroy(Habit $habit)
    {
        // Make sure the user owns this habit
        $this->authorize('delete', $habit);

        // Delete the habit
        $habit->delete();

        return redirect()
            ->route('dashboard.habits.settings')
            ->with('warning', 'Hábito deletado com sucesso!');
    }

    // Show the settings page to manage habits
    public function settings()
    {
        // Get all habits for the current user
        $habits = Auth::user()->habits;

        return view('habits.settings', compact('habits'));
    }

    // Toggle a habit as completed for today
    public function toggle(Request $request, Habit $habit)
    {
        // Make sure the user owns this habit
        $this->authorize('toggle', $habit);

        // Get today's date
        $today = Carbon::today()->toDateString();

        // Check if there's already a log for today
        $log = HabitLog::query()
            ->where('habit_id', $habit->id)
            ->where('completed_at', $today)
            ->first();

        // If exists, remove it (uncomplete); otherwise, create it (complete)
        if ($log) {
            $log->delete();
            $completed = false;
            $alert = 'warning';
            $message = 'Habit unmarked.';
        } else {
            HabitLog::query()->create([
                'user_id' => Auth::id(),
                'habit_id' => $habit->id,
                'completed_at' => $today,
            ]);
            $completed = true;
            $alert = 'success';
            $message = 'Habit completed!';
        }

        // If this is an AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'completed' => $completed,
                'message' => $message,
                'streak' => $habit->getCurrentStreak(),
            ]);
        }

        return redirect()
            ->route('dashboard.habits.index')
            ->with($alert, $message);
    }

    // Show the history page with the year grid
    public function history(?int $year = null): View
    {
        // Use the given year or the current year
        $selectedYear = $year ?? Carbon::now()->year;

        // Find the earliest year with any habit logs
        $minDateStr = HabitLog::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->min('completed_at');
            
        $minYear = $minDateStr ? \Carbon\Carbon::parse($minDateStr)->year : null;

        $currentYear = Carbon::now()->year;

        // Build a list of available years
        if ($minYear === null) {
            $availableYears = [];
        } else {
            $availableYears = range((int) $minYear, $currentYear);
        }

        // Make sure the selected year is valid
        if (! empty($availableYears) && ! in_array($selectedYear, $availableYears)) {
            abort(404, 'Invalid year.');
        }

        // Get logs for the selected year
        $startDate = Carbon::create($selectedYear, 1, 1)->toDateString();
        $endDate = Carbon::create($selectedYear, 12, 31)->toDateString();

        $logCounts = HabitLog::query()
            ->where('user_id', Auth::id())
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->selectRaw('completed_at, COUNT(*) as total')
            ->groupBy('completed_at')
            ->pluck('total', 'completed_at');

        // Find the maximum count for scaling the chart
        $maxCount = $logCounts->max() ?? 1;

        // Generate the calendar grid
        $weeks = Habit::generateYearGrid($selectedYear);

        // Get total number of habits
        $totalHabits = Auth::user()->habits()->count();

        return view('habits.history', compact(
            'selectedYear',
            'availableYears',
            'logCounts',
            'maxCount',
            'weeks',
            'totalHabits'
        ));
    }

    // Get habits completed on a specific day (for history page popover)
    public function historyDay(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $date = Carbon::parse($request->get('date'))->toDateString();

        // Get all logs for this date
        $habits = HabitLog::with('habit')
            ->where('user_id', Auth::id())
            ->whereDate('completed_at', $date)
            ->get()
            ->map(fn ($log) => ['name' => $log->habit->name]);

        return response()->json($habits);
    }

    // Show the calendar page
    public function calendar()
    {
        // Get all habits for the current user
        $habits = Auth::user()->habits;

        return view('habits.calendar', compact('habits'));
    }

    // Get calendar events (for FullCalendar)
    public function calendarEvents(Request $request)
    {
        $habitId = $request->get('habit_id');

        // Get all logs for the user
        $query = HabitLog::with('habit')
            ->where('user_id', Auth::id());

        // Filter by habit if selected
        if ($habitId) {
            $query->where('habit_id', $habitId);
        }

        $logs = $query->get();

        // Format for FullCalendar
        $events = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'title' => $log->habit->name,
                'start' => Carbon::parse($log->completed_at)->toDateString(),
                'color' => '#22c55e',
            ];
        });

        return response()->json($events);
    }

    // Toggle a habit for a specific date (from calendar)
    public function calendarToggle(Request $request)
    {
        $validated = $request->validate([
            'habit_id' => 'required|exists:habits,id',
            'date' => 'required|date',
        ]);

        // Make sure the user owns this habit
        $habit = Habit::where('id', $validated['habit_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $date = Carbon::parse($validated['date']);
        $today = Carbon::today();

        // Don't allow marking future dates
        if ($date->greaterThan($today)) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível completar hábitos no futuro.',
            ], 422);
        }

        $dateStr = $date->toDateString();

        // Check if already completed on this date
        $log = HabitLog::where('habit_id', $habit->id)
            ->whereDate('completed_at', $dateStr)
            ->first();

        // Toggle: if exists, delete; otherwise create
        if ($log) {
            $log->delete();
        } else {
            HabitLog::create([
                'user_id' => Auth::id(),
                'habit_id' => $habit->id,
                'completed_at' => $dateStr,
            ]);
        }

        return response()->json(['success' => true]);
    }

    // Get habits with optional search filter (for dashboard)
    public function paginate(Request $request)
    {
        $search = $request->get('search', '');

        // Get habits for the current user
        $query = Habit::where('user_id', Auth::id())
            ->orderBy('name');

        // Apply search filter if provided
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Format habit data
        $habits = $query->get()->map(fn ($h) => [
            'id' => $h->id,
            'name' => $h->name,
            'wasCompletedToday' => $h->wasCompletedToday(),
            'streak' => $h->getCurrentStreak(),
        ]);

        return response()->json([
            'habits' => $habits,
            'all_count' => Auth::user()->habits()->count(),
            'total' => $habits->count(),
        ]);
    }
}
