<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('task_id')->unsigned();
            $table->timestamps();
/*
            $table->index('user_id');
            $table->foreign('user_id')->references('users')->on('id')->onDelete('cascade');

            $table->index('task_id');
            $table->foreign('task_id')->references('tasks')->on('id')->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_invitations');
    }
}
