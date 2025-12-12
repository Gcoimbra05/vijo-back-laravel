@extends('layouts.app')
@section('title', 'Video Types List')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2"><?= $pageTitle; ?></h5>
        <a class="btn btn-label-primary btn-sm" type="button" href="" style="margin-bottom: 10px;"><span class="tf-icons bx bx-plus me-1"></span> Add New</a>
    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap p-2">
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
                                <?php
                                    // Render emoji: accept hex code (e.g. "1F600"), HTML entity (e.g. "&#x1F600;") or the character itself
                                    $emojiVal = $catalog->emoji_rendered ?? '';
                                    echo $emojiVal;
                                ?>
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
                                        <?php if ($catalog->status == 1) { ?>
                                            <a class="dropdown-item" href="{{ route('admin.catalog.deactivate', $catalog->id) }}">
                                                <i class="bx bx-x me-1"></i> Deactivate
                                            </a>
                                        <?php } else if ($catalog->status == 0) { ?>
                                            <a class="dropdown-item" href="{{ route('admin.catalog.activate', $catalog->id) }}">
                                                <i class="bx bx-check me-1"></i> Activate
                                            </a>
                                        <?php } ?>
                                        <a class="dropdown-item" href="{{ route('admin.catalogs.edit', $catalog->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.catalogs.destroy', $catalog->id) }}" onClick="return confirm('Are you sure you want to delete this record?');">
                                            <i class="bx bx-trash me-1"></i> Delete
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

    @if (session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 2000;">
            <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ☑ {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @elseif (session('error'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 2000;">
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                       ⊗ {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toastElList = [].slice.call(document.querySelectorAll('.toast'));
        toastElList.map(function (toastEl) {
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        });
    });
</script>
@endsection