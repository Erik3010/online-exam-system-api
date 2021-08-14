<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Classroom;
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
            DB::raw('ROUND(AVG(CAST(score AS float)), 2) AS avg_score')
        ])
            ->with('exam')
            ->groupBy('exam_id')
            ->get();

        $studentStatistic = Classroom::has('exams')
            ->with(['examResult' => function ($query) {
                $query->select([
                    DB::raw('ROUND(AVG(CAST(score AS float)), 2) AS avg_score')
                ])->groupBy('student_id');
            }])
            ->get()
            ->map(function ($item) {
                $item->min_score = (float) $item->examResult->min('avg_score');
                $item->max_score = (float) $item->examResult->max('avg_score');

                return $item;
            });

        $studentAvg = ExamResult::select([
            DB::raw('AVG(score) AS avg_score'),
        ])
            ->groupBy('student_id')
            ->get();

        $allStudentStatistic = [
            'avg' => (float) $studentAvg->avg('avg_score'),
            'min_score' => (float) $studentAvg->min('avg_score'),
            'max_score' => (float) $studentAvg->max('avg_score')
        ];

        return Response::withData([
            'exam_statistic' => $examStatistic,
            'student_statistic' => $studentStatistic,
            'all_student_statistic' => $allStudentStatistic
        ]);
    }

    public function show(Request $request)
    {
        if ($request->filled('classroom_id'))
            $student_ids = Classroom::find($request->classroom_id)->students()->pluck('id');
        else
            $student_ids = Student::pluck('id');

        $studentsResult = ExamResult::whereIn('student_id', $student_ids)
            ->with('student')
            ->groupBy('student_id')
            ->select([
                'id',
                'student_id',
                DB::raw("ROUND(AVG(CAST(score AS float)),2) AS avg_score")
            ])
            ->orderBy('avg_score', 'DESC')
            ->get();

        return Response::withData($studentsResult);
    }
}
