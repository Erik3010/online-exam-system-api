<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultipleChoiceQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiple_choice_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('question');
            $table->integer('weight');
            $table->integer('exam_id')->unsigned();
            $table->integer('correct_answer_id')->unsigned();
            $table->timestamps();

            $table->foreign('exam_id')->references('id')->on('exams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('multiple_choice_questions');
    }
}
