<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->date('datum');
        $table->decimal('vrijeme_rada', 5, 2)->change();
        $table->string('ime_stranke');
        $table->enum('tip_posla', ['produktiv', 'neproduktivan', 'interni posao']);
        $table->boolean('selectline')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
