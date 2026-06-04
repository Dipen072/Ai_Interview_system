<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'difficulty',
        'total_questions',
        'duration_minutes',
        'status',
        'overall_score',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'overall_score' => 'decimal:2',
    ];

    /**
     * Get the user who took this interview.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of this interview.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the questions generated for this interview.
     */
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order_index');
    }

    /**
     * Get the answers submitted for this interview.
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Get the overall result of this interview.
     */
    public function result()
    {
        return $this->hasOne(Result::class);
    }
}
