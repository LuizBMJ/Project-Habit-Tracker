<?php

namespace App\Http\Controllers;

use App\Http\Requests\HabitRequest;
use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HabitControler extends Controller
{

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
        if($habit->user_id !== Auth::user()->id) {
            abort(403);
        }

        return view('habits.edit', compact('habit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HabitRequest $request, Habit $habit)
    {
        if($habit->user_id !== Auth::user()->id) {
            abort(403);
        };

        $habit->update($request->all());

        return redirect()
            ->route('habits.index')
            ->with('success', 'Hábito atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Habit $habit)
    {
        if($habit->user_id !== Auth::user()->id) {
            abort(403);
        }

        $habit->delete();  
        
        return redirect()
            ->route('habits.index')
            ->with('success', 'Hábito deletado com sucesso!');
    }

    public function settings() {
        $habits = Auth::user()->habits;
    
        return view('habits.settings', compact('habits'));
    }

    public function toggle(Habit $habit) {
        // 1 - Verifica se o hábito pertence ao usuário autenticado
        if($habit->user_id !== Auth::user()->id) {
            abort(403);
        }
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
            $message = 'Hábito desmarcado.';
        } else {
            // 5 - Se não existir, criar o registro
            HabitLog::create([
                'user_id' => Auth::user()->id,
                'habit_id' => $habit->id,
                'completed_at' => $today
            ]);

            $message = 'Hábito concluído.';
        }
        
        // 6 - Retornar para a pagina anterior
        return redirect()
            ->route('habits.index')
            ->with('success', $message);
    }
}