<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EssayQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function keywords()
    {
        return $this->hasMany(EssayKeyword::class, 'essay_id');
    }

    public function answer()
    {
        return $this->hasOne(StudentEssayAnswer::class);
    }

    public function historyAnswer()
    {
        return $this->hasOne(HistoryStudentEssayAnswer::class);
    }
}
