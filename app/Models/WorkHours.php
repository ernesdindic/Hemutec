<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WorkHours extends Model
{
    use HasFactory;

     protected $table = 'workhours'; // Dodaj ovu liniju

    // Definišite atribute koji su masovno dodeljivi
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'break_time',
        'end_time',
        'description',
        'overtime_minutes', // dodaj polje za prekovremene sate
    ];

    public function calculateOvertime()
    {
        // Konvertuj vreme u lokalno vreme sa pravom vremenskom zonom
        $start = Carbon::parse($this->start_time)->setTimezone('Europe/Berlin');
        $end = Carbon::parse($this->end_time)->setTimezone('Europe/Berlin');
    
        // Proveri da li je pauza postavljena, inače je postavi na 0
        $breakMinutes = 0;
        if ($this->break_time) {
            $break = explode(':', $this->break_time);
            $breakMinutes = (int)$break[0] * 60 + (int)$break[1]; // Konvertuj pauzu u minute
        }
    
        // Izračunaj ukupno radno vreme u minutama
        $workedMinutes = $end->diffInMinutes($start);
    
        // Standardno radno vreme (510 minuta = 8 sati i 30 minuta)
        $standardMinutes = 510;
        $fullday = 1020  ;
    
        // Izračunaj prekovremene ili minus sate
        $overtimeMinutes = ($standardMinutes  - $workedMinutes - $fullday - $breakMinutes);
    
        // Vratiti vrednost prekovremenih minuta (može biti negativna)
        return $overtimeMinutes; 
    }
    

    










}
