<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Response\Response;
use Illuminate\Http\Request;
use App\Models\MultipleChoiceOption;
use App\Models\MultipleChoiceQuestion;
use Illuminate\Support\Facades\Validator;

class MultipleChoiceQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Exam $exam, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => ['required'],
            'options' => ['required', 'array', 'size:5'],
            'weight' => ['required'],
            'correct_answer_id' => ['required', 'integer', 'between:0,4'],
        ]);

        if ($validator->fails())
            return Response::invalidField();

        if ($exam->multipleChoice()->sum("weight") + $request->weight > 100)
            return Response::error("Exam total weight exceed");

        $params = $request->only(['question', 'weight']);
        $params['exam_id'] = $exam->id;
        $multipleChoiceQuestion = MultipleChoiceQuestion::create($params);

        $option_ids = [];
        foreach ($request->options as $option) {
            $opt_id = $multipleChoiceQuestion->choices()->create(['text' => $option]);
            $option_ids[] = $opt_id->id;
        }

        $multipleChoiceQuestion->update([
            'correct_answer_id' => $option_ids[$request->correct_answer_id]
        ]);

        return Response::success("multiple question created successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MultipleChoiceQuestion  $multipleChoiceQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(MultipleChoiceQuestion $multipleChoiceQuestion)
    {
        //
    }
}
