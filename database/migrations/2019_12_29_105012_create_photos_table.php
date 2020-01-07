<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable()->comment('photo name');
            $table->string('mime_type')->nullable()->comment('mime type');
            $table->string('size')->nullable()->comment('size');
            $table->string('original_file_path', 255)->nullable()->comment('original file path');
            $table->string('base_name')->nullable()->comment('base name without mime type');
            $table->string('full_name')->nullable()->comment('base name without mime type');
            $table->json('thumbnails')->nullable()->comment('thumbnails');
            $table->unsignedBigInteger('album_id')->comment('related album');
            $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
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
        Schema::dropIfExists('photos');
    }
}
