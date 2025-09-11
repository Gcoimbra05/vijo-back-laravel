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

    .custom-multiselect {
        position: relative;
        width: 200px;
    }

    .select-box {
        border: 1px solid #ccc;
        padding: 8px;
        cursor: pointer;
    }

    .checkboxes {
        border: 1px solid #ccc;
        position: absolute;
        background: #fff;
        width: 100%;
        max-height: 150px;
        overflow-y: auto;
        z-index: 10;
        flex-direction: column; 
        padding: 5px;
    }

    .checkboxes label {
        display: block; 
        margin-bottom: 4px;
        cursor: pointer;
    }

    .checkboxes label:hover {
    background-color: #f0f0f0;
    }

    .select-box {
        border: 1px solid #ccc;
        border-radius: 5px;
        border-bottom: none;
        padding: 8px;
        cursor: pointer;
        position: relative;
        width: 200px;
    }

    /* seta de dropdown simulada */
    .select-box::after {
        content: "";
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid #666; 
        pointer-events: none; 
    }
</style>

@section('content')

@php
    $isEdit = $action === 'Edit';
    $pageAction = $isEdit ? 'catalog.update' : 'catalog.store';
    $formAction = $isEdit
        ? route('catalog.update', $info[0]['id'] ?? '')
        : route('catalog.store');
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

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <span>Error saving catalogs</span>
                        </div>
                    @endif

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="title" class="form-label">Name</label>
                            <input class="form-control wide-input @error('title') is-invalid @enderror" type="text" id="title" name="title" placeholder="Ex: Career" autocomplete="off" value="{{ old('title', $info[0]['title'] ?? '') }}" autofocus required />
                            @error('title')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-3">
                            <label for="emoji" class="form-label">Emoji</label>
                            <div class="input-container">
                                <div id="fakeInput" class="fake-input form-control form-control-lg @error('emoji') is-invalid @enderror">
                                    @if (!empty($info) && !empty($info[0]['emoji']) && $info[0]['emoji'])
                                        {{-- Converte o código hexadecimal em emoji --}}
                                        <span>{!! mb_convert_encoding('&#x' . $info[0]['emoji'] . ';', 'UTF-8', 'HTML-ENTITIES') !!}</span>
                                    @else
                                        <span>Select Emoji</span>
                                    @endif
                                </div>
                                <input type="hidden" id="base64Input" class="hidden-input" name="emoji" value="{{ old('emoji', $info[0]['emoji'] ?? '') }}">
                                <button id="toggleButton" class="toggle-button" type="button">😀</button>
                            </div>
                            @error('emoji')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="selects">
                        <div class="mb-3 col-md-6">
                            <label for="status">Status</label>
                                <select name="status" id="status" class="form-control wide-input @error('status') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1" {{ old('status', $info[0]['status'] ?? '') == 1 ? 'selected' : '' }}>Activate</option>
                                    <option value="0" {{ old('status', $info[0]['status'] ?? '') == 0 ? 'selected' : '' }}>Deactivate</option>
                                </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="is_promotional">Promotional</label>
                                <select name="is_promotional" id="is_promotional" class="form-control wide-input @error('is_promotional') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                    <option value="" disabled selected>Select</option>
                                    <option value="0" {{ old('is_promotional', $info[0]['is_promotional'] ?? '') == 1 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('is_promotional', $info[0]['is_promotional'] ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            @error('is_promotional')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="is_premium">Premium</label>
                                <select name="is_premium" id="is_premium" class="form-control wide-input @error('is_premium') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                    <option value="" disabled selected>Select</option>
                                    <option value="0" {{ old('is_premium', $info[0]['is_premium'] ?? '') == 1 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('is_premium', $info[0]['is_premium'] ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            @error('is_premium')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="is_multipart">Multipart</label>
                                <select name="is_multipart" id="is_multipart" class="form-control wide-input @error('is_multipart') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                    <option value="" disabled selected>Select</option>
                                    <option value="0" {{ old('is_multipart', $info[0]['is_multipart'] ?? '') == 1 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('is_multipart', $info[0]['is_multipart'] ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                             @error('is_multipart')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="selects col-md-11">
                        <div class="mb-3 col-md-6">
                        <label for="video_type_id">Video Type</label>
                        <select name="video_type_id" id="video_type_id" class="form-control wide-input @error('video_type_id') is-invalid @enderror" style="cursor: pointer; appearance: menulist"">
                            <option value="" disabled selected>Select</option>
                            @foreach($videoTypes as $type)
                                <option value="{{ $type->id }}" {{ old('video_type_id', $info[0]['video_type_id'] ?? '') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('video_type_id')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id" class="form-control wide-input @error('category_id') is-invalid @enderror" style="cursor: pointer; appearance: menulist"">
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
                            <label for="parent_catalog_id">Parent Catalog</label>
                            <select name="parent_catalog_id" id="parent_catalog_id" class="form-control wide-input @error('parent_catalog_id') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                <option value="" disabled selected>Select</option>
                                @foreach($catalogs as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_catalog_id', $info[0]['parent_catalog_id'] ?? '') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_catalog_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="selects">
                        <div class="mb-3 col-md-6">
                            <label for="min_record_time">Min Record Time</label>
                            <input class="form-control wide-input @error('min_record_time') is-invalid @enderror" type="number" id="min_record_time" name="min_record_time" placeholder="Ex: 1" autocomplete="off" value="{{ old('min_record_time', $info[0]['min_record_time'] ?? '') }}" autofocus required />
                            @error('min_record_time')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="max_record_time">Max Record Time</label>
                            <input class="form-control wide-input @error('max_record_time') is-invalid @enderror" type="number" id="max_record_time" name="max_record_time" placeholder="Ex: 15" autocomplete="off" value="{{ old('max_record_time', $info[0]['max_record_time'] ?? '') }}" autofocus required />
                            @error('max_record_time')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="admin_order">Admin Order</label>
                            <input class="form-control wide-input @error('admin_order') is-invalid @enderror" type="number" id="admin_order" name="admin_order" placeholder="Ex: 1234" autocomplete="off" value="{{ old('admin_order', $info[0]['admin_order'] ?? '') }}" autofocus required  />
                            @error('admin_order')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3 col-md-12">
                        <label for="tags" class="form-label">Tags</label>
                        <div class="custom-multiselect">
                            <div id="selectBox" class="select-box" style="appearance: menulist">
                                Select Tags
                            </div>
                            <div id="checkboxes" class="checkboxes" style="display: none;">
                                @foreach($tags as $tag)
                                <label>
                                    <input type="checkbox" value="{{ $tag->name }}" class="option_checkbox" /> {{ $tag->name }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                         <textarea class="form-control wide-input @error('tags') is-invalid @enderror" id="tags_textarea" name="tags_text" placeholder="Ex: tags" autocomplete="off" style="cursor: default" readonly>{{ old('tags', $info[0]['tags'] ?? '') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control wide-input @error('description') is-invalid @enderror" id="description" name="description" placeholder="Ex: Description" autocomplete="off">{{ old('description', $info[0]['description'] ?? '') }}</textarea>
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

    const selectBox = document.getElementById('selectBox');
    const checkboxes = document.getElementById('checkboxes');
    const textarea = document.getElementById('tags_textarea');

    // Recuperar tags existentes do catálogo
    let selectedTags = [];
    const existingTags = textarea.value ? textarea.value.split(',').map(t => t.trim()) : [];

    // Marcar checkboxes correspondentes e popular selectedTags
    checkboxes.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        if (existingTags.includes(checkbox.value)) {
            checkbox.checked = true;
            selectedTags.push(checkbox.value);
        }

        // Listener para atualizar selectedTags quando o usuário marcar/desmarcar
        checkbox.addEventListener('change', (e) => {
            const value = e.target.value;
            if (e.target.checked) {
                selectedTags.push(value);
            } else {
                selectedTags = selectedTags.filter(tag => tag !== value);
            }
            textarea.value = selectedTags.join(', ');
        });
    });

    // Mostrar/ocultar lista de checkboxes ao clicar no selectBox
    selectBox.addEventListener('click', () => {
        checkboxes.style.display = checkboxes.style.display === 'none' ? 'block' : 'none';
    });

    // Fechar lista ao clicar fora
    document.addEventListener('click', (e) => {
        if (!selectBox.contains(e.target) && !checkboxes.contains(e.target)) {
            checkboxes.style.display = 'none';
        }
    });

    // Inicializa textarea com os valores existentes
    textarea.value = selectedTags.join(', ');



</script>
@endsection
