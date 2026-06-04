<?php

namespace App\Repositories\Eloquent;

use App\Models\Interview;
use App\Repositories\Contracts\InterviewRepositoryInterface;

class InterviewRepository implements InterviewRepositoryInterface
{
    public function all()
    {
        return Interview::with(['user', 'category'])->get();
    }

    public function find(int $id)
    {
        return Interview::with(['user', 'category', 'questions.answers'])->findOrFail($id);
    }

    public function getForUser(int $userId)
    {
        return Interview::with('category')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        return Interview::create($data);
    }

    public function update(int $id, array $data)
    {
        // Avoid nested questions loading for standard update
        $interview = Interview::findOrFail($id);
        $interview->update($data);
        return $interview;
    }

    public function delete(int $id)
    {
        $interview = Interview::findOrFail($id);
        return $interview->delete();
    }
}
