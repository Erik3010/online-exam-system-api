<?php

namespace App\Http\Controllers;

use App\Models\EssayQuestion;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\HistoryStudentEssayAnswer;
use App\Models\HistoryStudentMultipleChoiceAnswer;
use App\Models\Student;
use App\Models\StudentEssayAnswer;
use App\Models\StudentMultipleChoiceAnswer;
use App\Response\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::where('created_by', Auth::user()->teacher_id)
            ->with('classroom')
            ->get();

        return Response::withData($exams);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'start' => ['required', 'date_format:Y-m-d H:i'],
            'end' => ['required', 'date_format:Y-m-d H:i', 'after:start'],
        ]);

        if ($validator->fails()) {
            return Response::invalidField();
        }

        $params = $request->only(['title', 'classroom_id', 'start', 'end']);
        $params['created_by'] = Auth::user()->teacher_id;

        Exam::create($params);

        return Response::success('exam created successfully');
    }

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

            if ($item['score'] > $essay->weight) {
                return Response::error("Essay question with id {$item['question_id']} has score more than question the weight", 422);
            }

            return $total + $item['score'];
        }, 0);

        $score = (($multipleChoiceScore + $essayScore) / 2);
        $examResult = ExamResult::updateOrCreate(
            ['exam_id' => $exam->id, 'student_id' => $student->id],
            ['score' => $score]
        );

        return Response::withData($examResult);
    }

    public function examByClass()
    {
        $exams = Exam::where('classroom_id', Auth::user()->student->classroom_id)
            ->get();

        return Response::withData($exams);
    }

    public function examQuestions(Exam $exam)
    {
        $exam->load([
            'multipleChoice' => function ($query) {
                $query->select([
                    'id',
                    'question',
                    'weight',
                    'exam_id',
                    'created_at',
                    'updated_at'
                ])->with('choices')->inRandomOrder();
            },
            'essay' => function ($query) {
                $query->inRandomOrder();
            }
        ]);

        return $exam;
    }

    public function submitAnswer(Exam $exam, Request $request)
    {
        $student_id = Auth::user()->student_id;

        $exam->loadCount([
            'essayAnswer' => function ($query) use ($student_id) {
                $query->where('student_id', $student_id);
            },
            'multipleChoiceAnswer' => function ($query) use ($student_id) {
                $query->where('student_id', $student_id);
            }
        ]);

        $isStudentAnswered = $exam->essay_answer_count || $exam->multiple_choice_answer_count;

        DB::beginTransaction();

        try {
            if ($isStudentAnswered) {
                $prevMultipleChoiceAnswer = $exam->multipleChoiceAnswer()
                    ->select(['question_id', 'option_id', 'student_id'])
                    ->where('student_id', $student_id)
                    ->get()
                    ->map(function ($history) {
                        unset($history['laravel_through_key']);

                        $now = now();

                        $history['created_at'] = $now;
                        $history['updated_at'] = $now;

                        return $history;
                    });

                HistoryStudentMultipleChoiceAnswer::insert($prevMultipleChoiceAnswer->toArray());

                $prevEssayAnswer = $exam->essayAnswer()
                    ->select(['answer', 'essay_question_id', 'student_id'])
                    ->where('student_id', $student_id)
                    ->get()
                    ->map(function ($history) {
                        unset($history['laravel_through_key']);

                        $now = now();

                        $history['created_at'] = $now;
                        $history['updated_at'] = $now;

                        return $history;
                    });

                HistoryStudentEssayAnswer::insert($prevEssayAnswer->toArray());
            }

            foreach ($request->multiple_choice as $multiple_choice) {
                StudentMultipleChoiceAnswer::updateOrCreate(
                    ['question_id' => $multiple_choice['question_id'], 'student_id' => $student_id],
                    ['option_id' => $multiple_choice['answer']]
                );
            }

            foreach ($request->essay as $essay) {
                StudentEssayAnswer::updateOrCreate(
                    ['essay_question_id' => $essay['question_id'], 'student_id' => $student_id],
                    ['answer' => $essay['answer']]
                );
            }

            DB::commit();
            return Response::success('Answer submitted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::error($e->getMessage());
        }
    }
}
