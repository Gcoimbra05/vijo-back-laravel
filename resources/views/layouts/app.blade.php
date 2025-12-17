<!doctype html>
<html
    lang="en"
    class=" layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="ltr" data-skin="bordered" data-bs-theme="light"
    data-assets-path="../../assets/"
    data-template="vertical-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel - vijo')</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/group.svg') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('dist/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/libs/node-waves/node-waves.css') }}" />
    <script src="{{ asset('dist/libs/@algolia/autocomplete-js.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('dist/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/libs/apex-charts/apex-charts.css') }}" />

    <script src="{{ asset('dist/js/helpers.js') }}"></script>
    <script src="{{ asset('dist/js/template-customizer.js') }}"></script>
    <script src="{{ asset('dist/js/config.js') }}"></script>
</head>

@include('layouts.sections.body')

</html>
