<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleChoiceQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function correctAnswer()
    {
        return $this->belongsTo(MultipleChoiceOption::class, 'correct_answer_id');
    }

    public function choices()
    {
        return $this->hasMany(MultipleChoiceOption::class, 'multiple_choice_id');
    }

    public function studentAnswer()
    {
        return $this->hasOne(StudentMultipleChoiceAnswer::class, 'question_id');
    }

    public function historyStudentAnswer()
    {
        return $this->hasOne(HistoryStudentEssayAnswer::class, 'question_id');
    }
}
