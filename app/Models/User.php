<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Arr;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'departments',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        $filtered = Arr::except($this->fillable, [3]);

        return LogOptions::defaults()
            ->logOnly($filtered)
            ->setDescriptionForEvent(fn(string $eventName) => "This user info has been {$eventName}");
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'banned_at' => 'datetime',
            'disabled_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'departments' => 'array',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user is online record associated with the user.
     */
    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    /**
     * Get the agent record associated with the user.
     */
    public function agent()
    {
        return $this->hasOne(Agent::class);
    }
}
