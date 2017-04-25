<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuthorIdToComicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comics', function(Blueprint $table){
            $table->integer('author_id')->unsigned()->nullable();
            $table->foreign('author_id')
                ->references('id')->on('authors')
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
            $table->dropForeign("comics_author_id_foreign");
            $table->dropColumn("author_id");
        });
    }
}
