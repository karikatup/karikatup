<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddComicIdToUserLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_likes', function(Blueprint $table){
            $table->integer('comic_id')->unsigned();
            $table->foreign('comic_id')
                ->references('id')->on('comics')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_likes', function(Blueprint $table){
            $table->dropForeign("user_likes_comic_id_foreign");
            $table->dropColumn("comic_id");
        });
    }
}
