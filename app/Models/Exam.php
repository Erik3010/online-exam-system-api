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
}
