<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceColumnsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('assoc_price', 11, 2)->default(0.00)->after('current_price');
            $table->decimal('stand_price', 11, 2)->default(0.00)->after('current_price');
        });

        DB::table('products')->update([
            'assoc_price' => DB::raw('products.current_price'),
            'stand_price' => DB::raw('products.current_price')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('assoc_price');
            $table->dropColumn('stand_price');
        });
    }
}
