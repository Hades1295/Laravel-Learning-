<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomerInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_data_table', function (Blueprint $table) {
            $table->id();
            $table->string('name', 500)->nullable();
            $table->string('middle', 255)->nullable();  
            $table->string('lastname', 255)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('email', 500)->nullable();
            $table->string('dob', 255)->nullable();  
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
