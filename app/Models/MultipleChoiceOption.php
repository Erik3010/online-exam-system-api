<?php

namespace App\Models;

use App\Http\Controllers\MultipleChoiceOptionController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleChoiceOption extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(MultipleChoiceQuestion::class, 'correct_answer_id');
    }
}
