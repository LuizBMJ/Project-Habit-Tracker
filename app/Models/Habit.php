<?php

namespace App\Models;

// This is the Habit model - represents a habit or task the user wants to track
// Each habit belongs to a user and has many logs (records of completion)

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    // Use this trait to create habits in tests
    use HasFactory;

    // Fields that can be filled when creating a habit
    protected $fillable = [
        'user_id',
        'name',
    ];

    // Relationship: Get the user who owns this habit
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Get all completion logs for this habit
    public function habitLogs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    // Check if this habit was completed today
    public function wasCompletedToday(): bool
    {
        if ($this->relationLoaded('habitLogs')) {
            return $this->wasCompletedTodayFromLoaded();
        }
        return $this->habitLogs()
            ->where('completed_at', Carbon::today()->toDateString())
            ->exists();
    }

    // Check if this habit was completed on a specific date
    public function wasCompletedOn(Carbon $date): bool
    {
        return $this->habitLogs()
            ->where('completed_at', $date->toDateString())
            ->exists();
    }

    // Create an array representing the calendar grid for a year
    // Returns an array of weeks, where each week is an array of days
    public static function generateYearGrid(int $year): array
    {
        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);

        $weeks = [];
        $currentWeek = [];

        // Add empty days at the start of the first week
        $firstDayOfWeek = $startDate->dayOfWeek;
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $currentWeek[] = null;
        }

        // Loop through each day of the year
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $currentWeek[] = $date->copy();

            // Start a new week on Saturday or at the end of the year
            if ($date->isSaturday() || $date->eq($endDate)) {
                $weeks[] = $currentWeek;
                $currentWeek = [];
            }
        }

        return $weeks;
    }

    // Calculate the current streak - number of consecutive days completed
    public function getCurrentStreak(): int
    {
        if ($this->relationLoaded('habitLogs')) {
            return $this->getCurrentStreakFromLoaded();
        }
        // Get all completion dates, newest first
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

        // If the last completion wasn't today or yesterday, streak is 0
        if (! $lastLog->equalTo($today) && ! $lastLog->equalTo($yesterday)) {
            return 0;
        }

        $streak = 0;
        $currentDate = $lastLog->copy();

        // Count consecutive days
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

    public function wasCompletedTodayFromLoaded(): bool
    {
        $today = Carbon::today()->toDateString();
        return $this->habitLogs->contains(fn ($log) => $log->completed_at === $today);
    }

    public function getCurrentStreakFromLoaded(): int
    {
        $logs = $this->habitLogs
            ->map(fn ($log) => Carbon::parse($log->completed_at)->startOfDay())
            ->sortByDesc('timestamp')
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
