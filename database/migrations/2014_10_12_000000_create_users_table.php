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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('second_name')->nullable(false);
            $table->string('email')->unique();
            $table->bigInteger('phone');
            $table->string('user_name')->nullable(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('company_name')->nullable(false);
            $table->string('gst_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->enum('role', ['admin', 'pos', 'userAccess'])->default('admin');
            $table->bigInteger('user_added_by')->nullable();
            $table->string('image_url')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('user_added_by')->references('id')->on('users')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
