<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Category::all();
    }

    public function find(int $id)
    {
        return Category::findOrFail($id);
    }

    public function findBySlug(string $slug)
    {
        return Category::where('slug', $slug)->firstOrFail();
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update(int $id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id)
    {
        $category = $this->find($id);
        return $category->delete();
    }
}
