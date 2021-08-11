<?php

namespace App\Http\Controllers;

use App\Models\EssayQuestion;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Response\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $exams = Exam::where('created_by', Auth::user()->teacher_id)
            ->with('classroom')
            ->get();

        return Response::withData($exams);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'start' => ['required', 'date_format:Y-m-d H:i'],
            'end' => ['required', 'date_format:Y-m-d H:i', 'after:start'],
        ]);

        if ($validator->fails())
            return Response::invalidField();

        $params = $request->only(['title', 'classroom_id', 'start', 'end']);
        $params['created_by'] = Auth::user()->teacher_id;

        Exam::create($params);

        return Response::success('exam created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function show(Exam $exam)
    {
        $essayStudent = $exam->essayAnswer()
            ->groupBy('student_id')
            ->pluck('student_id');

        $multipleChoiceStudent = $exam->multipleChoiceAnswer()
            ->groupBy('student_id')
            ->whereNotIn('student_id', $essayStudent)
            ->pluck('student_id');

        $participatedStudent = $essayStudent->merge($multipleChoiceStudent);

        $students = Student::whereIn('id', $participatedStudent)
            ->with(['examResult' => function ($query) use ($exam) {
                $query->where('exam_id', $exam->id);
            }])
            ->get();

        return Response::withData($students);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Exam $exam)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exam $exam)
    {
        DB::beginTransaction();

        try {
            $exam->historyEssayAnswer()->delete();
            $exam->essayAnswer()->delete();
            $exam->essayKeywords()->delete();
            $exam->essay()->delete();

            $exam->historyMultipleChoiceAnswer()->delete();
            $exam->multipleChoiceAnswer()->delete();
            $exam->multipleChoiceOption()->delete();
            $exam->multipleChoice()->delete();
            $exam->examResult()->delete();

            $exam->delete();

            DB::commit();

            return Response::success('Exam deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();

            return Response::error($e->getMessage());
        }
    }

    public function studentAnswer(Exam $exam, Student $student)
    {
        $exam->load([
            'essay.keywords',
            'essay.answer' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            },
            'multipleChoice.choices',
            'multipleChoice.studentAnswer' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            },
        ]);

        return Response::withData($exam);
    }

    public function processExamResult(Exam $exam, Student $student, Request $request)
    {
        $multipleChoice = $exam->multipleChoice()->with(['studentAnswer' => function ($query) use ($student) {
            $query->where('student_id', $student->id);
        }])->get();

        $multipleChoiceScore = $multipleChoice->reduce(function ($total, $item) {
            $currentScore = ($item->correct_answer_id === ($item->studentAnswer->option_id ?? null))
                ? $item->weight
                : 0;

            return $total + $currentScore;
        }, 0);

        $essayScore = collect($request->essay_result)->reduce(function ($total, $item) {
            $essay = EssayQuestion::find($item['question_id']);

            if ($item['score'] > $essay->weight)
                return Response::error("Essay question with id {$item['question_id']} has score more than question the weight", 422);

            return $total + $item['score'];
        }, 0);

        $score = (($multipleChoiceScore + $essayScore) / 2);
        $examResult = ExamResult::updateOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $student->id],
            ['score' => $score]
        );

        return Response::withData($examResult);
    }
}
