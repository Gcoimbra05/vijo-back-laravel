@extends('layouts.app')

@section('content')
@php
    $formAction = $action === 'Edit'
        ? route('vijoplan.update', $editing->id)
        : route('vijoplan.store');

    $formMethod = $action === 'Edit' ? 'PUT' : 'POST';
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ $action }} My Plans</h5>
            <hr class="m-0">
            <div class="card-body">
                <form method="POST" action="{{ $formAction }}">
                    @csrf
                    @if($formMethod === 'PUT')
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="mb-3 col-md-8">
                            <label for="name" class="form-label">Plan Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ old('name', $editing->name ?? '') }}" required>
                        </div>

                        <div class="mb-3 col-md-4">
                            <label for="length_in_weeks" class="form-label">Length (weeks)</label>
                            <input type="number" min="0" class="form-control" id="length_in_weeks" name="length_in_weeks"
                                   value="{{ old('length_in_weeks', $editing->length_in_weeks ?? '') }}" required>
                        </div>

                        <div class="mb-3 col-md-12">
                            <label for="cost" class="form-label">Description</label>
                            <textarea class="form-control wide-input" id="description" name="description">
                                {{ old('description', $editing->description ?? '') }}
                            </textarea>
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2" id="btn_save">Save changes</button>
                        <a href="{{ route('users.edit', $editing->user_id) }}" class="btn btn-outline-secondary">Back</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
