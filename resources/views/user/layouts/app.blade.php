<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @include('user.layouts.partials.loader')

    <link href="{{ asset('style.css') }}" rel="stylesheet">
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
        @include('user.layouts.partials.sidebar')

        <div class="page-container">
            @include('user.layouts.partials.navbar')
            <main class="main-content bgc-grey-100">
                <div id="mainContent">
                    {{ $slot }}
                </div>
            </main>
            @include('user.layouts.partials.footer')
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('vendor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bundle.js') }}"></script>
</body>

</html>
