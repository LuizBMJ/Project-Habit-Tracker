<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function habitLogs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    public function wasCompletedToday(): bool
    {
        return $this->habitLogs()
            ->where('completed_at', Carbon::today()->toDateString())
            ->exists();
    }

    public function wasCompletedOn(Carbon $date): bool
    {
        return $this->habitLogs()
            ->where('completed_at', $date->toDateString())
            ->exists();
    }

    public static function generateYearGrid(int $year): array
    {
        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);

        $weeks = [];
        $currentWeek = [];

        $firstDayOfWeek = $startDate->dayOfWeek;
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $currentWeek[] = null;
        }

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $currentWeek[] = $date->copy();

            if ($date->isSaturday() || $date->eq($endDate)) {
                $weeks[] = $currentWeek;
                $currentWeek = [];
            }
        }

        return $weeks;
    }

    /**
     * Calcula a sequência atual de dias consecutivos concluídos.
     */
    public function getCurrentStreak(): int
    {
        $logs = $this->habitLogs()
            ->orderBy('completed_at', 'desc')
            ->pluck('completed_at')
            ->map(fn ($date) => Carbon::parse($date)->startOfDay())
            ->values();

        if ($logs->isEmpty()) {
            return 0;
        }

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $lastLog = $logs->first();

        if (! $lastLog->equalTo($today) && ! $lastLog->equalTo($yesterday)) {
            return 0;
        }

        $streak = 0;
        $currentDate = $lastLog->copy();

        foreach ($logs as $logDate) {
            if ($logDate->equalTo($currentDate)) {
                $streak++;
                $currentDate->subDay();
            } elseif ($logDate->lessThan($currentDate)) {
                break;
            }
        }

        return $streak;
    }
}
