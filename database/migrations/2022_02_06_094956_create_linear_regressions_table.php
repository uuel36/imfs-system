<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinearRegressionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linear_regressions', function (Blueprint $table) {
            $table->id();
            $table->float('bud_injection_x1', 20, 2);
            $table->unsignedBigInteger('area_id');
            $table->date('bu_injection_date')->nullable();
            $table->float('bagging_report_x2', 20, 2)->nullable();
            $table->date('bagging_report_date')->nullable();
            $table->float('stem_cut_y', 20, 2)->nullable();
            $table->date('stem_cut_y_date')->nullable();
            $table->float('sum_X1', 20, 2)->nullable(); 
            $table->float('sum_X2', 20, 2)->nullable();
            $table->float('b1', 20, 2)->nullable();     
            $table->float('b2', 20, 2)->nullable();    
            $table->float('m1', 20, 2)->nullable();   
            $table->float('m2', 20, 2)->nullable();    
            $table->float('x1m1', 20, 2)->nullable();  
            $table->float('x2m2', 20, 2)->nullable();   
            $table->float('x1m12', 20, 2)->nullable();  
            $table->float('x2m22', 20, 2)->nullable();  
            $table->float('y', 20, 2)->nullable(); // New column for y value
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
        Schema::dropIfExists('linear_regressions');
    }
}
