<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Chat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
          Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('other_user_id');
            $table->longText('message');
            $table->integer('group_id')->nullable();
            $table->tinyInteger('is_read')->define(0);
            $table->string('time');
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
        //
    }
}
