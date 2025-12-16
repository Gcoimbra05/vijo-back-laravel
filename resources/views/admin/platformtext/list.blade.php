@extends('layouts.app')

<style>
.table td {
  white-space: nowrap;        /* Impede quebra de linha */
  overflow: hidden;           /* Oculta o que passar do limite */
  text-overflow: ellipsis;    /* Mostra "..." no final */
  max-width: 200px;           /* Ajuste o tamanho máximo da célula */
}
</style>

@section('title', 'Video Types List')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">{{ $pageTitle }}</h5>
        <a class="btn btn-label-primary btn-sm" type="button" href="{{ route('admin.platformtext.create') }}" style="margin-bottom: 10px;">
            <span class="tf-icons bx bx-plus me-1"></span> Add New
        </a>
    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover dataTableList">
            <thead>
                <tr>
                    <th>title</th>
                    <th>Slug</th>
                    <th>highlight</th>
                    <th>emoji</th>
                    <th>location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody class="table-border-bottom-0">
                @if(isset($platformtexts) && $platformtexts->count())
                    @foreach($platformtexts as $key => $platformtext)
                        <tr>
                        <td>{{ $platformtext->title }}</td>
                        <td>{{ $platformtext->slug }}</td>
                        <td>{{ $platformtext->highlight }}</td>
                        <td>{{ $platformtext->emoji }}</td>
                        <td>{{ $platformtext->location }}</td>
                        <td>@switch($platformtext->status)
                                    @case(1) Activated @break
                                    @case(0) Deactivated @break
                                    @default {{ $platformtext->status }}
                            @endswitch</td>
                        <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="overflow: visible !important">
                                        <i class="icon-base ri ri-more-2-line icon-18px"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($platformtext->status == 1)
                                            <a class="dropdown-item" href="{{ route('admin.platformtext.deactivate', $platformtext->id) }}">
                                                <i class="icon-base ri ri-close-line me-1"></i> Deactivate
                                            </a>
                                        @else
                                            <a class="dropdown-item" href="{{ route('admin.platformtext.activate', $platformtext->id) }}">
                                                <i class="icon-base ri ri-check-line me-1"></i> Activate
                                            </a>
                                        @endif

                                        <a class="dropdown-item" href="{{ route('admin.platformtext.edit', $platformtext->id) }}">
                                            <i class="icon-base ri ri-edit-line me-1"></i> Edit
                                        </a>

                                        <a class="dropdown-item" href="{{ route('admin.platformtext.delete', $platformtext->id) }}" onclick="return confirm('Are you sure you want to delete this record?');">
                                            <i class="icon-base ri ri-delete-bin-line me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">No platform text found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
