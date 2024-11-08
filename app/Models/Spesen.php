<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spesen extends Model
{
    use HasFactory;
    use HasFactory;

    protected $table = 'spesen'; // Dodajemo ime tabele

    protected $fillable = ['user_id', 'datum', 'standort', 'kilometer', 'parkgebuehr'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
