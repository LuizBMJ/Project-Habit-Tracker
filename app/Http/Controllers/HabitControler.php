<?php

namespace App\Http\Controllers;

use App\Http\Requests\HabitRequest;
use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HabitControler extends Controller
{
    use AuthorizesRequests;

    public function index(): View {

        $habits = Auth::user()->habits()
            ->with('habitLogs')
            ->get();
        return view('dashboard', compact('habits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('habits.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HabitRequest $request)
    {
        $validated = $request->validated();

        Auth::user()->habits()->create($validated);

        return redirect()
            ->route('habits.index')
            ->with('success', 'Hábito criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Habit $habit)
    {
        $this->authorize('update', $habit);

        return view('habits.edit', compact('habit'))->with('success', 'Hábito criado com sucesso!');;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HabitRequest $request, Habit $habit)
    {
        $this->authorize('update', $habit);

        $habit->update($request->validated()); // was $request->all()

        return redirect()
            ->route('habits.index')
            ->with('success', 'Hábito atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Habit $habit)
    {
        $this->authorize('delete', $habit);

        $habit->delete();  
        
        return redirect()
            ->route('habits.index')
            ->with('warning', 'Hábito deletado com sucesso!');
    }

    public function settings() {
        $habits = Auth::user()->habits;
    
        return view('habits.settings', compact('habits'));
    }

    public function toggle(Habit $habit) {
        // 1 - Verifica se o hábito pertence ao usuário autenticado
        $this->authorize('toggle', $habit);
        // 2 - Pegar a data de hoje
        $today = Carbon::today()->toDateString();

        // 2.1 - Pegar o log
        $log = HabitLog::query()
        ->where('habit_id', $habit->id)
        ->where('completed_at', $today)
        ->first();
        
        // 3 - Verificar se nessa data ja existe um registro
        if($log) {
            // 4 - Se existir, remover o registro
            $log->delete();
            $alert = 'warning';
            $message = 'Hábito desmarcado.';
        } else {
            // 5 - Se não existir, criar o registro
            HabitLog::query()
                ->create([
                    'user_id' => Auth::user()->id,
                    'habit_id' => $habit->id,
                    'completed_at' => $today
                ]);

            $alert = 'success';
            $message = 'Hábito concluído.';
        }
        
        // 6 - Retornar para a pagina anterior
        return redirect()
            ->route('habits.index')
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

        if (!empty($availableYears) && !in_array($selectedYear, $availableYears)) {
            abort(404, 'Ano inválido.');
        }

        $startDate = Carbon::create($selectedYear, 1, 1)->toDateString();
        $endDate   = Carbon::create($selectedYear, 12, 31)->toDateString();

        // Count completions per day
        $logCounts = HabitLog::query()
            ->where('user_id', Auth::id())
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->selectRaw('completed_at, COUNT(*) as total')
            ->groupBy('completed_at')
            ->pluck('total', 'completed_at'); // ['2025-01-03' => 4, ...]

        $maxCount = $logCounts->max() ?? 1;

        $weeks = \App\Models\Habit::generateYearGrid($selectedYear);

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
            ->map(fn($log) => ['name' => $log->habit->name]);

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
            'date' => 'required|date'
        ]);

        $habit = Habit::where('id', $validated['habit_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $date = Carbon::parse($validated['date'])->toDateString();

        $log = HabitLog::where('habit_id', $habit->id)
            ->whereDate('completed_at', $date)
            ->first();

        if ($log) {
            $log->delete();
        } else {
            HabitLog::create([
                'user_id' => Auth::id(),
                'habit_id' => $habit->id,
                'completed_at' => $date
            ]);
        }

        return response()->json(['success' => true]);
    }
}