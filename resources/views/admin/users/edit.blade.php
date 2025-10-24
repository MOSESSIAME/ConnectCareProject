@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Edit User</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Role --}}
                <div class="mb-3">
                    <label class="form-label">User Role</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Buttons --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
