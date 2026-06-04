@extends('layouts.app')

@slot('title')
    Edit Category
@endslot

@section('content')
<div class="container-fluid" style="max-width: 700px;">
    <div class="mb-4">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Categories
        </a>
        <h2 class="fw-bold text-white"><i class="bi bi-pencil text-indigo me-2"></i> Edit Category</h2>
        <p class="text-muted-custom">Modify details for the "{{ $category->name }}" mock exam topic.</p>
    </div>

    <div class="card p-4">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Category Name -->
            <div class="mb-3">
                <label for="name" class="form-label text-white fw-bold">Category Name</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px;">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Icon Class -->
            <div class="mb-3">
                <label for="icon_class" class="form-label text-white fw-bold">Bootstrap Icon Class</label>
                <input type="text" name="icon_class" id="icon_class" class="form-control @error('icon_class') is-invalid @enderror" value="{{ old('icon_class', $category->icon_class) }}" style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px;">
                <div class="form-text text-muted-custom" style="font-size: 0.75rem;">
                    Use any standard class from <a href="https://icons.getbootstrap.com/" target="_blank" class="text-info text-decoration-none">Bootstrap Icons</a>. Examples: <code>bi-braces</code>, <code>bi-server</code>, <code>bi-chat-left-text</code>.
                </div>
                @error('icon_class')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="form-label text-white fw-bold">Description</label>
                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 10px; resize: none;">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                <i class="bi bi-check2-circle me-1"></i> Update Category
            </button>
        </form>
    </div>
</div>
@endsection
