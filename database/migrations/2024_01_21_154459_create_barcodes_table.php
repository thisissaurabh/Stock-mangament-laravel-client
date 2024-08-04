<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarcodesTable extends Migration
{
    public function up()
    {
        Schema::create('barcodes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('code');
            $table->string('image_url');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    public function down()
    {
        Schema::dropIfExists('barcodes');
    }
}
