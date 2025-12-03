@extends('layouts.app')

@section('content')

@php
    $isEdit = ($action === 'edit');
    $pageAction = $isEdit ? 'emailtemplate.update' : 'emailtemplate.store';
    $formAction = $isEdit
        ? route('admin.emailtemplate.update', $emailTemplate->id)
        : route('admin.emailtemplate.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ $action }} Email template</h5>
            <hr class="m-0">
            <div class="card-body">
                <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <span>Error saving Email Template</span>
                        </div>
                    @endif

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control wide-input @error('name') is-invalid @enderror" type="text" id="name" name="name" placeholder="Ex: Career" autocomplete="off" value="{{ old('name', $emailTemplate->name ?? '') }}" autofocus required />
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="status" style="margin-bottom: 3%" >Status</label>
                                <select name="status" id="status" class="form-control wide-input @error('status') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1" {{ old('status', $emailTemplate->status ?? '') == 1 ? 'selected' : '' }}>Activate</option>
                                    <option value="0" {{ old('status', $emailTemplate->status ?? '') == 0 ? 'selected' : '' }}>Deactivate</option>
                                </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <hr class="m-1">
                    <div class="row" style="margin-top: 3%">
                        <div class="mb-3 col-md-8">
                            <label for="subject" class="form-label">Subject</label>
                            <input class="form-control wide0input @error('subject') is-invalid @enderror" type="text" id="subject" placeholder="Ex: Welcome to Vijo" name="subject" autocomplete="off" value="{{ old('subject', $emailTemplate->subject ?? '') }}" autofocus required />
                        </div>
                        @error('subject')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="body" class="form-label">Body</label>
                            <textarea class="form-control wide-input @error('body') is-invalid @enderror" id="body" name="body" placeholder="Ex: Hello! Welcome to Vijo" autocomplete="off">{{ old('body', $emailTemplate->body ?? '') }}</textarea>
                            @error('body')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control wide-input @error('description') is-invalid @enderror" id="description" name="description" placeholder="Ex: Description" autocomplete="off">{{ old('description', $emailTemplate->description ?? '') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2" id="btn_save">Save changes</button>
                        <a href="{{ url('admin/emailtemplate') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection