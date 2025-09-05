@extends('layouts.app')
@section('title', 'Video Types List')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2"><?= $pageTitle; ?></h5>
        <a class="btn btn-label-primary btn-sm" type="button" href="{{ route('catalog.add') }}" style="margin-bottom: 10px;"><span class="tf-icons bx bx-plus me-1"></span> Add New</a>
    </div>

    <hr class="m-0">
    <div class="table-responsive text-nowrap p-2">
        <table class="table table-striped table-hover dataTableList">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Premium</th>
                    <th>Deleted</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php if (isset($categories) && count($categories) > 0): ?>
                    <?php foreach ($categories as $key => $catalog): ?>
                        <tr>
                            <td><?= $key + 1; ?></td>
                            <td><?= $catalog->name; ?></td>
                            <td><?= $catalog->status; ?></td>
                            <td><?= date('Y-m-d', strtotime($catalog->updated_at)); ?></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" style="overflow: visible !important">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <?php if ($catalog->status == 1) { ?>
                                            <a class="dropdown-item" href="<?php echo url('admin/journal_catalog/deactivate/' . $catalog->id); ?>"><i class="bx bx-x me-1"></i> Deactivate</a>
                                        <?php } else if ($catalog->status == 0) { ?>
                                            <a class="dropdown-item" href="<?php echo url('admin/journal_catalog/activate/' . $catalog->id); ?>"><i class="bx bx-check me-1"></i> Activate</a>
                                        <?php } ?>
                                        <a class="dropdown-item" href="<?php echo url('admin/journal_catalog/edit/' . $catalog->id); ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                        <a class="dropdown-item" href="<?php echo url('admin/journal_catalog/delete/' . $catalog->id); ?>" onClick="return confirm('Are you sure you want to delete this record?');"><i class="bx bx-trash me-1"></i> Delete</a>
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
@endsection