<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('leave_type_id')->unsigned();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->integer('number_of_days')->nullable();
            $table->integer('employee_id')->unsigned();
            $table->text('leave_reason')->nullable();
            $table->integer('remaining_days')->nullable();
            $table->string('status')->nullable();
            $table->integer('created_by')->unsigned();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('leave_requests');
    }
}
