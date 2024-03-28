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
        Schema::create('log_merged_students', function (Blueprint $table) {
            $table->id();

            $table->string("current_student_fullname",20); 
            $table->integer("current_student_id");
            $table->string("current_student_phone",20); 
            $table->string("current_student_phone1",20)->nullable(); 
            $table->string("current_student_phone2",20)->nullable(); 
            $table->string("current_student_phone3",20)->nullable(); 
            $table->string("current_student_phone4",20)->nullable(); 
            $table->string("current_student_student_phone",20)->nullable(); 
            $table->string("current_student_mother_phone",20)->nullable(); 
            $table->string("current_student_father_phone",20)->nullable(); 
            
           
            $table->string("old_student_fullname",20); 
            $table->integer("old_student_id"); 
            $table->string("old_student_phone",20); 
            $table->string("old_student_phone1",20)->nullable(); 
            $table->string("old_student_phone2",20)->nullable();
            $table->string("old_student_phone3",20)->nullable(); 
            $table->string("old_student_phone4",20)->nullable(); 
            $table->string("old_student_student_phone",20)->nullable(); 
            $table->string("old_student_mother_phone",20)->nullable(); 
            $table->string("old_student_father_phone",20)->nullable(); 
            

            $table->integer("user_id_updater");
            $table->string("user_fullname_updater"); 

           
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_merged_students');
    }
};
