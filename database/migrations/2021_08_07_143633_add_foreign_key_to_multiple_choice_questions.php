<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToMultipleChoiceQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            $table->foreign('correct_answer_id')->references('id')->on('multiple_choice_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            //
        });
    }
}
