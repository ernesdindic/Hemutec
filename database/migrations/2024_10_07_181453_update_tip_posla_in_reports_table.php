<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Modify 'tip_posla' to include new job types
            $table->enum('tip_posla', [
                'produktiv', 
                'neproduktivan', 
                'interni posao', 
                'interno produktivan', 
                'telefonsko produktivan', 
                'telefonsko neproduktivan', 
                'pauza', 
                'weiterbildung', 
                'anderes', 
                'e-mails', 
                'hemutec procesi', 
                'fahrt'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Rollback to the original job types
            $table->enum('tip_posla', [
                'produktiv', 
                'neproduktivan', 
                'interni posao'
            ])->change();
        });
    }
};
