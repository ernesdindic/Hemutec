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
        Schema::table('workhours', function (Blueprint $table) {
            $table->integer('overtime_minutes')->default(0)->after('end_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workhours', function (Blueprint $table) {
            $table->dropColumn('overtime_minutes');
        });
    }
};
