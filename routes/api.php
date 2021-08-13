<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EssayQuestionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\MultipleChoiceQuestionController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\StatisticController;

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::group(['middleware' => 'authorized-user'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('me', [AuthController::class, 'me']);
        Route::post('reset-password', [AuthController::class, 'reset']);

        Route::group(['middleware' => 'teacher'], function () {
            Route::resource('exam', ExamController::class)->only(['index', 'show', 'store', 'destroy']);
            Route::resource('exam/{exam}/multiple-choice', MultipleChoiceQuestionController::class)->only(['store']);
            Route::resource('exam/{exam}/essay', EssayQuestionController::class)->only(['store']);

            Route::get('exam/{exam}/student/{student}/answer', [ExamController::class, 'studentAnswer']);
            Route::post('exam/{exam}/student/{student}/assess', [ExamController::class, 'processExamResult']);
        });

        Route::group(['middleware' => 'student'], function () {
            Route::get('exam-by-class', [ExamController::class, 'examByClass']);
            Route::get('exam/{exam}/questions', [ExamController::class, 'examQuestions']);
            Route::post('exam/{exam}/submit-answer', [ExamController::class, 'submitAnswer']);

            Route::get('score', [ScoreController::class, 'index']);

            Route::get('statistic', [StatisticController::class, 'index']);
        });
    });
});
