<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback'; // Match migration

    protected $fillable = [
        'result_id',
        'criteria',
        'score',
        'feedback_text',
        'suggestions',
    ];

    /**
     * Get the result that owns this feedback.
     */
    public function result()
    {
        return $this->belongsTo(Result::class);
    }
}
