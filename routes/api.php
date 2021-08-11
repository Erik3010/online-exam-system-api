<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EssayQuestionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\MultipleChoiceQuestionController;
use App\Http\Controllers\StudentMultipleChoiceAnswerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

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
    });
});
