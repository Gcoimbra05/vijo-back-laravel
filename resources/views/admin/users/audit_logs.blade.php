@extends('layouts.app')
@section('title', 'Audit Logs')

@section('content')
<x-admin.datatable
    title="Audit Logs"
    :columns="[
        'Sr. No.',
        'User',
        'Role',
        'Action',
        'Who',
        'IP Address',
        'Access From',
        'Browser',
        'Browser Version',
        'Platform',
        'Local Date'
    ]"
    :fields="[
        fn($log, $i) => $i + 1,
        'user_name',
        'role',
        'action_message',
        'ref_user_name',
        'ip_address',
        'access_from',
        'browser_name',
        'browser_version',
        'platform_name',
        'create_date_w_tz'
    ]"
    :rows="$auditLogs"
/>
@endsection
