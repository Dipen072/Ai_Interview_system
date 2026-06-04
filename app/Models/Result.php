<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_id',
        'user_id',
        'overall_score',
        'percentage',
        'duration_seconds',
        'summary_feedback',
        'ai_suggestions',
    ];

    protected $casts = [
        'overall_score' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    /**
     * Get the interview associated with this result.
     */
    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }

    /**
     * Get the user who owns this result.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the feedback records for this result.
     */
    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
