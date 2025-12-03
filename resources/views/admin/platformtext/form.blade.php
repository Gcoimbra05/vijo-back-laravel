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

    .selects{
        display: grid;
        grid-auto-flow: column;
    }

</style>

@section('content')

@php
    $isEdit = ($action === 'edit');
    $pageAction = $isEdit ? 'platformtext.update' : 'platformtext.store';
    $formAction = $isEdit
        ? route('admin.platformtext.update', $platformText->id)
        : route('admin.platformtext.store');
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
                            <span>Error saving Platform Text</span>
                        </div>
                    @endif

                    <div class="selects">
                        <div class="mb-3 col-md-10">
                            <label for="title" class="form-label">Title</label>
                            <input class="form-control wide-input @error('title') is-invalid @enderror" type="text" id="title" name="title" placeholder="Ex: Career" autocomplete="off" value="{{ old('title', $platformText->title ?? '') }}" autofocus  />
                            @error('title')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-8">
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

                        <div class="mb-3 col-md-6">
                            <label for="status">Status</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1" {{ old('status', $info[0]['status'] ?? '') == 1 ? 'selected' : '' }}>Activate</option>
                                    <option value="0" {{ old('status', $info[0]['status'] ?? '') == 0 ? 'selected' : '' }}>Deactivate</option>
                                </select>
                        </div>
                    </div>

                    <div class="row">

                        <div class="mb-3 col-md-6">
                            <label for="link" class="form-label">Link</label>
                            <input class="form-control wide-input @error('link') is-invalid @enderror" type="text" id="link" name="link" placeholder="Ex: career" autocomplete="off" value="{{ old('link', $platformText->link ?? '') }}" autofocus  />
                            @error('link')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="slug" class="form-label">Slug</label>
                            <input class="form-control wide-input @error('slug') is-invalid @enderror" type="text" id="slug" name="slug" placeholder="Ex: career" autocomplete="off" value="{{ old('slug', $platformText->slug ?? '') }}" autofocus  />
                            @error('slug')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="highlight" class="form-label">Highlight</label>
                            <input class="form-control wide-input @error('highlight') is-invalid @enderror" type="text" id="highlight" name="highlight" placeholder="Ex: Explore your career opportunities with Vijo." autocomplete="off" value="{{ old('highlight', $platformText->highlight ?? '') }}" autofocus  />
                            @error('highlight')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                            
                        <div class="mb-3 col-md-6">
                            <label for="location" class="form-label">Location</label>
                            <input class="form-control wide-input @error('location') is-invalid @enderror" type="text" id="location" name="location" placeholder="Ex: footer, header, etc." autocomplete="off" value="{{ old('location', $platformText->location ?? '') }}" autofocus  />
                            @error('location')
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
                        <a href="{{ url('admin/platformtext') }}" class="btn btn-outline-secondary">Back</a>
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
    const tagsContainer = document.getElementById('tagsContainer');
    const hiddenTags = document.getElementById('hiddenTags');

    // Inicializar selectedTags com valores existentes
    let selectedTags = hiddenTags.value ? hiddenTags.value.split(',').map(t => t.trim()) : [];

    // Função para renderizar as tags dentro do container
    function renderTags() {
        tagsContainer.innerHTML = '';
        selectedTags.forEach(tag => {
            const tagEl = document.createElement('div');
            tagEl.className = 'badge bg-primary d-flex align-items-center gap-1';
            tagEl.innerHTML = `
                ${tag} <button type="button" class="btn-close btn-close-white btn-sm"></button>
            `;
            // Remover tag ao clicar no X
            tagEl.querySelector('button').addEventListener('click', () => {
                selectedTags = selectedTags.filter(t => t !== tag);
                hiddenTags.value = selectedTags.join(', ');
                // Desmarca checkbox correspondente
                checkboxes.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    if(cb.value === tag) cb.checked = false;
                });
                renderTags();
            });
            tagsContainer.appendChild(tagEl);
        });
    }

</script>
@endsection