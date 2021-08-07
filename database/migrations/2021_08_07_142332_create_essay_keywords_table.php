<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEssayKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('essay_keywords', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('essay_id')->unsigned();
            $table->timestamps();

            $table->foreign('essay_id')->references('id')->on('essay_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('essay_keywords');
    }
}
