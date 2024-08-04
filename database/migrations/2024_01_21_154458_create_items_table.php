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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('brand_id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('design_name');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('hsn_code');
            $table->integer('quantity');
            $table->string('unit');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('tax', 5, 2)->default(0);
            $table->decimal('sell_mrp', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('item_image')->nullable();
            $table->timestamps();
            $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('brand_id')->references('id')->on('groups_brand');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
