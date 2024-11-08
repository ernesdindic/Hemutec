<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('reports', function (Blueprint $table) {
        $table->string('opis_rada')->nullable(); // Dodajemo kolonu opis_rada
    });
}

public function down()
{
    Schema::table('reports', function (Blueprint $table) {
        $table->dropColumn('opis_rada'); // Uklanjamo kolonu ako migraciju vratimo
    });
}

};