<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Ako koristiš verifikaciju, uključi ovo
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Report;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

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
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the reports associated with the user.
     */
    public function reports()
    {
        return $this->hasMany(Report::class); // Proverite da li je Report model ispravno definisan
    }

    
    public function workhours()
{
    return $this->hasMany(WorkHours::class, 'user_id');
}
}
