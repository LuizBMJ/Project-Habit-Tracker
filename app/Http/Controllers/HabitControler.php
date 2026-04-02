<?php

namespace App\Http\Controllers;

use App\Http\Requests\HabitRequest;
use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HabitControler extends Controller
{

    public function index(): View {

        $habits = auth('web')->user()->habits;
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

        auth('web')->user()->habits()->create($validated);

        return redirect()
            ->route('habits.index')
            ->with('success', 'Hábito criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Habit $habit)
    {
        if($habit->user_id !== auth('web')->user()->id) {
            abort(403);
        }

        return view('habits.edit', compact('habit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HabitRequest $request, Habit $habit)
    {
        if($habit->user_id !== auth('web')->user()->id) {
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
        if($habit->user_id !== auth('web')->user()->id) {
            abort(403);
        }

        $habit->delete();  
        
        return redirect()
            ->route('habits.index')
            ->with('success', 'Hábito deletado com sucesso!');
    }

    public function settings() {
        $habits = auth('web')->user()->habits;
    
        return view('habits.settings', compact('habits'));
    }
}