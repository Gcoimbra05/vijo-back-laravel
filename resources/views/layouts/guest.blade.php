<!doctype html>
<html
    lang="en"
    class="layout-wide customizer-hide"
    dir="ltr" data-skin="default" data-bs-theme="light"
    data-template="vertical-menu-template"
>

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

    <link rel="stylesheet" href="{{ asset('dist/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/css/pages/page-auth.css') }}" />
</head>

<body>
    <div class="position-relative">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6 mx-4">

                <div class="card p-sm-7 p-2">
                    <div class="app-brand justify-content-center mt-5">
                        <a href="{{ url('admin') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img src="{{ asset('assets/images/group.svg') }}" style="width: 100px;" />
                            </span>
                        </a>
                    </div>

                    <div class="card-body mt-1">
                        @yield('content')
                        @yield('scripts')
                    </div>
                </div>

                <img src="{{ asset('admin/assets/img/illustrations/tree-3.png') }}" alt="auth-tree" class="authentication-image-object-left d-none d-lg-block" />
                <img src="{{ asset('admin/assets/img/illustrations/auth-basic-mask-light.png') }}" class="authentication-image d-none d-lg-block scaleX-n1-rtl" height="172" alt="triangle-bg" data-app-light-img="illustrations/auth-basic-mask-light.png" data-app-dark-img="illustrations/auth-basic-mask-dark.png" />
                <img src="{{ asset('admin/assets/img/illustrations/tree.png') }}" alt="auth-tree" class="authentication-image-object-right d-none d-lg-block" />
            </div>
        </div>
    </div>
</body>
</html>
