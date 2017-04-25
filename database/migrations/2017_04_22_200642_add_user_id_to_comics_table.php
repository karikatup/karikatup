<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdToComicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comics', function(Blueprint $table){
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comics', function(Blueprint $table){
            $table->dropForeign("comics_user_id_foreign");
            $table->dropColumn("user_id");
        });
    }
}
