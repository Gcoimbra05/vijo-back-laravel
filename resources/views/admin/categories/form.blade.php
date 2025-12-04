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

    .nav-pills .nav-link.active{
        background-color: #1e0e84ff !important;
        border-bottom-left-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        box-shadow: none !important;
    }

    .nav-link{
        transition: transform 0.2s linear !important;
    }

    .nav-link:hover{
        transform: scale(1.06);
    }

    .tab-content{
        padding-top: 0.6rem !important;
    }

</style>

@section('content')

@php
    $isEdit = $action === 'Edit';
    $pageAction = $isEdit ? 'admin.journalCategories.edit' : 'admin.journalCategories.store';
    $formAction = $isEdit
        ? route($pageAction, $info[0]['id'] ?? null)
        : route($pageAction);
    $formMethod = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ $action }} Journal Category</h5>
            <hr class="m-0">

            <div class="card-body">
                <form id="journal_type_form" name="journal_type_form" method="POST" enctype="multipart/form-data" action="{{ $formAction }}">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    <?= csrf_field() ?>

                    <input type="hidden" name="from" value="{{ $from ?? 'category' }}">

                    
                    <!-- Nav Tabs -->
                    <ul class="nav nav-pills" id="editTabs" role="tablist" style="border-bottom: 1px solid #d3d8dcff;">
                        <!-- Category Data -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="category-tab" data-bs-toggle="tab" data-bs-target="#category" type="button" role="tab">Category Data</button>
                        </li>
                        <!-- Catalogs -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="catalogs-tab" data-bs-toggle="tab" data-bs-target="#catalogs" type="button" role="tab">Catalogs</button>
                        </li>
                    </ul>

                    
                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="editTabsContent">

                        <!-- Conteúdo Category Data -->
                        <div class="tab-pane fade show active" id="category" role="tabpanel">
                            
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" placeholder="Ex: Career" autocomplete="off" value="{{ old('name', $info[0]['name'] ?? '') }}" autofocus />
                                    @error('name')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-3">
                                    <label for="category_emoji" class="form-label">Emoji</label>
                                    <div class="input-container">
                                        <div id="fakeInput" class="fake-input form-control form-control-lg @error('category_emoji') is-invalid @enderror">
                                            @if (!empty($info) && !empty($info[0]['category_emoji']) && $info[0]['category_emoji'])
                                                <span>12341</span>
                                            @else
                                                <span>Select Emoji</span>
                                            @endif
                                        </div>
                                        <input type="hidden" id="base64Input" class="hidden-input" name="category_emoji" value="{{ old('category_emoji', $info[0]['category_emoji'] ?? '') }}">
                                        <button id="toggleButton" class="toggle-button" type="button">😀</button>
                                    </div>
                                    @error('category_emoji')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
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
                            </div>
                        </div>

                            <!-- Catalogs -->
                        <div class="tab-pane fade" id="catalogs" role="tabpanel">
                            <div class="table-responsive text-nowrap p-0 mb-0">
                                <table class="table table-striped table-hover dataTableList">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Title</th>
                                            <th>Emoji</th>
                                            <th>Premium</th>
                                            <th>Promotional</th>
                                            <th>Multipart</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <?php if (isset($catalogs) && count($catalogs) > 0): ?>
                                            <?php foreach ($catalogs as $key => $catalog): ?>
                                                <tr>
                                                    <td><?= $key + 1; ?></td>
                                                    <td><?= $catalog->title; ?></td>
                                                    <td>
                                                        <?= !empty($catalog->emoji) ? mb_convert_encoding('&#x' . $catalog->emoji . ';', 'UTF-8', 'HTML-ENTITIES') : '-' ?>
                                                    </td>
                                                    <td><?= $catalog->is_premium ? 'Yes' : 'No'; ?></td>
                                                    <td><?= $catalog->is_promotional ? 'Yes' : 'No'; ?></td>
                                                    <td><?= $catalog->is_multipart ? 'Yes' : 'No'; ?></td>
                                                    <td>
                                                        <?php
                                                            switch($catalog->status) {
                                                                case 1: echo 'Activate'; break;
                                                                case 0: echo 'Deactivate'; break;
                                                            }
                                                        ?>
                                                    </td>
                                                    <td><?= date('Y-m-d', strtotime($catalog->updated_at)); ?></td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="overflow: visible !important">
                                                                <i class="bx bx-dots-vertical-rounded"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                
                                                                <a class="dropdown-item" href="{{ route('catalog.edit', $catalog->id) }}">
                                                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                                                </a>
                                                                
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No records found.</td>
                                            </tr>
                                            <div class="dropdown my-5">
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    
                            <!-- buttons -->
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary me-2" id="btn_save">Save changes</button>
                            <a href="<?php echo url('admin/journal_categories'); ?>" class="btn btn-outline-secondary">Back</a>
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
