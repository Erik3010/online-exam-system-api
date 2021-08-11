<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMultipleChoiceAnswer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(MultipleChoiceQuestion::class, 'question_id');
    }

    public function choice()
    {
        return $this->belongsTo(MultipleChoiceQuestion::class, 'option_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
