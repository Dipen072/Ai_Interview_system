<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id',
        'interview_id',
        'user_id',
        'answer_text',
    ];

    /**
     * Get the question this answer belongs to.
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the interview this answer belongs to.
     */
    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }

    /**
     * Get the user who submitted this answer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
