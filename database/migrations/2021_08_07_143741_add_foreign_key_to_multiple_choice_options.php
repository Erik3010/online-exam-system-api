<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToMultipleChoiceOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('multiple_choice_options', function (Blueprint $table) {
            $table->foreign('multiple_choice_id')->references('id')->on('multiple_choice_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('multiple_choice_options', function (Blueprint $table) {
            $table->dropForeign(['multiple_choice_id']);
        });
    }
}
