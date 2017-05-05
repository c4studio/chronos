<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageStylesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_styles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('height')->unsigned()->nullable();
            $table->integer('width')->unsigned()->nullable();
            $table->integer('crop_height')->unsigned()->nullable();
            $table->integer('crop_width')->unsigned()->nullable();
            $table->string('crop_type')->default('crop');
            $table->enum('anchor_h', ['left', 'center', 'right'])->default('center');
            $table->enum('anchor_v', ['bottom', 'middle', 'top'])->default('middle');
            $table->boolean('upsizing')->default(0);
            $table->smallInteger('rotate')->unsigned()->default(0);
            $table->boolean('greyscale')->default(0);
            $table->boolean('cloak')->default(0);
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
        Schema::dropIfExists('image_styles');
    }
}
