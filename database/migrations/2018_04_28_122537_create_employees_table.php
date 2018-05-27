<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('employee_number')->nullable();
            $table->date('joining_date')->nullable();
            $table->integer('company_id')->unsigned();
            $table->integer('supervisior_id')->nullable();
            $table->integer('designation_id')->unsigned();
            $table->string('photo_id')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('contact')->nullable();
            $table->string('official_number')->nullable();
            $table->string('fathers_name')->nullable();
            $table->string('fathers_number')->nullable();
            $table->string('mothers_name')->nullable();
            $table->string('mothers_number')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('spouse_number')->nullable();
            $table->string('current_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('nid')->nullable();
            $table->string('passport')->nullable();
            $table->string('driving_licence')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->string('relation_emergency_contact')->nullable();
            $table->text('skills')->nullable();
            $table->text('education')->nullable();
            $table->text('experience')->nullable();
            $table->date('leaving_date')->nullable();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('employees');
    }
}
