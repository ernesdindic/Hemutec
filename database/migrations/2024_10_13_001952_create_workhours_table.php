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
        if (!Schema::hasTable('workhours')) {  // Proveri da li tabela već postoji
            Schema::create('workhours', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('date');
                $table->time('start_time');
                $table->time('break_time')->nullable();
                $table->time('end_time');
                $table->text('description')->nullable();
                $table->timestamps();
    
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
    

};
