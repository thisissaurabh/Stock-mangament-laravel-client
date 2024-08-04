<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suppliers_id');
            $table->unsignedBigInteger('user_add');
            $table->string('purchase_invoice_no')->unique();
            $table->string('challan_no');
            $table->date('invoice_date');
            $table->enum('coding_type', ['unique', 'lot']);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('suppliers_id')->references('id')->on('user_customers');
            $table->foreign('user_add')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
