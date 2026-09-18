@extends('layouts.app')

@section('title', 'Edit Task List')
@section('page-title', 'Edit Task List')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square text-warning me-2"></i> Edit Task List</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('task-lists.update', $taskList) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label small fw-semibold text-secondary">List Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $taskList->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label small fw-semibold text-secondary">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $taskList->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('task-lists.show', $taskList) }}" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-jara">
                            <i class="bi bi-check-lg me-1"></i> Update Task List
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
