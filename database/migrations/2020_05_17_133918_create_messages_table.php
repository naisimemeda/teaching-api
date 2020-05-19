<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('send_id');
            $table->string('send_type', 60);
            $table->text('message');
            $table->unsignedBigInteger('receive_id');
            $table->string('receive_type', 60);
            $table->index(['send_id', 'send_type']);
            $table->index(['receive_id', 'receive_type']);
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
        Schema::dropIfExists('messages');
    }
}
