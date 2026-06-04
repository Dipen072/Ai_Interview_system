<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'interview_id',
        'question_text',
        'order_index',
    ];

    /**
     * Get the interview that this question belongs to.
     */
    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }

    /**
     * Get the answers submitted for this question.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
