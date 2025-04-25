<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('area_id');
            $table->unsignedBigInteger('team_id');
            $table->json('team_members')->nullable();
            $table->unsignedBigInteger('leadman_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->date('date');
            $table->float('data', 20, 2)->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_reports');
    }
}
