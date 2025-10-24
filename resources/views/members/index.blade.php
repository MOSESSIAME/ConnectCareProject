@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Members</h1>

    <a href="{{ route('members.create') }}" class="btn btn-primary mb-3">+ Add Member</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Service Unit</th>
                <th>Homecell</th>
                <th>Foundation Class</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr>
                    <td>{{ $member->full_name }}</td>
                    <td>{{ $member->type }}</td>
                    <td>{{ $member->phone }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->serviceUnit->name ?? 'N/A' }}</td>
                    <td>{{ $member->homecell->name ?? 'N/A' }}</td>
                    <td>
                        @if($member->foundation_class_completed)
                            ✅ Completed
                        @else
                            ❌ Pending
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('members.edit', $member->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('members.destroy', $member->id) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No members found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $members->links() }}
</div>
@endsection
