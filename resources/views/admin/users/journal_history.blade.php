@extends('layouts.app')
@section('title', 'Journal History')

@section('content')
<x-admin.datatable
    title="Journal History"
    :columns="[
        'Sr. No.',
        'Metrics',
        'Emotions Data',
        'Created At',
        'Actions'
    ]"
    :fields="[
        fn($log, $i) => $i + 1,
        'metrics',
        'emotions_data',
        'created_at',
        'actions'
    ]"
    :rows="$journalHistory"
/>
@endsection
