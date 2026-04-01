<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Um usuario pode ter muitos hábitos

    public function habits(): HasMany {
        return $this->hasMany(Habit::class);
    }

    // Um usuario pode ter muitos registros

    public function habitLogs(): HasMany {
        return $this->hasMany(HabitLog::class);
    }
}