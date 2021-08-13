<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Response\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function index()
    {
        $examStatistic = ExamResult::select([
            'id',
            'exam_id',
            DB::raw('MAX(score) AS max_score'),
            DB::raw('MIN(score) AS min_score'),
            DB::raw('ROUND(AVG(score)) AS avg_score')
        ])
            ->with('exam')
            ->groupBy('exam_id')
            ->get();

        $studentStatistic = Classroom::with(['exams' => function ($query) {
            $query->with(['minScore', 'maxScore']);
        }])
            ->get();

        return $studentStatistic;
    }
}
