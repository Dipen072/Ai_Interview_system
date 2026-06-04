<?php

namespace App\Repositories\Contracts;

interface ResultRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function findByInterviewId(int $interviewId);
    public function getForUser(int $userId);
    public function create(array $data);
}
