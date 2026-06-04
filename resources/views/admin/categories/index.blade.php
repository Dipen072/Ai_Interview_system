@extends('layouts.app')

@slot('title')
    Manage Categories
@endslot

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-white"><i class="bi bi-tags text-indigo me-2"></i> Categories</h2>
            <p class="text-muted-custom m-0">Create, update, and manage mock exam topics available for candidates.</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i> Create Category
            </a>
        </div>
    </div>

    <!-- Category Tables card -->
    <div class="card shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover m-0 align-middle">
                    <thead>
                        <tr class="border-secondary text-muted-custom" style="font-size: 0.85rem;">
                            <th class="px-4 py-3">ICON</th>
                            <th class="py-3">NAME</th>
                            <th class="py-3">SLUG</th>
                            <th class="py-3" style="width: 45%;">DESCRIPTION</th>
                            <th class="px-4 py-3 text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="border-secondary">
                                <td class="px-4 py-3">
                                    <div class="rounded-3 bg-secondary-subtle p-2 d-inline-flex align-items-center justify-content-center text-info" style="width: 40px; height: 40px; background-color: rgba(6, 182, 212, 0.1) !important;">
                                        <i class="bi {{ $category->icon_class ?? 'bi-tag' }} fs-5"></i>
                                    </div>
                                </td>
                                <td class="py-3 fw-bold text-white fs-6">{{ $category->name }}</td>
                                <td class="py-3 text-muted-custom font-monospace" style="font-size: 0.85rem;">{{ $category->slug }}</td>
                                <td class="py-3 text-muted-custom" style="font-size: 0.85rem; line-height: 1.5;">
                                    {{ Str::limit($category->description, 120) }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-secondary">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                        
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category? It will restrict new mock setup under this category.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted-custom">No categories found. Click "Create Category" to add one!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
