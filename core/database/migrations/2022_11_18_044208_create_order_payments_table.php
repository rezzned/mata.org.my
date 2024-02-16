<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->bigInteger('model_id');
            $table->string('order_id')->comment('unique');
            $table->text('order_data')->nullable();
            $table->string('item_model');
            $table->bigInteger('item_id')->nullable();
            $table->decimal('amount')->default(0);
            $table->string('trx_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('payment_data')->nullable();
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('order_payments');
    }
}
