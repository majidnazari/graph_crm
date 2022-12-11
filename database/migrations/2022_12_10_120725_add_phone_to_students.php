<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('phone1',20)->nullable()->after("phone");
            $table->string('phone2',20)->nullable()->after("phone");
            $table->string('phone3',20)->nullable()->after("phone");
            $table->string('phone4',20)->nullable()->after("phone");
            $table->string('phone5',20)->nullable()->after("phone");
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('phone1');
            $table->dropColumn('phone2');
            $table->dropColumn('phone3');
            $table->dropColumn('phone4');
            $table->dropColumn('phone5');
            
        });
    }
};
