<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryStudentMultipleChoiceAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_student_multiple_choice_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id')->unsigned();
            $table->integer('option_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('multiple_choice_questions');
            $table->foreign('option_id')->references('id')->on('multiple_choice_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_student_multiple_choice_answers');
    }
}
