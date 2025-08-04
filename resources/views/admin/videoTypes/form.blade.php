@extends('layouts.app')
@section('content')

@php
    $isEdit = $action === 'Edit';
    $pageAction = $isEdit ? 'journal_types.update' : 'journal_types.store';
    $formAction = $isEdit
        ? route($pageAction, $info[0]['id'] ?? null)
        : route($pageAction);
    $formMethod = $isEdit ? 'PUT' : 'POST';
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <h5 class="card-header">{{ $action }} Journal Type</h5>
            <hr class="m-0">
            <div class="card-body">
                <form id="journal_type_form" name="journal_type_form" method="POST" enctype="multipart/form-data" action="{{ $formAction }}">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" placeholder="Ex: 0M 1KPI 1V" autocomplete="off" value="{{ old('name', $info[0]['name'] ?? '') }}" autofocus />
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="kpi_no" class="form-label"># KPI</label>
                            <input class="form-control @error('kpi_no') is-invalid @enderror" type="text" id="kpi_no" name="kpi_no" placeholder="Ex: 1" autocomplete="off" value="{{ old('kpi_no', $info[0]['kpi_no'] ?? '') }}" />
                            @error('kpi_no')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="metric_no" class="form-label"># Metric</label>
                            <input class="form-control @error('metric_no') is-invalid @enderror" type="text" id="metric_no" name="metric_no" placeholder="Ex: 0" autocomplete="off" value="{{ old('metric_no', $info[0]['metric_no'] ?? '') }}" />
                            @error('metric_no')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="video_no" class="form-label"># Video Question</label>
                            <input class="form-control @error('video_no') is-invalid @enderror" type="text" id="video_no" name="video_no" placeholder="Ex: 1" autocomplete="off" value="{{ old('video_no', $info[0]['video_no'] ?? '') }}" />
                            @error('video_no')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2" id="btn_save">Save changes</button>
                        <a href="<?php echo url('admin/journal_types'); ?>" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#kpi_no').on('keyup', function() {
            let kpi = $('#kpi_no').val();
            if (kpi == 1) {
                $('#video_no').val(1);
            } else {
                $('#video_no').val(kpi);
            }
        });

        $('#video_no').on('keyup', function() {
            let kpi = $('#kpi_no').val();
            let video_que = $('#video_no').val();

            let modulo = (video_que % kpi);

            if (modulo == 0) {
                $('#video_no').val(video_que);
            } else {
                $('#video_no').val('');
                let msg = "Enter video question in multiple of " + kpi;
                alert(msg);
                return FALSE;
            }
        });

        $('#metric_no').on('keyup', function() {
            let kpi = $('#kpi_no').val();
            let metric = $('#metric_no').val();

            let modulo = (metric % kpi);

            if (modulo == 0) {
                $('#metric_no').val(metric);
            } else {
                $('#metric_no').val('');
                let msg = "Enter metrics in multiple of " + kpi;
                alert(msg);
                return FALSE;
            }
        });
    });
</script>
@endsection
