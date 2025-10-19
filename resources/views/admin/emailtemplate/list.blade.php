@extends('layouts.app')

<style>
.table td {
  white-space: nowrap;        /* Impede quebra de linha */
  overflow: hidden;           /* Oculta o que passar do limite */
  text-overflow: ellipsis;    /* Mostra "..." no final */
  max-width: 200px;           /* Ajuste o tamanho máximo da célula */
}
</style>

@section('title', 'Email Template List')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ $pageTitle }}</h5>
        <a class="btn btn-label-primary btn-sm" type="button" href="{{ route('emailtemplate.create') }}" style="margin-bottom: 10px;">
            <span class="tf-icons bx bx-plus me-1"></span> Add New
        </a>
    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap p-2">
        <table class="table table-striped table-hover dataTableList">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($emailtemplates) && $emailtemplates->count())
                    @foreach($emailtemplates as $key => $emailtemplate)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $emailtemplate->name }}</td>
                            <td>{{ $emailtemplate->subject }}</td>
                             <td>
                                @switch($emailtemplate->status)
                                    @case(1) Activated @break
                                    @case(0) Deactivated @break
                                    @default {{ $emailtemplate->status }}
                                @endswitch
                            </td>
                            <td>{{ optional($emailtemplate->created_at)->format('Y-m-d') }}</td>
                            <td>{{ optional($emailtemplate->updated_at)->format('Y-m-d') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="overflow: visible !important">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($emailtemplate->status == 1)
                                            <a class="dropdown-item" href="{{ route('emailtemplate.deactivate', $emailtemplate->id) }}">
                                                <i class="bx bx-x me-1"></i> Deactivate
                                            </a>
                                        @else
                                            <a class="dropdown-item" href="{{ route('emailtemplate.activate', $emailtemplate->id) }}">
                                                <i class="bx bx-check me-1"></i> Activate
                                            </a>
                                        @endif

                                        <a class="dropdown-item" href="{{ route('emailtemplate.show', $emailtemplate->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>

                                        <a class="dropdown-item" href="{{ route('emailtemplate.delete', $emailtemplate->id) }}" onclick="return confirm('Are you sure you want to delete this record?');">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">No emailtemplates found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
