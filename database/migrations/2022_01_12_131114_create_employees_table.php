<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('middlename')->nullable();
            $table->string('suffix')->nullable();
            $table->date('birthday');
            $table->string('gender');
            $table->string('contact');
            $table->unsignedBigInteger('position_id');
            $table->string('sss')->nullable();
            $table->string('philhealth')->nullable();
            $table->text('address');
            $table->text('profile')->nullable();
            $table->boolean('is_contribution')->default(true);
            $table->boolean('deduction')->nullable();
            $table->float('salary', 20, 2)->nullable();
            $table->text('profile_path')->nullable();
            $table->text('profile_name')->nullable();
            $table->string('qrcode')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
