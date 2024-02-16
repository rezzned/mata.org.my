<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('nation')->nullable();
            $table->string('idcard_no')->nullable();
            $table->string('personal_phone')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_country')->nullable();
            $table->decimal('current_income', 10)->nullable();
            $table->string('company_address')->nullable();
            $table->decimal('cpd_point', 10)->default(0);
            $table->string('membership_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
            $table->dropColumn('age');
            $table->dropColumn('gender');
            $table->dropColumn('nation');
            $table->dropColumn('idcard_no');
            $table->dropColumn('personal_phone');
            $table->dropColumn('company_phone');
            $table->dropColumn('company_email');
            $table->dropColumn('company_city');
            $table->dropColumn('company_state');
            $table->dropColumn('company_country');
            $table->dropColumn('current_income', 10);
            $table->dropColumn('company_address');
            $table->dropColumn('cpd_point');
            $table->dropColumn('membership_id');
        });
    }
}
