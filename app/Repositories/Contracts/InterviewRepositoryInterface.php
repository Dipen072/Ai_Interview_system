<?php

namespace App\Repositories\Contracts;

interface InterviewRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function getForUser(int $userId);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
