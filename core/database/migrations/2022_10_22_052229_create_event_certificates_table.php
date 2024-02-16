<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_detail_id');
            $table->unsignedBigInteger('event_id');
            $table->string('event_name')->nullable();
            $table->date('event_date')->nullable();
            $table->string('event_venue')->nullable();
            $table->string('participant_name')->nullable();
            $table->string('ic_number')->nullable();
            $table->decimal('cpd_point')->nullable();
            $table->integer('certificate_number');
            $table->date('certificate_date');
            $table->string('certificate_file')->nullable();
            $table->string('short_form', 50)->nullable();
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
        Schema::dropIfExists('event_certificates');
    }
}
