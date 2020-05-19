<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLineTeacherStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('line_name')->nullable();
            $table->string('line_avatar_url')->nullable();
            $table->string('line_id', 60)->nullable()->index();
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('line_name')->nullable();
            $table->string('line_avatar_url')->nullable();
            $table->string('line_id', 60)->index()->nullable();
            $table->string('avatar_url', 150)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['line_id', 'line_avatar_url', 'line_name']);
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['line_id', 'avatar_url', 'line_avatar_url', 'line_name']);
        });
    }
}
