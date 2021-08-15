<?php

namespace App\Http\Controllers;

use App\Models\EssayQuestion;
use App\Models\Exam;
use App\Response\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EssayQuestionController extends Controller
{
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
}
