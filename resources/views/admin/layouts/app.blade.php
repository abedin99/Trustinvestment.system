<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meta_title }}</title>

    @include('admin.layouts.partials.loader')

    <!-- stylesheet -->
    <link href="{{ asset('style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/fontawesome/css/all.min.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="app">
    <div id="loader">
        <div class="spinner"></div>
    </div>
    <script>
        window.addEventListener('load', function load() {
            const loader = document.getElementById('loader');
            setTimeout(function() {
                loader.classList.add('fadeOut');
            }, 300);
        });
    </script>
    <div>
        @include('admin.layouts.partials.sidebar')

        <div class="page-container">
            @include('admin.layouts.partials.navbar')
            <main class="main-content bgc-grey-100">
                <div id="mainContent">
                    {{ $slot }}
                </div>
            </main>
            @include('admin.layouts.partials.footer')
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('vendor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bundle.js') }}"></script>

    @include('sweetalert::alert')
</body>

</html>
