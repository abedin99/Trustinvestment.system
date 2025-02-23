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

    @isset($header_components)
        {{ $header_components }}
    @endisset
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @isset($css)
        <!-- Custom style -->
        {{ $css }}
    @endisset
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    @if (Route::has('admin.migrate.fresh'))
        <script>
            $(document).ready(function() {
                $('.reset-system').on('click', function(event) {
                    event.preventDefault(); // Prevent the default navigation

                    let resetUrl = $(this).attr('href'); // Get the URL to navigate to

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will reset the system and cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, reset it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = resetUrl; // Redirect if confirmed
                        }
                    });
                });
            });
        </script>
    @endif

    @isset($footer_components)
        <!-- Optional JS -->
        {{ $footer_components }}
    @endisset

    @include('sweetalert::alert')

    @isset($js)
        <!-- Custom js -->
        {{ $js }}
    @endisset
</body>

</html>
