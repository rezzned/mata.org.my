<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceColumnsToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('stand_price', 11, 2)->nullable()->after('current_price');
            $table->decimal('assoc_price', 11, 2)->nullable()->after('current_price');
        });

        DB::table('courses')->update([
            'assoc_price' => DB::raw('courses.current_price'),
            'stand_price' => DB::raw('courses.current_price')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('assoc_price');
            $table->dropColumn('stand_price');
        });
    }
}
