<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable()->index();
            $table->foreign('parent_id')->references('id')->on('media')->onDelete('cascade');
            $table->string('file');
            $table->string('filename');
            $table->string('basename');
            $table->string('type', 5);
            $table->bigInteger('size')->unsigned()->nullable();
            $table->smallInteger('image_height')->unsigned()->nullable();
            $table->smallInteger('image_width')->unsigned()->nullable();
            $table->integer('image_style_id')->unsigned()->nullable()->index();
            $table->foreign('image_style_id')->references('id')->on('image_styles')->onDelete('cascade');
            $table->binary('data')->nullable();
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
        Schema::drop('media');
    }
}
