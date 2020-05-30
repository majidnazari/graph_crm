<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStudentsTableAddExtra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->string('introducing', 255)->nullable();
            $table->string('student_phone', 20)->nullable();
            $table->integer('sources_id')->default(0);
            $table->integer('citys_id')->default(0);
            $table->integer('users_id');
            $table->integer('supporters_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            //
        });
    }
}
