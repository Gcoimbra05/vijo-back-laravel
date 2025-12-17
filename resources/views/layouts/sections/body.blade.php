<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.sections.menu.sidebar')

            <div class="layout-page">
                @include('layouts.sections.menu.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @if(isset($breadcrumbs) && !empty($breadcrumbs))
                            <x-breadcrumbs :items="$breadcrumbs" />
                        @endif

                        @yield('content')
                    </div>


                    @include('layouts.footer')

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    @include('layouts.sections.scripts')
    @yield('scripts')
</body>
