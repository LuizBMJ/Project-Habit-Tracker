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
        'name'
        ];

    
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }


    public function habitLogs(): HasMany {
        return $this->hasMany(HabitLog::class);
    }

    public function wasCompletedToday(): bool {
        return $this->habitLogs()
            ->where('completed_at', Carbon::today()->toDateString())
            ->exists();
    }

    public function wasCompletedOn(Carbon $date): bool {
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

}