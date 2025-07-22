@php
    use App\Models\User;


@endphp

@extends('layouts.app')
@section('title', 'Users List')

@section('content')
<x-admin.datatable
    :title="'Users List'"
    :columns="['Sr. No.', 'Name', 'Email', 'Mobile', 'Status']"
    :fields="[
        'id',
        fn($user) => $user->first_name . ' ' . $user->last_name,
        'email',
        'mobile',
        'status',
    ]"
    :rows="$users"
    :actions="[

        [
            'label' => 'Journal History',
            'url' => fn($user) => route('users.journalHistory', $user->id),
            'icon' => 'bx bx-history me-1',
        ],
        [
            'label' => 'Audit Logs',
            'url' => fn($user) => route('users.auditLogs', $user->id),
            'icon' => 'bx bxl-blogger me-1',
        ],
        [
            'label' => 'Delete',
            'url' => fn($user) => route('users.destroy', $user->id),
            'icon' => 'bx bx-trash me-1',
            'confirm' => 'Are you sure you want to delete this record?',
        ]
    ]"
/>

@endsection
