<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->enum('customer_type', ['business', 'individual'])->default('business');
            $table->enum('user_customer_type', ['customer', 'supplier']);
            $table->string('first_name');
            $table->string('second_name');
            $table->string('company_name');
            $table->string('mail');
            $table->bigInteger('phone');
            $table->string('work')->nullable();
            $table->text('other_details')->nullable();
            $table->string('gst_no')->nullable();
            $table->text('company_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->unsigned()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_customers');
    }
}
