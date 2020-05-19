<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_teacher', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('teacher_id');
            $table->boolean('is_admin')->default(false);
            $table->index(['school_id', 'teacher_id']);
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
        Schema::dropIfExists('school_teacher');
    }
}
