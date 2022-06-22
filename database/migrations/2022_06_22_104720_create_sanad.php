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
        if (!Schema::hasTable('sanads')) {
            Schema::create('sanads', function (Blueprint $table) {
                $table->id();
                $table->string('number');
                $table->string('description');
                $table->integer('total');
                $table->integer('supporter_percent');
                $table->integer('supporter_id');
                $table->integer('user_id');
                $table->enum('status', ['created', 'paid']);
                $table->integer('type')->default(1);
                $table->integer('total_cost');
                $table->timestamps();
                
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sanads');
    }
};
