<?php

namespace App\Http\Controllers;

use App\Models\EssayQuestion;
use App\Models\Exam;
use App\Response\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EssayQuestionController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
            'keywords' => ['required'],
            'weight' => ['required']
        ]);

        if ($validator->fails()) {
            return Response::invalidField();
        }

        if ($exam->essay()->sum('weight') + $request->weight > 100) {
            return Response::error('Exam total weight exceed');
        }

        $params = $request->only(['question', 'weight']);
        $params['exam_id'] = $exam->id;
        $essayQuestion = EssayQuestion::create($params);

        foreach (explode('|', $request->keywords) as $keyword) {
            $essayQuestion->keywords()->create(['keyword' => $keyword]);
        }

        return Response::success('Essay question successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(EssayQuestion $essayQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(EssayQuestion $essayQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EssayQuestion $essayQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EssayQuestion  $essayQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(EssayQuestion $essayQuestion)
    {
        //
    }
}
