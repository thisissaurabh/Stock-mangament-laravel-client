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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('group_name');
            $table->string('hsn_sac_code')->nullable();
            $table->decimal('cgst', 5, 2)->nullable();
            $table->decimal('sgst', 5, 2)->nullable();
            $table->decimal('igst', 5, 2)->nullable();
            $table->decimal('cess', 5, 2)->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
