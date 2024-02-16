<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->decimal('cpd_points')->nullable();
        });
        Schema::table('event_details', function (Blueprint $table) {
            $table->tinyInteger('completed')->default(0);
            $table->decimal('cpd_points')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('cpd_points');
        });
        Schema::table('event_details', function (Blueprint $table) {
            $table->dropColumn('completed');
            $table->dropColumn('cpd_points');
        });
    }
}
