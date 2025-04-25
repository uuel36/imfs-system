<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requestorders', function (Blueprint $table) {
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
            $table->dateTime('date_time_recieved')->nullable();
            $table->unsignedBigInteger('is_approved')->default(0);
            $table->unsignedBigInteger('is_disapproved')->default(0);
            $table->unsignedBigInteger('is_quantity')->default(0);
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
        Schema::dropIfExists('requestorders');
    }
}
