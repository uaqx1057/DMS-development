<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ Vite::asset('resources/assets/images/favicon.ico') }}">

    <!-- APP Assets Bundles -->
    @vite(['resources/assets/css/theme.css'])
 <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="{{ asset('css/custom-style.css') }}" rel="stylesheet" />
    
    <title>{{ $title ?? env('APP_NAME') }}</title>
    @livewireStyles
</head>

<body>
    <div id="layout-wrapper">
        <x-layouts.navbar/>
        <x-layouts.sidebar/>
        <div class="main-content">
            {{-- Toast --}}
            <div class="page-content">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>
        </div>
        <x-layouts.footer/>
    </div>
    @livewireScripts
    @stack('scripts')
</body>


</html>
