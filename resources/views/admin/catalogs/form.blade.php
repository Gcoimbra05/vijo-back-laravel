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
</style>

@section('content')

@php
    $isEdit = $action === 'Edit';
    $pageAction = $isEdit ? 'catalog.update' : 'catalog.store';
    $formAction = $isEdit
        ? url("admin/catalog/update/" . ($info[0]['id'] ?? ''))
        : url("admin/catalog/add");
    $formMethod = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ $action }} Journal Catalog</h5>
            <hr class="m-0">
            <div class="card-body">
                <form id="catalog_form" name="catalog_form" method="POST" enctype="multipart/form-data" action="{{ $formAction }}">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" placeholder="Ex: Career" autocomplete="off" value="{{ old('name', $info[0]['name'] ?? '') }}" autofocus />
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="catalog_emoji" class="form-label">Emoji</label>
                            <div class="input-container">
                                <div id="fakeInput" class="fake-input form-control form-control-lg @error('catalog_emoji') is-invalid @enderror">
                                    @if (!empty($info) && !empty($info[0]['catalog_emoji']) && $info[0]['catalog_emoji'])
                                        <span>{{ $info[0]['catalog_emoji'] }}</span>
                                    @else
                                        <span>Select Emoji</span>
                                    @endif
                                </div>
                                <input type="hidden" id="base64Input" class="hidden-input" name="catalog_emoji" value="{{ old('catalog_emoji', $info[0]['catalog_emoji'] ?? '') }}">
                                <button id="toggleButton" class="toggle-button" type="button">😀</button>
                            </div>
                            @error('catalog_emoji')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="selects">
                        <div class="mb-3 col-md-6">
                            <label for="is_delect">Delect</label>
                                <select name="is_delect" id="is_delect" class="form-control @error('is_delect') is-invalid @enderror" style="cursor: pointer; appearance: menulist; width: 85%">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="status">Status</label>
                                <select name="status" id="status" class="form-control @error('is_delect') is-invalid @enderror" style="cursor: pointer; appearance: menulist; width: 85%">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1">Active</option>
                                    <option value="2">Desactive</option>
                                </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="is_promotional">Promotional</label>
                                <select name="is_promotional" id="is_promotional" class="form-control @error('is_delect') is-invalid @enderror" style="cursor: pointer; appearance: menulist; width: 85%">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="is_premium">Premium</label>
                                <select name="is_premium" id="is_premium" class="form-control @error('is_delect') is-invalid @enderror" style="cursor: pointer; appearance: menulist; width: 85%">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="is_multipart">Multipart</label>
                                <select name="is_multipart" id="is_multipart" class="form-control @error('is_delect') is-invalid @enderror" style="cursor: pointer; appearance: menulist; width: 85%">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                        </div>
                    </div>

                    <div class="selects">
                        <div class="mb-3 col-md-6">
                            <label for="video_type_id">Video Type ID</label>
                            <input class="form-control @error('category_id') is-invalid @enderror" type="number" id="video_type_id" name="video_type_id" placeholder="Ex: 1234" autocomplete="off" value="" autofocus, style="width: 150%" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="category_id">Category ID</label>
                            <input class="form-control @error('category_id') is-invalid @enderror" type="number" id="category_id" name="category_id" placeholder="Ex: 1234" autocomplete="off" value="" autofocus, style="width: 150%" />
                        </div>
                    </div>

                    <div class="selects">
                        <div class="mb-3 col-md-6">
                            <label for="parent_catalog_id">Parent Catalog ID</label>
                            <input class="form-control @error('parent_catalog_id') is-invalid @enderror" type="number" id="parent_catalog_id" name="parent_catalog_id" placeholder="Ex: 1234" autocomplete="off" value="" autofocus, style="width: 150%"/>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="admin_order">Admin Order</label>
                            <input class="form-control @error('admin_order') is-invalid @enderror" type="number" id="admin_order" name="admin_order" placeholder="Ex: 1234" autocomplete="off" value="" autofocus, style="width: 150%" />
                        </div>
                    </div>

                    <div class="selects">
                        <div class="mb-3 col-md-6">
                            <label for="min_record_time">Min Record Time</label>
                            <input class="form-control @error('min_record_time') is-invalid @enderror" type="time" id="min_record_time" name="min_record_time" placeholder="Ex: 01:00" autocomplete="off" value="" autofocus, style="width: 150%"/>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="max_record_time">Max Record Time</label>
                            <input class="form-control @error('max_record_time') is-invalid @enderror" type="time" id="max_record_time" name="max_record_time" placeholder="Ex: 15:00" autocomplete="off" value="" autofocus, style="width: 150%" />
                        </div>
                    </div>
                    
                    <div class="mb-3 col-md-12">
                        <label for="tags" class="form-label">Tags</label>
                        <textarea class="form-control @error('tags') is-invalid @enderror" id="tags" name="tags" placeholder="Ex: Tags" autocomplete="off"></textarea>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Ex: Description" autocomplete="off">{{ old('description', $info[0]['description'] ?? '') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2" id="btn_save">Save changes</button>
                        <a href="{{ url('admin/catalogs') }}" class="btn btn-outline-secondary">Back</a>
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
