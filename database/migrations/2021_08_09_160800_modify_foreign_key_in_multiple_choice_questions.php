<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyForeignKeyInMultipleChoiceQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            $table->dropForeign(['correct_answer_id']);

            $table->foreign('correct_answer_id')->references('id')->on('multiple_choice_options')->onDelete('cascade');
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
