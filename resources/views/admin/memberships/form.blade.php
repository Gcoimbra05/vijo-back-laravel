@extends('layouts.app')

<style>

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
    
    .input-container {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }


    .input-container input {
        flex: 1;
        width: 100%;
        padding-right: 3rem; /* espaço para o botão */
        box-sizing: border-box;
    }


    .copy-btn {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%); 
        padding: 0.25rem 0.5rem;
        font-size: 1rem;
        cursor: pointer;
        border: none;
        background: #eee;
        border-radius: 4px;
        transition: transform 0.3s ease;
    }

    .copy-btn:hover {
        transform: translateY(-50%) scale(1.05); 
    }

    .copy-btn.copied {
        background-color: #28a745;
        color: white;
        cursor: default;
        transform: translateY(-50%) scale(1);
    }

    .copy-btn2 {
        position: absolute;
        left: 0.5rem;
        top: 50%;
        transform: translateY(-30%); 
        padding: 0.25rem 0.5rem;
        font-size: 1rem;
        cursor: pointer;
        border: 1px solid #d9dee3;
        background: #eee;
        border-radius: 4px;
        transition: transform 0.3s ease;
    }

    .copy-btn2:hover {
        transform: translateY(-30%) scale(1.1); 
    }



</style>

@section('content')

@php
    $isEdit = $action === 'Edit';
    $pageAction = $isEdit ? 'membership.update' : 'membership.store';
    $formAction = $isEdit
        ? route('membership.update', $info[0]['id'] ?? '')
        : route('membership.store');
    $formMethod = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ $action }} Membership Plans</h5>
            <hr class="m-0">
            <div class="card-body">
                <form id="membership_form" name="membership_form" method="POST" enctype="multipart/form-data" action="{{ $formAction }}">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <span>Error saving Memberships Plans</span>
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
                            <label for="payment_link" class="form-label">Payment Link</label>
                            <input class="form-control @error('payment_link') is-invalid @enderror" type="text" id="payment_link" name="payment_link" placeholder="Ex: https://example.com/payments" autocomplete="off" value="{{ old('payment_link', $info[0]['payment_link'] ?? '') }}" autofocus required />
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        
                    </div>


                    <div class="selects">
                        <div class="mb-3 col-md-12">
                            <label for="slug" class="form-label">Slug</label>
                            <div class="input-container">
                                <input class="form-control @error('slug') is-invalid @enderror" type="text" id="slug" name="slug" placeholder="Your slug will appear here" autocomplete="off" value="{{ old('slug', $info[0]['slug'] ?? '') }}" style="cursor:default" autofocus readonly />
                                <button type="button" id="copyButton" class="copy-btn" title="Copy to clipboard"> <i class="bx bx-copy"></i> </button>
                            </div>
                            @error('slug')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="input-container">
                            <button type="button" id="generateSlugBtn" title="Click for to generate the Friendly Link" style="font-size:10px" class="copy-btn2">Generate</button>
                        </div>

                    </div>
                    

                    <div class="selects">
                        <div class="mb-3 col-md-6">
                            <label for="monthly_cost" class="form-label">Monthly Cost</label>
                            <input class="form-control @error('monthly_cost') is-invalid @enderror" type="number" id="monthly_cost" name="monthly_cost" step="0.01" min="0" placeholder="Ex: 1.5" autocomplete="off" value="{{ old('monthly_cost', $info[0]['monthly_cost'] ?? '') }}" autofocus required />
                            @error('monthly_cost')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="annual_cost" class="form-label">Annual Cost</label>
                            <input class="form-control @error('annual_cost') is-invalid @enderror" type="number" id="annual_cost" name="annual_cost" step="0.01" min="0" placeholder="Ex: 1.5" autocomplete="off" value="{{ old('annual_cost', $info[0]['annual_cost'] ?? '') }}" autofocus required />
                            @error('annual_cost')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
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
                            <label for="payment_mode">Payment Mode</label>
                                <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror" style="cursor: pointer; appearance: menulist">
                                    <option value="" disabled selected>Select</option>
                                    <option value="1" {{ old('payment_mode', $info[0]['payment_mode'] ?? '') == 1 ? 'selected' : '' }}>Debit</option>
                                    <option value="2" {{ old('payment_mode', $info[0]['payment_mode'] ?? '') == 2 ? 'selected' : '' }}>Credit</option>
                                </select>
                        </div>
                    </div>
                                    
                    <div class="row">
                        <div class="mb-3 col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control wide-input" id="description" name="description" placeholder="Ex: Description">{{ old('description', $info[0]['description'] ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2" id="btn_save">Save changes</button>
                        <a href="{{ url('admin/memberships') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>

    const copyButton = document.getElementById('copyButton');
    const slugInput = document.getElementById('slug');
    const generateButton = document.querySelector('.copy-btn2');
    const paymentInput = document.getElementById('payment_link');

    generateButton.addEventListener('click', () => {
        const url = paymentInput.value.trim();
        
        if (!url) {
            alert('Please fill in Payment Link before generating the slug');
            return;
        }

        // Remove barras no final e pega o último trecho
        const parts = url.replace(/\/+$/, '').split('/');
        const slug = parts[parts.length - 1];

        // Preenche o campo slug
        slugInput.value = slug;
    });


    copyButton.addEventListener('click', () => {
        if(copyButton.classList.contains('copied')) return;

        slugInput.select();
        slugInput.setSelectionRange(0, 99999); // Para dispositivos móveis

        navigator.clipboard.writeText(slugInput.value).then(() => {
            copyButton.innerHTML = 'Copied! ✓';
            copyButton.classList.add('copied');

            setTimeout(() => {
                copyButton.innerHTML = '<i class="bx bx-copy"></i>';
                copyButton.classList.remove('copied');
            }, 1500);
        });
    });


</script>
@endsection
