<?php

namespace App\Http\Controllers;

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
    use AuthorizesRequests;

    public function index(): View
    {

        $habits = Auth::user()->habits()
            ->with('habitLogs')
            ->get();
        $habitCount = $habits->count();

        return view('dashboard', compact('habits', 'habitCount'));
    }

    public function create(): View
    {
        $limitReached = Auth::user()->habits()->count() >= 10;

        return view('habits.create', compact('limitReached'));
    }

    public function store(HabitRequest $request)
    {
        $count = Auth::user()->habits()->count();
        if ($count >= 10) {
            return redirect()
                ->route('dashboard.habits.index')
                ->with('error', 'Limite de 10 hábitos atingido.');
        }

        $validated = $request->validated();
        Auth::user()->habits()->create($validated);

        if ($count + 1 === 10) {
            return redirect()
                ->route('dashboard.habits.index')
                ->with('warning', 'Você atingiu o limite de hábitos');
        }

        return redirect()
            ->route('dashboard.habits.index')
            ->with('success', 'Hábito criado com sucesso!');
    }

    public function edit(Habit $habit)
    {
        $this->authorize('update', $habit);

        return view('habits.edit', compact('habit'))->with('success', 'Hábito criado com sucesso!');
    }

    public function update(HabitRequest $request, Habit $habit)
    {
        $this->authorize('update', $habit);

        $habit->update($request->validated());

        return redirect()
            ->route('dashboard.habits.settings')
            ->with('success', 'Hábito atualizado com sucesso!');
    }

    public function destroy(Habit $habit)
    {
        $this->authorize('delete', $habit);

        $habit->delete();

        return redirect()
            ->route('dashboard.habits.index')
            ->with('warning', 'Hábito deletado com sucesso!');
    }

    public function settings()
    {
        $habits = Auth::user()->habits;

        return view('habits.settings', compact('habits'));
    }

    public function toggle(Request $request, Habit $habit)
    {
        $this->authorize('toggle', $habit);

        $today = Carbon::today()->toDateString();

        $log = HabitLog::query()
            ->where('habit_id', $habit->id)
            ->where('completed_at', $today)
            ->first();

        if ($log) {
            $log->delete();
            $completed = false;
            $alert = 'warning';
            $message = 'Hábito desmarcado.';
        } else {
            HabitLog::query()->create([
                'user_id' => Auth::id(),
                'habit_id' => $habit->id,
                'completed_at' => $today,
            ]);
            $completed = true;
            $alert = 'success';
            $message = 'Hábito concluído.';
        }

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

    public function history(?int $year = null): View
    {
        $selectedYear = $year ?? Carbon::now()->year;

        $minYear = HabitLog::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->selectRaw('MIN(YEAR(completed_at)) as min_year')
            ->value('min_year');

        $currentYear = Carbon::now()->year;

        if ($minYear === null) {
            $availableYears = [];
        } else {
            $availableYears = range((int) $minYear, $currentYear);
        }

        if (! empty($availableYears) && ! in_array($selectedYear, $availableYears)) {
            abort(404, 'Ano inválido.');
        }

        $startDate = Carbon::create($selectedYear, 1, 1)->toDateString();
        $endDate = Carbon::create($selectedYear, 12, 31)->toDateString();

        $logCounts = HabitLog::query()
            ->where('user_id', Auth::id())
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->selectRaw('completed_at, COUNT(*) as total')
            ->groupBy('completed_at')
            ->pluck('total', 'completed_at');

        $maxCount = $logCounts->max() ?? 1;

        $weeks = Habit::generateYearGrid($selectedYear);

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

    public function historyDay(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $date = Carbon::parse($request->get('date'))->toDateString();

        $habits = HabitLog::with('habit')
            ->where('user_id', Auth::id())
            ->whereDate('completed_at', $date)
            ->get()
            ->map(fn ($log) => ['name' => $log->habit->name]);

        return response()->json($habits);
    }

    public function calendar()
    {
        $habits = Auth::user()->habits;

        return view('habits.calendar', compact('habits'));
    }

    public function calendarEvents(Request $request)
    {
        $habitId = $request->get('habit_id');

        $query = HabitLog::with('habit')
            ->where('user_id', Auth::id());

        if ($habitId) {
            $query->where('habit_id', $habitId);
        }

        $logs = $query->get();

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

    public function calendarToggle(Request $request)
    {
        $validated = $request->validate([
            'habit_id' => 'required|exists:habits,id',
            'date' => 'required|date',
        ]);

        $habit = Habit::where('id', $validated['habit_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $date = Carbon::parse($validated['date']);
        $today = Carbon::today();

        if ($date->greaterThan($today)) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível completar hábitos em datas futuras.',
            ], 422);
        }

        $dateStr = $date->toDateString();

        $log = HabitLog::where('habit_id', $habit->id)
            ->whereDate('completed_at', $dateStr)
            ->first();

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

    public function paginate(Request $request)
    {
        $search = $request->get('search', '');

        $query = Habit::where('user_id', Auth::id())
            ->orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

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
