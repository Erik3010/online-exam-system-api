<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryStudentEssayAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_student_essay_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->text('answer');
            $table->integer('essay_question_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->timestamps();

            $table->foreign('essay_question_id')->references('id')->on('essay_questions');
            $table->foreign('student_id')->references('id')->on('students');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_student_essay_answers');
    }
}
