<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->after('id', function ($table) {
                $table->foreignId('company_id')->onUpdate('cascade')->nullOnDelete()
                    ->nullable()->constrained('companies');
                $table->foreignId('department_id')->onUpdate('cascade')->nullOnDelete()
                    ->nullable()->constrained('departments');
                $table->foreignId('rate_id')->onUpdate('cascade')->nullOnDelete()
                    ->nullable()->constrained('rates');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
