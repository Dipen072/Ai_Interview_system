<?php

namespace App\Repositories\Eloquent;

use App\Models\Result;
use App\Repositories\Contracts\ResultRepositoryInterface;

class ResultRepository implements ResultRepositoryInterface
{
    public function all()
    {
        return Result::with(['user', 'interview.category'])->get();
    }

    public function find(int $id)
    {
        return Result::with(['user', 'interview.category', 'feedback'])->findOrFail($id);
    }

    public function findByInterviewId(int $interviewId)
    {
        return Result::with(['user', 'interview.category', 'feedback'])
            ->where('interview_id', $interviewId)
            ->first();
    }

    public function getForUser(int $userId)
    {
        return Result::with('interview.category')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        return Result::create($data);
    }
}
