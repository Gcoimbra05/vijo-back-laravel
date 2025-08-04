@include('layouts.header')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.sidebar')

            @include('layouts.messages')

            <div class="layout-page">
                @include('layouts.navbar')

                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        @if(isset($breadcrumbs) && !empty($breadcrumbs))
                            <x-breadcrumbs :items="$breadcrumbs" />
                        @endif

                        @yield('content')
                    </div>

                    @include('layouts.footer')
                </div>
            </div>
        </div>
    </div>
    @include('layouts.scripts')

    @yield('scripts')
</body>

</html>
