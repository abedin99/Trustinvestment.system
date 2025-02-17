<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @include('demo.template.partials.loader')

    <link href="{{ asset('style.css') }}" rel="stylesheet">
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
        @include('demo.template.partials.sidebar')

        <div class="page-container">
            @include('demo.template.partials.navbar')
            <main class="main-content bgc-grey-100">
                <div id="mainContent">
                    @yield('content')
                </div>
            </main>
            @include('demo.template.partials.footer')
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('vendor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('bundle.js') }}"></script>
</body>

</html>