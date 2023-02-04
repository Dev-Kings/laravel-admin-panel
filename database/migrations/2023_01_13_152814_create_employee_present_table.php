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
        Schema::create('employee_present', function (Blueprint $table) {
            $table->foreignId('employee_id')->onUpdate('cascade')->nullOnDelete()
                    ->nullable()->constrained('employees');
            $table->foreignId('present_id')->onUpdate('cascade')->nullOnDelete()
                    ->nullable()->constrained('presents');
            $table->decimal('total')->default(0.0);
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_present');
    }
};
