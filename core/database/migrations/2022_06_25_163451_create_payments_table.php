<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('trnx_id')->nullable();
            $table->string('gateway')->nullable();
            $table->text('payment_details')->nullable();
            $table->string('item_model')->comment('model class');
            $table->text('item_info')->nullable();
            $table->text('order_model')->nullable();
            $table->text('order_info')->nullable();
            $table->decimal('amount', 10);
            $table->string('currency', 10)->nullable();
            $table->integer('status')->default(0)->comment('0 = pending, 1 = completed, 2 = failed');
            $table->string('invoice')->nullable();
            $table->string('invoice_id')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
