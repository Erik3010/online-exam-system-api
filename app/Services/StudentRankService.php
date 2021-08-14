<?php

namespace App\Services;

use App\Models\ExamResult;
use Illuminate\Support\Facades\DB;

class StudentRankService
{
    public function getStudentResult($student_ids)
    {
        return ExamResult::select([
            DB::raw('SUM(score) AS total_score'),
            DB::raw('COUNT(*) AS total_exam'),
            'student_id',
            'exam_id'
        ])
        ->whereIn('student_id', $student_ids)
        ->groupBy('student_id')
        ->orderBy('total_score', 'DESC')
        ->get()
        ->map(function ($item) {
            $item->average = $item->total_score / $item->total_exam;
            return $item;
        })
        ->sortByDesc('average')
        ->map(function ($item, $key) {
            $item->rank = $key + 1;

            return $item;
        });
    }
}
