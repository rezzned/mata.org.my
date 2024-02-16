<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpdExternalPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpd_external_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount');
            $table->string('trx_type', 1)->comment('+/-');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('training_title');
            $table->string('organized_by');
            $table->string('certificate')->nullable();
            $table->text('details')->nullable();
            $table->string('remarks')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending, 1=approved, 2=rejected');
            $table->timestamps();
            $table->foreign('user_id', 'cpd_ex_trx_fk')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cpd_external_points');
    }
}
