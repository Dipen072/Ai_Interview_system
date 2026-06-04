@extends('layouts.app')

@slot('title')
    User Directory
@endslot

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold m-0 text-white"><i class="bi bi-people text-indigo me-2"></i> User Directory</h2>
        <p class="text-muted-custom m-0">View all registered users and manage their system permissions or roles.</p>
    </div>

    <div class="card shadow-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover m-0 align-middle">
                    <thead>
                        <tr class="border-secondary text-muted-custom" style="font-size: 0.85rem;">
                            <th class="px-4 py-3">NAME</th>
                            <th class="py-3">EMAIL</th>
                            <th class="py-3">ROLES</th>
                            <th class="py-3">JOINED ON</th>
                            <th class="px-4 py-3 text-end">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="border-secondary">
                                <td class="px-4 py-3 fw-bold text-white">{{ $user->name }}</td>
                                <td class="py-3 text-muted-custom">{{ $user->email }}</td>
                                <td class="py-3">
                                    @foreach($user->roles as $role)
                                        <span class="badge 
                                            @if($role->name === 'Admin') bg-danger-subtle text-danger
                                            @else bg-info-subtle text-info
                                            @endif px-2 py-1">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="py-3 text-muted-custom" style="font-size: 0.85rem;">
                                    {{ $user->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-secondary">
                                            <i class="bi bi-pencil me-1"></i> Edit Roles
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to suspend/soft-delete this user account?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
