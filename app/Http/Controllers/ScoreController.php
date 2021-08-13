<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Response\Response;
use App\Services\StudentRankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['student.classroom.students']);
        $classStudentId = $user->student->classroom->students->pluck('id');

        $exams = Exam::where('classroom_id', Auth::user()->student->classroom_id)
            ->with('studentExamResult')
            ->get();

        $studentRankService = new StudentRankService();

        $classExamResult = $studentRankService->getStudentResult($classStudentId);
        $schoolExamResult = $studentRankService->getStudentResult(Student::pluck('id'));

        $classAvg = $classExamResult->firstWhere('student_id', $user->student_id);
        $schoolAvg = $schoolExamResult->firstWhere('student_id', $user->student_id);

        return Response::withData([
            'classRank' => $classAvg,
            'schoolAvg' => $schoolAvg,
            'exams' => $exams
        ]);
    }
}
