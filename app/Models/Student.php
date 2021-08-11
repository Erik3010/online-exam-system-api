<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function allExamResult()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function examResult()
    {
        return $this->hasOne(ExamResult::class);
    }
}
