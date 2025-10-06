<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- App Title -->
    <title>{{ $title ?? env('APP_NAME') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('public/favicon.ico') }}">

    <!-- Theme CSS (Vite handles defer + minify in production) -->
    @vite(['resources/assets/css/theme.css'])

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="{{ asset('css/custom-style.css') }}" rel="stylesheet" />

    @livewireStyles
</head>

<body>
    <div id="layout-wrapper">
        <x-layouts.navbar />
        <x-layouts.sidebar />
        <x-layouts.mobile-sidebar />


        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <x-layouts.footer />
    </div>

    <!-- JS Scripts (Optimized Load Order) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    @livewireScripts
<script>

document.addEventListener("DOMContentLoaded", () => {
    const mobileSidebar = document.getElementById("mobile-sidebar");
    const overlay = document.getElementById("mobile-sidebar-overlay");
    const toggleBtn = document.getElementById("sidebarToggle");

    toggleBtn.addEventListener("click", () => {
        if (window.innerWidth >= 350 && window.innerWidth <= 1000) {
            mobileSidebar.classList.toggle("active");
            overlay.classList.toggle("active");
            
        }
    });

    overlay.addEventListener("click", () => {
        mobileSidebar.classList.remove("active");
        overlay.classList.remove("active");
    });

    // Reload page when menu clicked
    document.querySelectorAll("#mobile-sidebar a").forEach(link => {
        link.addEventListener("click", () => {
            window.location.href = link.href;
        });
    });
});


function initUserDropdown() {
        const btn = document.getElementById('userDropdownBtn');
        const menu = document.getElementById('userDropdownMenu');

        if (!btn || !menu) return;

        // Toggle menu on button click
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('show');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function () {
            menu.classList.remove('show');
        });
    }

    // Run on page load
    initUserDropdown();
</script>



    @stack('scripts')
</body>
</html>
