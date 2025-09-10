@extends('layouts.app')

<style>
    .input-container {
        display: flex;
        align-items: center;
        max-width: 400px;
        gap: 1rem;
        position: relative;
    }

    .fake-input {
        position: relative;
        font-size: 2.25rem;
    }

    .fake-input img {
        max-width: 32px;
        max-height: 32px;
    }

    .toggle-button {
        font-size: 24px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        position: absolute;
        right: 0.5rem;
    }

    .picker-container {
        display: none;
        position: absolute;
        width: 100%;
        z-index: 10;
        top: 100%; /* Default: Below the input */
        margin-top: 4px; /* Space between input and picker */
    }

    .picker-container.visible {
        display: block;
    }

    .picker-container.top {
        bottom: 100%;
        margin-bottom: 4px; /* Space between input and picker when above */
    }

    .picker-container.bottom {
        top: 100%;
        margin-top: 4px; /* Space between input and picker when below */
    }

    .hidden-input {
        display: none;
    }

    .loading {
        text-align: center;
        color: #666;
    }

    @media (max-width: 600px) {
        .input-container {
            max-width: 100%;
        }

        .toggle-button {
            font-size: 20px;
        }
    }

    .selects{
        display: grid;
        grid-auto-flow: column;
    }
    .type-id{
        display: grid;
        grid-auto-flow: column;
    }
    .wide-input { 
        width: 150%; 
    }
</style>

@section('content')

@php
    $isEdit = $action === 'Edit';
    $pageAction = $isEdit ? 'tag.update' : 'tag.store';
    $formAction = $isEdit
        ? route('tag.update', $info[0]['id'] ?? '')
        : route('tag.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ $action }} Tags</h5>
            <hr class="m-0">
            <div class="card-body">
                <form id="tag_form" name="tag_form" method="POST" enctype="multipart/form-data" action="{{ $formAction }}">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <span>Error saving Tags</span>
                        </div>
                    @endif

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" placeholder="Ex: Career" autocomplete="off" value="{{ old('name', $info[0]['name'] ?? '') }}" autofocus required />
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        
                        <div class="mb-3 col-md-6">
                            <label for="created_by_user">Created by user</label>
                            <select name="created_by_user" class="form-control @error('created_by_user') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                <option value="" disabled {{ $selectedUserId ? '' : 'selected' }}>Select a user</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="selects">
                        <div class="mb-3 col-md-6">
                            <label for="status">Status</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1" {{ old('status', $info[0]['status'] ?? '') == 1 ? 'selected' : '' }}>Activate</option>
                                    <option value="0" {{ old('status', $info[0]['status'] ?? '') == 0 ? 'selected' : '' }}>Deactivate</option>
                                </select>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="category_id">Category</label>
                            <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror wide-input @error('category_id') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                <option value="" disabled selected>Select</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $info[0]['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control  @error('type') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                <option value="" disabled selected>Select</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ old('type', $info[0]['type'] ?? '') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>


                            @error('type')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror wide-input @error('description') is-invalid @enderror" id="description" name="description" placeholder="Ex: Description" autocomplete="off">{{ old('description', $info[0]['description'] ?? '') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2" id="btn_save">Save changes</button>
                        <a href="{{ url('admin/tags') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>

<script>
    async function imageUrlToBase64(url) {
        try {
            const response = await fetch(url, { mode: 'cors' });
            const blob = await response.blob();
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result);
                reader.readAsDataURL(blob);
            });
        } catch (error) {
            console.error('Error converting image to base64:', error);
            return '';
        }
    }

    function togglePicker(position = 'bottom') {
        const pickerContainer = document.getElementById('pickerContainer');
        pickerContainer.classList.toggle('visible');
        pickerContainer.classList.toggle('bottom', position === 'bottom');
        pickerContainer.classList.toggle('top', position === 'top');
    }

    async function initEmojiPicker(position = 'bottom') {
        const inputContainer = document.querySelector('.input-container');
        const fakeInput = document.getElementById('fakeInput');
        const base64Input = document.getElementById('base64Input');
        const toggleButton = document.getElementById('toggleButton');

        let pickerContainer = document.getElementById('pickerContainer');
        if (!pickerContainer) {
            pickerContainer = document.createElement('div');
            pickerContainer.id = 'pickerContainer';
            pickerContainer.classList.add('picker-container');
            inputContainer.appendChild(pickerContainer);
        }

        toggleButton.addEventListener('click', (event) => {
            event.preventDefault();
            togglePicker(position);
        });

        try {
            const picker = new EmojiMart.Picker({
                onEmojiSelect: async (emoji) => {
                    base64Input.value = emoji.unified;
                    fakeInput.innerHTML = `<span>${emoji.native}</span>`;
                    pickerContainer.classList.remove('visible');
                },
                set: 'native',
                locale: 'en',
                showPreview: false
            });

            pickerContainer.innerHTML = '';
            pickerContainer.appendChild(picker);
        } catch (error) {
            console.error('Error initializing emoji picker:', error);
            pickerContainer.innerHTML = '<p class="loading">Failed to load emojis.</p>';
        }
    }

    document.addEventListener('click', (event) => {
        const pickerContainer = document.getElementById('pickerContainer');
        const inputContainer = document.querySelector('.input-container');
        const toggleButton = document.getElementById('toggleButton');

        if (pickerContainer && inputContainer && !inputContainer.contains(event.target) && !toggleButton.contains(event.target)) {
            pickerContainer.classList.remove('visible');
        }
    });

    initEmojiPicker('bottom');
</script>
@endsection
