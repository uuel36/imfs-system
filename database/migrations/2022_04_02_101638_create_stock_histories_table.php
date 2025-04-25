<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deploy_id')->nullable();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->float('quantity', 20, 2);
            $table->string('area')->nullable();
            $table->unsignedBigInteger('leadman_id')->nullable();
            $table->dateTime('date');
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
        Schema::dropIfExists('stock_histories');
    }
}
