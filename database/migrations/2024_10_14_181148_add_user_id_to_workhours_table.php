<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToWorkhoursTable extends Migration
{
    public function up()
    {
        Schema::table('workhours', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');

            // Add a foreign key constraint if you have a users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('workhours', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['user_id']);
            // Drop the user_id column
            $table->dropColumn('user_id');
        });
    }
}
