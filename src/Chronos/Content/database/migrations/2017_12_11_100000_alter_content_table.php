<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterContentTable extends Migration
{
    /**
     * Run the database.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('content', function (Blueprint $table) {
            $table->timestamp('status_scheduled')->nullable()->after('status');
        });
    }

    /**
     * Reverse the database.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content', function ($table) {
            $table->dropColumn('status_scheduled');
        });
    }
}
