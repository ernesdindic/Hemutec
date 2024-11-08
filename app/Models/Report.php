<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Report extends Model
{
    // Definiši fillable atribute
    protected $fillable = [
        'datum',
        'vrijeme_rada',
        'ime_stranke',
        'opis_rada',
        'tip_posla',
        'selectline',
        'user_id', // Dodajte user_id u fillable
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
