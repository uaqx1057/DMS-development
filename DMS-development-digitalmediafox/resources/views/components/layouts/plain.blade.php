<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- App Title -->
    <title>{{ $title ?? env('APP_NAME') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ Vite::asset('resources/assets/images/favicon.ico') }}">

    <!-- Theme CSS -->
    @vite(['resources/assets/css/theme.css'])

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Custom CSS -->
    <link href="{{ asset('css/custom-style.css') }}?v={{ filemtime(public_path('css/custom-style.css')) }}" rel="stylesheet" />

    @livewireStyles

    <style>
        /* Hide loader by default, show with Livewire */
        #livewire-loader {
            display: none;
        }
        [wire\\:loading] #livewire-loader {
            display: flex !important;
        }
    </style>
</head>
<body>

    <!-- Livewire Page Loader -->
    <div
        id="livewire-loader"
        class="position-fixed top-0 start-0 w-100 h-100 bg-white d-flex justify-content-center align-items-center"
        style="z-index: 9999;">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Main Page Content -->
    <div wire:loading.delay>
        {{-- This empty div enables wire:loading to trigger --}}
    </div>

    {{ $slot }}

 

    @livewireScripts
    @stack('scripts')
       <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Fallback: hide loader after 10s if Livewire fails -->
    <script>
        setTimeout(() => {
            document.getElementById('livewire-loader')?.remove();
        }, 100);
    </script>
</body>
</html>
