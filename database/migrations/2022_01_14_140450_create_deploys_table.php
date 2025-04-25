<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeploysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deploys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('area_id');
            $table->integer('quantity');
            $table->unsignedBigInteger('leadman_id');
            $table->unsignedBigInteger('remaining_quantity');
            $table->unsignedBigInteger('is_returned')->default(0);
            $table->dateTime('date_returned')->nullable();
            $table->dateTime('date');
            $table->unsignedBigInteger('is_approved')->default(0);
            $table->dateTime('date_time_recieved')->nullable();
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
        Schema::dropIfExists('deploys');
    }
}
