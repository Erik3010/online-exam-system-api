<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function creator()
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function essay()
    {
        return $this->hasMany(EssayQuestion::class);
    }

    public function multipleChoice()
    {
        return $this->hasMany(MultipleChoiceQuestion::class);
    }

    public function essayAnswer()
    {
        return $this->hasManyThrough(StudentEssayAnswer::class, EssayQuestion::class);
    }

    public function historyEssayAnswer()
    {
        return $this->hasManyThrough(HistoryStudentEssayAnswer::class, EssayQuestion::class);
    }

    public function multipleChoiceAnswer()
    {
        return $this->hasManyThrough(
            StudentMultipleChoiceAnswer::class,
            MultipleChoiceQuestion::class,
            'exam_id',
            'question_id'
        );
    }

    public function historyMultipleChoiceAnswer()
    {
        return $this->hasManyThrough(
            HistoryStudentMultipleChoiceAnswer::class,
            MultipleChoiceQuestion::class,
            'exam_id',
            'question_id'
        );
    }
}
